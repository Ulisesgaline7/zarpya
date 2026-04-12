<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\DeliveryPricingService;
use App\Http\Controllers\Controller;
use App\Models\DynamicPricingRule;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class DynamicPricingRuleController extends Controller
{
    public function index()
    {
        $rules      = DynamicPricingRule::orderByDesc('priority')->get();
        $rainActive = DeliveryPricingService::isRainActive();

        return view('admin-views.zarpya-pricing.dynamic-rules.index', compact('rules', 'rainActive'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'rule_type'    => 'required|string|max:40|unique:dynamic_pricing_rules,rule_type',
            'label'        => 'required|string|max:80',
            'multiplier'   => 'required|numeric|min:1.0|max:3.0',
            'time_start'   => 'nullable|date_format:H:i',
            'time_end'     => 'nullable|date_format:H:i|after:time_start',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:0|max:6',
            'priority'     => 'required|integer|min:0',
        ]);

        DynamicPricingRule::create([
            'rule_type'    => $request->rule_type,
            'label'        => $request->label,
            'multiplier'   => $request->multiplier,
            'time_start'   => $request->time_start ? $request->time_start . ':00' : null,
            'time_end'     => $request->time_end ? $request->time_end . ':00' : null,
            'days_of_week' => $request->days_of_week,
            'priority'     => $request->priority,
            'status'       => true,
        ]);

        Toastr::success('Regla de precio dinámico creada.');
        return back();
    }

    public function update(Request $request, $id)
    {
        $rule = DynamicPricingRule::findOrFail($id);

        $request->validate([
            'label'        => 'required|string|max:80',
            'multiplier'   => 'required|numeric|min:1.0|max:3.0',
            'time_start'   => 'nullable|date_format:H:i',
            'time_end'     => 'nullable|date_format:H:i',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'integer|min:0|max:6',
            'priority'     => 'required|integer|min:0',
        ]);

        $rule->update([
            'label'        => $request->label,
            'multiplier'   => $request->multiplier,
            'time_start'   => $request->time_start ? $request->time_start . ':00' : null,
            'time_end'     => $request->time_end ? $request->time_end . ':00' : null,
            'days_of_week' => $request->days_of_week,
            'priority'     => $request->priority,
        ]);

        Toastr::success('Regla actualizada.');
        return back();
    }

    public function status(Request $request)
    {
        $rule = DynamicPricingRule::findOrFail($request->id);
        $rule->update(['status' => $request->status]);
        Toastr::success('Estado actualizado.');
        return back();
    }

    /** Activa o desactiva lluvia via Redis */
    public function toggleRain(Request $request)
    {
        $active = (bool) $request->active;
        DeliveryPricingService::setRain($active, ttlSeconds: 7200); // 2 horas

        Toastr::success($active ? 'Modo lluvia ACTIVADO (2 horas).' : 'Modo lluvia DESACTIVADO.');
        return back();
    }

    /** Verifica el clima ahora mismo via OpenWeatherMap (llamada AJAX) */
    public function checkWeatherNow()
    {
        try {
            \App\Jobs\CheckWeatherConditionJob::dispatchSync();
            $wasRaining = DeliveryPricingService::isRainActive();
            return response()->json([
                'success' => true,
                'message' => $wasRaining
                    ? '🌧️ Lluvia detectada — multiplicador ×1.4 activado'
                    : '☀️ Sin lluvia — multiplicador desactivado',
                'raining' => $wasRaining,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        DynamicPricingRule::findOrFail($id)->delete();
        Toastr::success('Regla eliminada.');
        return back();
    }
}
