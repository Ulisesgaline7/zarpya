<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TaxiDriver;
use App\Models\TaxiRide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TaxiDriverController extends Controller
{
    /**
     * POST /api/v1/taxi/driver/login
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $driver = TaxiDriver::where('phone', $request->phone)->first();

        if (! $driver || ! Hash::check($request->password, $driver->password)) {
            return response()->json([
                'errors' => [['code' => 'auth-001', 'message' => 'Credenciales incorrectas.']],
            ], 401);
        }

        if ($driver->application_status !== 'approved') {
            return response()->json([
                'errors' => [['code' => 'auth-003', 'message' => 'Tu cuenta aún no ha sido aprobada.']],
            ], 401);
        }

        if (! $driver->status) {
            return response()->json([
                'errors' => [['code' => 'auth-003', 'message' => 'Tu cuenta ha sido suspendida.']],
            ], 401);
        }

        $token = Str::random(120);
        $driver->auth_token = $token;
        $driver->save();

        return response()->json([
            'token'  => $token,
            'driver' => [
                'id'            => $driver->id,
                'name'          => $driver->full_name,
                'phone'         => $driver->phone,
                'vehicle_type'  => $driver->vehicle_type,
                'license_plate' => $driver->license_plate,
                'available'     => (bool) $driver->available,
                'zone_id'       => $driver->zone_id,
            ],
        ], 200);
    }

    /**
     * GET /api/v1/taxi/driver/profile
     */
    public function profile(Request $request): JsonResponse
    {
        $driver = $this->authDriver($request);
        if (! $driver) return $this->unauthorized();

        return response()->json(['status' => true, 'data' => $driver]);
    }

    /**
     * POST /api/v1/taxi/driver/toggle-available
     */
    public function toggleAvailable(Request $request): JsonResponse
    {
        $driver = $this->authDriver($request);
        if (! $driver) return $this->unauthorized();

        $driver->update(['available' => $driver->available ? 0 : 1]);

        return response()->json([
            'status'    => true,
            'available' => (bool) $driver->available,
        ]);
    }

    /**
     * POST /api/v1/taxi/driver/location
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $driver = $this->authDriver($request);
        if (! $driver) return $this->unauthorized();

        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
        ]);

        $driver->update([
            'current_lat' => $request->lat,
            'current_lng' => $request->lng,
        ]);

        return response()->json(['status' => true]);
    }

    /**
     * GET /api/v1/taxi/driver/pending-rides
     * Viajes en estado "searching" en la zona del conductor.
     */
    public function pendingRides(Request $request): JsonResponse
    {
        $driver = $this->authDriver($request);
        if (! $driver) return $this->unauthorized();

        $rides = TaxiRide::where('status', 'searching')
            ->where('zone_id', $driver->zone_id)
            ->where('vehicle_type', $driver->vehicle_type)
            ->with('customer:id,f_name,l_name,phone')
            ->latest()
            ->get();

        return response()->json(['status' => true, 'data' => $rides]);
    }

    /**
     * POST /api/v1/taxi/driver/ride/{id}/accept
     */
    public function acceptRide(Request $request, int $id): JsonResponse
    {
        $driver = $this->authDriver($request);
        if (! $driver) return $this->unauthorized();

        $hasActive = TaxiRide::where('driver_id', $driver->id)
            ->whereIn('status', ['accepted', 'arriving', 'in_progress'])
            ->exists();

        if ($hasActive) {
            return response()->json([
                'status'  => false,
                'message' => 'Ya tienes un viaje activo.',
            ], 409);
        }

        $ride = TaxiRide::where('status', 'searching')
            ->where('zone_id', $driver->zone_id)
            ->findOrFail($id);

        $ride->update([
            'driver_id'   => $driver->id,
            'status'      => 'accepted',
            'accepted_at' => now(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Viaje aceptado.',
            'data'    => $ride->fresh(['customer:id,f_name,l_name,phone']),
        ]);
    }

    /**
     * POST /api/v1/taxi/driver/ride/{id}/status
     * Actualiza el estado: arriving → in_progress → completed
     */
    public function updateRideStatus(Request $request, int $id): JsonResponse
    {
        $driver = $this->authDriver($request);
        if (! $driver) return $this->unauthorized();

        $request->validate([
            'status' => 'required|in:arriving,in_progress,completed',
        ]);

        $ride = TaxiRide::where('driver_id', $driver->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->findOrFail($id);

        $timestamps = [];
        if ($request->status === 'in_progress') {
            $timestamps['started_at'] = now();
        } elseif ($request->status === 'completed') {
            $timestamps['completed_at'] = now();
        }

        $ride->update(array_merge(['status' => $request->status], $timestamps));

        return response()->json([
            'status'  => true,
            'message' => 'Estado actualizado.',
            'data'    => $ride,
        ]);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    private function authDriver(Request $request): ?TaxiDriver
    {
        $token = $request->bearerToken();
        if (! $token) return null;

        return TaxiDriver::where('auth_token', $token)
            ->where('active', 1)
            ->where('application_status', 'approved')
            ->first();
    }

    private function unauthorized(): JsonResponse
    {
        return response()->json([
            'errors' => [['code' => 'auth-001', 'message' => 'No autorizado.']],
        ], 401);
    }
}
