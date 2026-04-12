<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RentalBooking;
use App\Models\RentalVehicle;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class RentalAdminController extends Controller
{
    // ---------------------------------------------------------------
    // Flota de vehiculos
    // ---------------------------------------------------------------

    public function vehicles(Request $request)
    {
        $search = $request->search;
        $vehicles = RentalVehicle::with('zone')
            ->when($search, fn ($q) => $q->where('brand', 'like', "%{$search}%")
                ->orWhere('model', 'like', "%{$search}%")
                ->orWhere('plate', 'like', "%{$search}%"))
            ->latest()
            ->paginate(config('default_pagination'));

        $zones = Zone::orderBy('name')->get();
        $stats = [
            'available'   => RentalVehicle::where('status', 'available')->count(),
            'rented'      => RentalVehicle::where('status', 'rented')->count(),
            'maintenance' => RentalVehicle::where('status', 'maintenance')->count(),
        ];

        return view('admin-views.zarpya-rental.vehicles.index', compact('vehicles', 'zones', 'stats', 'search'));
    }

    public function storeVehicle(Request $request)
    {
        $request->validate([
            'type'             => 'required|string|in:car,moto,pickup,van',
            'brand'            => 'nullable|string|max:80',
            'model'            => 'nullable|string|max:80',
            'plate'            => 'nullable|string|max:20',
            'color'            => 'nullable|string|max:40',
            'zone_id'          => 'nullable|integer|exists:zones,id',
            'price_per_hour'   => 'required|numeric|min:0',
            'price_per_day'    => 'required|numeric|min:0',
            'deposit'          => 'required|numeric|min:0',
            'seats'            => 'required|integer|min:1',
            'owner_percent'    => 'required|numeric|min:0|max:100',
            'platform_percent' => 'required|numeric|min:0|max:100',
        ]);

        RentalVehicle::create($request->only([
            'type', 'brand', 'model', 'plate', 'color', 'zone_id',
            'price_per_hour', 'price_per_day', 'deposit', 'seats',
            'with_driver', 'owner_percent', 'platform_percent',
        ]) + ['status' => 'available']);

        Toastr::success('Vehículo agregado a la flota.');
        return back();
    }

    public function editVehicle($id)
    {
        $vehicle = RentalVehicle::findOrFail($id);
        $zones   = Zone::orderBy('name')->get();
        return view('admin-views.zarpya-rental.vehicles.edit', compact('vehicle', 'zones'));
    }

    public function updateVehicle(Request $request, $id)
    {
        $vehicle = RentalVehicle::findOrFail($id);
        $request->validate([
            'price_per_hour'   => 'required|numeric|min:0',
            'price_per_day'    => 'required|numeric|min:0',
            'deposit'          => 'required|numeric|min:0',
            'owner_percent'    => 'required|numeric|min:0|max:100',
            'platform_percent' => 'required|numeric|min:0|max:100',
            'status'           => 'required|in:available,rented,maintenance,inactive',
        ]);
        $vehicle->update($request->only([
            'brand', 'model', 'plate', 'color', 'zone_id',
            'price_per_hour', 'price_per_day', 'deposit', 'seats',
            'with_driver', 'owner_percent', 'platform_percent', 'status',
        ]));
        Toastr::success('Vehículo actualizado.');
        return back();
    }

    public function destroyVehicle($id)
    {
        RentalVehicle::findOrFail($id)->delete();
        Toastr::success('Vehículo eliminado.');
        return back();
    }

    // ---------------------------------------------------------------
    // Reservas
    // ---------------------------------------------------------------

    public function bookings(Request $request)
    {
        $status = $request->status;

        $bookings = RentalBooking::with(['vehicle', 'customer'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(config('default_pagination'));

        $stats = [
            'total'     => RentalBooking::count(),
            'pending'   => RentalBooking::where('status', 'pending')->count(),
            'active'    => RentalBooking::where('status', 'active')->count(),
            'completed' => RentalBooking::where('status', 'completed')->count(),
            'revenue'   => RentalBooking::where('status', 'completed')->sum('total_price'),
        ];

        return view('admin-views.zarpya-rental.bookings.index', compact('bookings', 'stats', 'status'));
    }

    public function updateBookingStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,active,completed,cancelled']);
        $booking = RentalBooking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        // Si se confirma, marcar vehiculo como rentado
        if ($request->status === 'active') {
            $booking->vehicle->update(['status' => 'rented']);
        } elseif (in_array($request->status, ['completed', 'cancelled'])) {
            $booking->vehicle->update(['status' => 'available']);
        }

        Toastr::success('Estado de reserva actualizado.');
        return back();
    }
}
