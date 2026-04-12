<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TaxiDriver;
use App\Models\TaxiRide;
use App\Models\TaxiZoneRate;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TaxiAdminController extends Controller
{
    // ---------------------------------------------------------------
    // Tarifas por zona
    // ---------------------------------------------------------------

    public function rates(Request $request)
    {
        $zoneId = $request->zone_id;
        $rates  = TaxiZoneRate::with('zone')
            ->when($zoneId, fn ($q) => $q->where('zone_id', $zoneId))
            ->orderBy('zone_id')
            ->paginate(config('default_pagination'));

        $zones = Zone::orderBy('name')->get();

        return view('admin-views.zarpya-taxi.rates.index', compact('rates', 'zones', 'zoneId'));
    }

    public function storeRate(Request $request)
    {
        $request->validate([
            'zone_id'          => 'required|integer|exists:zones,id',
            'vehicle_type'     => 'required|string|in:standard,premium,moto',
            'base_fare'        => 'required|numeric|min:0',
            'fare_per_km'      => 'required|numeric|min:0',
            'fare_per_min'     => 'required|numeric|min:0',
            'min_fare'         => 'required|numeric|min:0',
            'platform_percent' => 'required|numeric|min:0|max:100',
        ]);

        TaxiZoneRate::updateOrCreate(
            ['zone_id' => $request->zone_id, 'vehicle_type' => $request->vehicle_type],
            $request->only(['base_fare', 'fare_per_km', 'fare_per_min', 'min_fare', 'platform_percent']) + ['status' => true]
        );

        Toastr::success('Tarifa de taxi guardada.');
        return back();
    }

    public function updateRate(Request $request, $id)
    {
        $rate = TaxiZoneRate::findOrFail($id);
        $request->validate([
            'base_fare'        => 'required|numeric|min:0',
            'fare_per_km'      => 'required|numeric|min:0',
            'fare_per_min'     => 'required|numeric|min:0',
            'min_fare'         => 'required|numeric|min:0',
            'platform_percent' => 'required|numeric|min:0|max:100',
        ]);
        $rate->update($request->only(['base_fare', 'fare_per_km', 'fare_per_min', 'min_fare', 'platform_percent']));
        Toastr::success('Tarifa actualizada.');
        return back();
    }

    public function rateStatus(Request $request)
    {
        TaxiZoneRate::findOrFail($request->id)->update(['status' => $request->status]);
        Toastr::success('Estado actualizado.');
        return back();
    }

    public function destroyRate($id)
    {
        TaxiZoneRate::findOrFail($id)->delete();
        Toastr::success('Tarifa eliminada.');
        return back();
    }

    // ---------------------------------------------------------------
    // Viajes
    // ---------------------------------------------------------------

    public function rides(Request $request)
    {
        $status = $request->status;
        $search = $request->search;

        $rides = TaxiRide::with(['customer', 'driver', 'zone'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where('id', $search))
            ->latest()
            ->paginate(config('default_pagination'));

        $stats = [
            'total'     => TaxiRide::count(),
            'completed' => TaxiRide::where('status', 'completed')->count(),
            'active'    => TaxiRide::whereNotIn('status', ['completed', 'cancelled'])->count(),
            'cancelled' => TaxiRide::where('status', 'cancelled')->count(),
            'revenue'   => TaxiRide::where('status', 'completed')->sum('platform_earning'),
        ];

        return view('admin-views.zarpya-taxi.rides.index', compact('rides', 'stats', 'status', 'search'));
    }

    public function showRide($id)
    {
        $ride = TaxiRide::with(['customer', 'driver', 'zone'])->findOrFail($id);
        return view('admin-views.zarpya-taxi.rides.show', compact('ride'));
    }

    // ---------------------------------------------------------------
    // Conductores de Taxi
    // ---------------------------------------------------------------

    public function drivers(Request $request)
    {
        $search = $request->search;
        $status = $request->status;

        $drivers = TaxiDriver::with('zone')
            ->when($search, fn ($q) => $q->where('phone', 'like', "%$search%")
                ->orWhere('f_name', 'like', "%$search%")
                ->orWhere('l_name', 'like', "%$search%"))
            ->when($status !== null, fn ($q) => $q->where('application_status', $status))
            ->latest()
            ->paginate(config('default_pagination'));

        $zones = Zone::orderBy('name')->get();

        return view('admin-views.zarpya-taxi.drivers.index', compact('drivers', 'zones', 'search', 'status'));
    }

    public function showDriver($id)
    {
        $driver = TaxiDriver::with(['zone', 'rides'])->findOrFail($id);
        return view('admin-views.zarpya-taxi.drivers.show', compact('driver'));
    }

    public function storeDriver(Request $request)
    {
        $request->validate([
            'f_name'       => 'required|string|max:100',
            'l_name'       => 'nullable|string|max:100',
            'phone'        => 'required|string|unique:taxi_drivers,phone',
            'email'        => 'nullable|email|unique:taxi_drivers,email',
            'password'     => 'required|string|min:6',
            'zone_id'      => 'required|integer|exists:zones,id',
            'vehicle_type' => 'required|in:standard,premium,moto',
            'license_plate'=> 'nullable|string|max:20',
        ]);

        TaxiDriver::create([
            'f_name'            => $request->f_name,
            'l_name'            => $request->l_name,
            'phone'             => $request->phone,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'zone_id'           => $request->zone_id,
            'vehicle_type'      => $request->vehicle_type,
            'license_plate'     => $request->license_plate,
            'application_status'=> 'approved',
            'active'            => 1,
        ]);

        Toastr::success('Conductor de taxi creado.');
        return back();
    }

    public function driverStatus(Request $request)
    {
        $driver = TaxiDriver::findOrFail($request->id);
        $driver->update(['application_status' => $request->status]);
        Toastr::success('Estado del conductor actualizado.');
        return back();
    }

    public function destroyDriver($id)
    {
        TaxiDriver::findOrFail($id)->delete();
        Toastr::success('Conductor eliminado.');
        return back();
    }
}
