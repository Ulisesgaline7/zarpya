<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\DeliveryPricingService;
use App\Http\Controllers\Controller;
use App\Models\DeliveryCategoryPricing;
use App\Models\DynamicPricingRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryPricingController extends Controller
{
    /**
     * GET /api/v1/delivery/pricing/calculate
     *
     * Calcula el precio de envio en tiempo real.
     *
     * Params: category_slug, km, zone_id (opcional)
     *
     * Ejemplo respuesta:
     * {
     *   "raw_price": 57.00,
     *   "multiplier": 1.3,
     *   "active_rules": ["Hora pico (×1.3)"],
     *   "final_price": 74.10,
     *   "breakdown": { "driver": 65.21, "platform": 7.41, "insurance": 1.48 },
     *   "category": { "name": "Restaurantes / Comida", "base_price": 25, "price_per_km": 8 }
     * }
     */
    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'category_slug' => 'required|string|max:80',
            'km'            => 'required|numeric|min:0.1|max:50',
            'zone_id'       => 'nullable|integer|exists:zones,id',
        ]);

        $result = DeliveryPricingService::calculate(
            categorySlug: $request->category_slug,
            km:           (float) $request->km,
            zoneId:       $request->zone_id
        );

        return response()->json([
            'status'  => true,
            'data'    => $result,
        ]);
    }

    /**
     * GET /api/v1/delivery/pricing/categories
     *
     * Lista todas las categorias con precios de referencia a 3, 5 y 8 km.
     */
    public function categories(): JsonResponse
    {
        $categories = DeliveryCategoryPricing::active()
            ->orderBy('category_name')
            ->get()
            ->map(fn ($c) => [
                'slug'               => $c->category_slug,
                'name'               => $c->category_name,
                'commission_percent' => $c->commission_percent,
                'base_price'         => $c->base_price,
                'price_per_km'       => $c->price_per_km,
                'sample_prices'      => [
                    '3km' => $c->rawPrice(3),
                    '5km' => $c->rawPrice(5),
                    '8km' => $c->rawPrice(8),
                ],
                'distribution' => [
                    'driver_percent'    => $c->driver_percent,
                    'platform_percent'  => $c->platform_percent,
                    'insurance_percent' => $c->insurance_percent,
                ],
            ]);

        return response()->json([
            'status' => true,
            'data'   => $categories,
        ]);
    }

    /**
     * GET /api/v1/delivery/pricing/multipliers
     *
     * Devuelve los multiplicadores activos en este momento.
     */
    public function activeMultipliers(Request $request): JsonResponse
    {
        [$multiplier, $rules] = DeliveryPricingService::computeMultiplier(
            zoneId: $request->zone_id
        );

        $allRules = DynamicPricingRule::active()->get()->map(fn ($r) => [
            'type'       => $r->rule_type,
            'label'      => $r->label,
            'multiplier' => $r->multiplier,
            'is_active'  => $r->isActive(),
        ]);

        return response()->json([
            'status' => true,
            'data'   => [
                'current_multiplier' => $multiplier,
                'active_rules'       => $rules,
                'all_rules'          => $allRules,
                'rain_active'        => DeliveryPricingService::isRainActive(),
            ],
        ]);
    }
}
