<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\WhatsappNotificationService;
use App\Http\Controllers\Controller;
use App\Models\RentalBooking;
use App\Models\RentalVehicle;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RentalController extends Controller
{
    /**
     * GET /api/v1/rental/vehicles
     * Lista vehiculos disponibles en la zona del cliente.
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'zone_id' => 'nullable|integer|exists:zones,id',
            'type'    => 'nullable|string|in:car,moto,pickup,van',
        ]);

        $vehicles = RentalVehicle::available()
            ->when($request->zone_id, fn ($q) => $q->where('zone_id', $request->zone_id))
            ->when($request->type,    fn ($q) => $q->where('type', $request->type))
            ->with('zone')
            ->paginate(20);

        return response()->json([
            'status' => true,
            'data'   => $vehicles,
        ]);
    }

    /**
     * POST /api/v1/rental/book
     * Crea una reserva.
     */
    public function book(Request $request): JsonResponse
    {
        $request->validate([
            'vehicle_id'      => 'required|integer|exists:rental_vehicles,id',
            'start_at'        => 'required|date|after:now',
            'end_at'          => 'required|date|after:start_at',
            'pickup_address'  => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'payment_method'  => 'required|string|in:cash,bac,ficohsa,tigo_money',
        ]);

        $vehicle = RentalVehicle::available()->findOrFail($request->vehicle_id);

        // Verificar disponibilidad
        $conflict = RentalBooking::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['confirmed', 'active'])
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_at', [$request->start_at, $request->end_at])
                  ->orWhereBetween('end_at', [$request->start_at, $request->end_at]);
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'status'  => false,
                'message' => 'El vehiculo no está disponible en ese rango de fechas.',
            ], 409);
        }

        // Calcular precio
        $start   = \Carbon\Carbon::parse($request->start_at);
        $end     = \Carbon\Carbon::parse($request->end_at);
        $hours   = $start->diffInHours($end);
        $days    = $start->diffInDays($end);

        $totalPrice = $days >= 1
            ? $days * $vehicle->price_per_day
            : $hours * $vehicle->price_per_hour;

        $booking = RentalBooking::create([
            'vehicle_id'      => $vehicle->id,
            'customer_id'     => Auth::id(),
            'start_at'        => $request->start_at,
            'end_at'          => $request->end_at,
            'total_price'     => $totalPrice,
            'pickup_address'  => $request->pickup_address,
            'dropoff_address' => $request->dropoff_address,
            'status'          => 'pending',
            'payment_method'  => $request->payment_method,
        ]);

        // Notificacion WhatsApp
        $customer = Auth::user();
        if ($customer?->phone) {
            WhatsappNotificationService::rentalConfirmed(
                phone:       $customer->phone,
                vehicleName: "{$vehicle->brand} {$vehicle->model}",
                startDate:   $start->format('d/m/Y H:i'),
                endDate:     $end->format('d/m/Y H:i'),
                total:       $totalPrice,
                bookingId:   $booking->id
            );
        }

        return response()->json([
            'status'  => true,
            'message' => 'Reserva creada exitosamente.',
            'data'    => $booking->load('vehicle'),
        ], 201);
    }

    /**
     * GET /api/v1/rental/my-bookings
     */
    public function myBookings(): JsonResponse
    {
        $bookings = RentalBooking::where('customer_id', Auth::id())
            ->with('vehicle')
            ->latest()
            ->paginate(15);

        return response()->json([
            'status' => true,
            'data'   => $bookings,
        ]);
    }
}
