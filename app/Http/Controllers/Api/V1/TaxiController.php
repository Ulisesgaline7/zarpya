<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\DeliveryPricingService;
use App\CentralLogics\WhatsappNotificationService;
use App\Http\Controllers\Controller;
use App\Models\TaxiRide;
use App\Models\TaxiZoneRate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class TaxiController extends Controller
{
    /**
     * POST /api/v1/taxi/estimate
     * Devuelve estimacion de tarifa antes de solicitar el viaje.
     */
    public function estimate(Request $request): JsonResponse
    {
        $request->validate([
            'zone_id'       => 'required|integer|exists:zones,id',
            'vehicle_type'  => 'nullable|string|in:standard,premium,moto',
            'distance_km'   => 'required|numeric|min:0.1|max:100',
        ]);

        $vehicleType = $request->vehicle_type ?? 'standard';

        $rate = TaxiZoneRate::where('zone_id', $request->zone_id)
            ->where('vehicle_type', $vehicleType)
            ->where('status', true)
            ->first();

        if (! $rate) {
            return response()->json([
                'status'  => false,
                'message' => 'No hay tarifas configuradas para esta zona.',
            ], 404);
        }

        [$multiplier, $activeRules] = DeliveryPricingService::computeMultiplier($request->zone_id);

        $distanceFare = $request->distance_km * $rate->fare_per_km;
        $rawFare      = max($rate->base_fare + $distanceFare, $rate->min_fare);
        $finalFare    = round($rawFare * $multiplier, 2);

        return response()->json([
            'status' => true,
            'data'   => [
                'vehicle_type'   => $vehicleType,
                'base_fare'      => $rate->base_fare,
                'distance_fare'  => round($distanceFare, 2),
                'multiplier'     => $multiplier,
                'active_rules'   => $activeRules,
                'estimated_fare' => $finalFare,
                'currency'       => 'HNL',
            ],
        ]);
    }

    /**
     * POST /api/v1/taxi/request
     * Solicita un viaje.
     */
    public function requestRide(Request $request): JsonResponse
    {
        $request->validate([
            'zone_id'          => 'required|integer|exists:zones,id',
            'vehicle_type'     => 'nullable|string|in:standard,premium,moto',
            'pickup_address'   => 'required|string|max:255',
            'pickup_lat'       => 'required|numeric',
            'pickup_lng'       => 'required|numeric',
            'dropoff_address'  => 'required|string|max:255',
            'dropoff_lat'      => 'required|numeric',
            'dropoff_lng'      => 'required|numeric',
            'distance_km'      => 'required|numeric|min:0.1',
            'payment_method'   => 'required|string|in:cash,bac,ficohsa,tigo_money',
        ]);

        $vehicleType = $request->vehicle_type ?? 'standard';

        $rate = TaxiZoneRate::where('zone_id', $request->zone_id)
            ->where('vehicle_type', $vehicleType)
            ->where('status', true)
            ->firstOrFail();

        [$multiplier, $activeRules] = DeliveryPricingService::computeMultiplier($request->zone_id);

        $distanceFare    = $request->distance_km * $rate->fare_per_km;
        $rawFare         = max($rate->base_fare + $distanceFare, $rate->min_fare);
        $finalFare       = round($rawFare * $multiplier, 2);
        $platformEarning = round($finalFare * $rate->platform_percent / 100, 2);
        $driverEarning   = round($finalFare - $platformEarning, 2);

        $ride = TaxiRide::create([
            'customer_id'        => Auth::id(),
            'zone_id'            => $request->zone_id,
            'vehicle_type'       => $vehicleType,
            'pickup_address'     => $request->pickup_address,
            'pickup_lat'         => $request->pickup_lat,
            'pickup_lng'         => $request->pickup_lng,
            'dropoff_address'    => $request->dropoff_address,
            'dropoff_lat'        => $request->dropoff_lat,
            'dropoff_lng'        => $request->dropoff_lng,
            'distance_km'        => $request->distance_km,
            'base_fare'          => $rate->base_fare,
            'distance_fare'      => round($distanceFare, 2),
            'dynamic_multiplier' => $multiplier,
            'total_fare'         => $finalFare,
            'driver_earning'     => $driverEarning,
            'platform_earning'   => $platformEarning,
            'status'             => 'searching',
            'payment_method'     => $request->payment_method,
        ]);

        // TODO: broadcast a conductores disponibles en la zona via Socket.io

        return response()->json([
            'status'  => true,
            'message' => 'Buscando conductor disponible...',
            'data'    => $ride,
        ], 201);
    }

    /**
     * GET /api/v1/taxi/ride/{id}
     * Estado actual del viaje.
     */
    public function show(int $id): JsonResponse
    {
        $ride = TaxiRide::where('customer_id', Auth::id())
            ->with('driver')
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data'   => $ride,
        ]);
    }

    /**
     * POST /api/v1/taxi/ride/{id}/cancel
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $ride = TaxiRide::where('customer_id', Auth::id())
            ->whereIn('status', ['searching', 'accepted'])
            ->findOrFail($id);

        $ride->update([
            'status'              => 'cancelled',
            'cancellation_reason' => $request->reason ?? 'Cancelado por el cliente',
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Viaje cancelado.',
        ]);
    }
}
