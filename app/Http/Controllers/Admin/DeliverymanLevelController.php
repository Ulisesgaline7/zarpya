<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryMan;
use App\Models\DeliverymanLevel;
use App\Models\DmAchievement;
use App\Services\DmGamificationService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DeliverymanLevelController extends Controller
{
    public function index()
    {
        $levels       = DeliverymanLevel::withCount('deliverymen')->orderBy('sort_order')->get();
        $achievements = DmAchievement::where('status', true)->orderBy('id')->get();

        return view('admin-views.zarpya-pricing.deliveryman-levels.index', compact('levels', 'achievements'));
    }

    public function ranking(Request $request)
    {
        $topDrivers = DeliveryMan::active()
            ->with(['level', 'stat', 'rating', 'achievements', 'bonuses'])
            ->join('dm_stats', 'dm_stats.delivery_man_id', '=', 'delivery_men.id')
            ->orderByDesc('dm_stats.xp')
            ->select('delivery_men.*')
            ->limit(3)
            ->get();

        $allDrivers = DeliveryMan::active()
            ->with(['level', 'stat', 'rating', 'achievements', 'bonuses'])
            ->leftJoin('dm_stats', 'dm_stats.delivery_man_id', '=', 'delivery_men.id')
            ->orderByDesc('dm_stats.xp')
            ->select('delivery_men.*')
            ->paginate(20);

        return view('admin-views.zarpya-pricing.deliveryman-levels.ranking', compact('topDrivers', 'allDrivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'slug'              => 'required|string|max:40|unique:deliveryman_levels,slug',
            'name'              => 'required|string|max:80',
            'description'       => 'nullable|string',
            'driver_percent'    => 'required|numeric|min:50|max:100',
            'min_deliveries'    => 'required|integer|min:0',
            'min_rating'        => 'required|numeric|min:0|max:5',
            'min_months_active' => 'required|integer|min:0',
        ]);

        DeliverymanLevel::create([
            'slug'              => $request->slug,
            'name'              => $request->name,
            'description'       => $request->description,
            'driver_percent'    => $request->driver_percent,
            'min_deliveries'    => $request->min_deliveries,
            'min_rating'        => $request->min_rating,
            'min_months_active' => $request->min_months_active,
            'sort_order'        => DeliverymanLevel::max('sort_order') + 1,
            'status'            => true,
        ]);

        Toastr::success('Nivel de repartidor creado.');
        return back();
    }

    public function edit($id)
    {
        $level = DeliverymanLevel::findOrFail($id);
        return view('admin-views.zarpya-pricing.deliveryman-levels.edit', compact('level'));
    }

    public function update(Request $request, $id)
    {
        $level = DeliverymanLevel::findOrFail($id);

        $request->validate([
            'name'              => 'required|string|max:80',
            'description'       => 'nullable|string',
            'driver_percent'    => 'required|numeric|min:50|max:100',
            'min_deliveries'    => 'required|integer|min:0',
            'min_rating'        => 'required|numeric|min:0|max:5',
            'min_months_active' => 'required|integer|min:0',
        ]);

        $level->update($request->only([
            'name', 'description', 'driver_percent',
            'min_deliveries', 'min_rating', 'min_months_active',
        ]));

        Toastr::success('Nivel actualizado.');
        return back();
    }

    public function status(Request $request)
    {
        DeliverymanLevel::findOrFail($request->id)->update(['status' => $request->status]);
        Toastr::success('Estado actualizado.');
        return back();
    }
}
