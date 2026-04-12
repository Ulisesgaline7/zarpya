<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryCategoryPricing;
use App\Models\Module;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DeliveryCategoryPricingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;
        $pricings = DeliveryCategoryPricing::when($search, fn ($q) =>
            $q->where('category_name', 'like', "%{$search}%")
        )
        ->orderBy('category_name')
        ->paginate(config('default_pagination'));

        return view('admin-views.zarpya-pricing.category-pricing.index', compact('pricings', 'search'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_slug'      => 'required|string|max:80|unique:delivery_category_pricing,category_slug',
            'category_name'      => 'required|string|max:120',
            'base_price'         => 'required|numeric|min:0',
            'price_per_km'       => 'required|numeric|min:0',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'driver_percent'     => 'required|numeric|min:0|max:100',
            'platform_percent'   => 'required|numeric|min:0|max:100',
            'insurance_percent'  => 'required|numeric|min:0|max:100',
        ]);

        // Validate distribution adds up to 100
        $total = $request->driver_percent + $request->platform_percent + $request->insurance_percent;
        if (abs($total - 100) > 0.01) {
            Toastr::error('La distribución debe sumar 100%. Actual: ' . $total . '%');
            return back()->withInput();
        }

        DeliveryCategoryPricing::create($request->only([
            'category_slug', 'category_name', 'base_price', 'price_per_km',
            'commission_percent', 'driver_percent', 'platform_percent', 'insurance_percent',
        ]) + ['status' => true]);

        Toastr::success('Categoría de precio creada exitosamente.');
        return back();
    }

    public function edit($id)
    {
        $pricing = DeliveryCategoryPricing::findOrFail($id);
        return view('admin-views.zarpya-pricing.category-pricing.edit', compact('pricing'));
    }

    public function update(Request $request, $id)
    {
        $pricing = DeliveryCategoryPricing::findOrFail($id);

        $request->validate([
            'category_slug'      => 'required|string|max:80|unique:delivery_category_pricing,category_slug,' . $id,
            'category_name'      => 'required|string|max:120',
            'base_price'         => 'required|numeric|min:0',
            'price_per_km'       => 'required|numeric|min:0',
            'commission_percent' => 'required|numeric|min:0|max:100',
            'driver_percent'     => 'required|numeric|min:0|max:100',
            'platform_percent'   => 'required|numeric|min:0|max:100',
            'insurance_percent'  => 'required|numeric|min:0|max:100',
        ]);

        $total = $request->driver_percent + $request->platform_percent + $request->insurance_percent;
        if (abs($total - 100) > 0.01) {
            Toastr::error('La distribución debe sumar 100%. Actual: ' . $total . '%');
            return back()->withInput();
        }

        $pricing->update($request->only([
            'category_slug', 'category_name', 'base_price', 'price_per_km',
            'commission_percent', 'driver_percent', 'platform_percent', 'insurance_percent',
        ]));

        Toastr::success('Categoría de precio actualizada.');
        return back();
    }

    public function status(Request $request)
    {
        $pricing = DeliveryCategoryPricing::findOrFail($request->id);
        $pricing->update(['status' => $request->status]);
        Toastr::success('Estado actualizado.');
        return back();
    }

    public function destroy($id)
    {
        DeliveryCategoryPricing::findOrFail($id)->delete();
        Toastr::success('Categoría eliminada.');
        return back();
    }
}
