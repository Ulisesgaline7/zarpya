<?php

namespace App\CentralLogics;

use App\Models\DeliveryCategoryPricing;
use App\Models\DynamicPricingRule;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * DeliveryPricingService
 * Formula: Precio = (Base + Km × Tarifa/km) × Multiplicador_dinamico
 * Usa Cache (file/database) en lugar de Redis directo para máxima compatibilidad.
 */
class DeliveryPricingService
{
    private const CACHE_RAIN_KEY   = 'zarpya_dynamic_pricing_rain';
    private const CACHE_DEMAND_KEY = 'zarpya_dynamic_pricing_demand_zone_';

    // ── Cálculo principal ────────────────────────────────────────

    public static function calculate(
        string $categorySlug,
        float $km,
        ?int $zoneId = null,
        ?Carbon $at = null
    ): array {
        $at      = $at ?? Carbon::now();
        $pricing = DeliveryCategoryPricing::active()->where('category_slug', $categorySlug)->first();

        if (! $pricing) {
            $pricing = new DeliveryCategoryPricing([
                'base_price' => 25.00, 'price_per_km' => 8.00,
                'commission_percent' => 15.00, 'driver_percent' => 88.00,
                'platform_percent' => 10.00, 'insurance_percent' => 2.00,
            ]);
        }

        $rawPrice         = $pricing->rawPrice($km);
        [$multiplier, $activeRules] = self::computeMultiplier($zoneId, $at);
        $finalPrice       = round($rawPrice * $multiplier, 2);
        $breakdown        = $pricing->distribute($finalPrice);
        $commissionAmount = round($finalPrice * $pricing->commission_percent / 100, 2);

        return [
            'raw_price'         => $rawPrice,
            'multiplier'        => $multiplier,
            'active_rules'      => $activeRules,
            'final_price'       => $finalPrice,
            'breakdown'         => $breakdown,
            'commission_amount' => $commissionAmount,
            'category'          => [
                'slug'               => $pricing->category_slug ?? $categorySlug,
                'name'               => $pricing->category_name ?? $categorySlug,
                'commission_percent' => $pricing->commission_percent,
                'base_price'         => $pricing->base_price,
                'price_per_km'       => $pricing->price_per_km,
            ],
        ];
    }

    // ── Multiplicador dinámico ───────────────────────────────────

    public static function computeMultiplier(?int $zoneId = null, ?Carbon $at = null): array
    {
        $at              = $at ?? Carbon::now();
        $rules           = DynamicPricingRule::active()->get();
        $finalMultiplier = 1.00;
        $activeLabels    = [];

        foreach ($rules as $rule) {
            if (! $rule->isActive($at)) continue;

            if ($rule->rule_type === DynamicPricingRule::TYPE_RAIN) {
                if (! self::isRainActive()) continue;
            }

            if ($rule->rule_type === DynamicPricingRule::TYPE_HIGH_DEMAND && $zoneId) {
                $dm = self::computeDemandMultiplier($zoneId, $rule);
                if ($dm > 1.0 && $dm > $finalMultiplier) {
                    $finalMultiplier = $dm;
                    $activeLabels[]  = $rule->label . " (×{$dm})";
                }
                continue;
            }

            if ($rule->multiplier > $finalMultiplier) {
                $finalMultiplier = $rule->multiplier;
            }
            $activeLabels[] = $rule->label . " (×{$rule->multiplier})";
        }

        return [round($finalMultiplier, 2), $activeLabels];
    }

    // ── Lluvia — Cache en lugar de Redis ─────────────────────────

    public static function setRain(bool $active, int $ttlSeconds = 7200): void
    {
        if ($active) {
            Cache::put(self::CACHE_RAIN_KEY, true, $ttlSeconds);
        } else {
            Cache::forget(self::CACHE_RAIN_KEY);
        }
    }

    public static function isRainActive(): bool
    {
        try {
            return (bool) Cache::get(self::CACHE_RAIN_KEY, false);
        } catch (\Throwable) {
            return false;
        }
    }

    // ── Alta demanda — Cache ─────────────────────────────────────

    public static function setZoneDemand(int $zoneId, int $activeOrders, int $ttlSeconds = 360): void
    {
        try {
            Cache::put(self::CACHE_DEMAND_KEY . $zoneId, $activeOrders, $ttlSeconds);
        } catch (\Throwable) {}
    }

    private static function computeDemandMultiplier(int $zoneId, DynamicPricingRule $rule): float
    {
        if (! $rule->demand_threshold || ! $rule->multiplier_min || ! $rule->multiplier_max) {
            return 1.00;
        }
        try {
            $activeOrders = (int) Cache::get(self::CACHE_DEMAND_KEY . $zoneId, 0);
        } catch (\Throwable) {
            return 1.00;
        }
        if ($activeOrders < $rule->demand_threshold) return 1.00;

        $ratio = min($activeOrders / ($rule->demand_threshold * 2), 1.0);
        return round($rule->multiplier_min + ($rule->multiplier_max - $rule->multiplier_min) * $ratio, 2);
    }

    // ── Tabla de precios de referencia ───────────────────────────

    public static function priceTable(string $categorySlug, array $kmPoints = [3, 5, 8]): array
    {
        $pricing = DeliveryCategoryPricing::active()->where('category_slug', $categorySlug)->first();
        if (! $pricing) return [];

        $table = [];
        foreach ($kmPoints as $km) {
            $table["km_{$km}"] = $pricing->rawPrice($km);
        }
        return $table;
    }

    // ── Distribución rápida ──────────────────────────────────────

    public static function distributePrice(float $totalPrice, string $categorySlug, ?string $driverLevel = null): array
    {
        $pricing          = DeliveryCategoryPricing::active()->where('category_slug', $categorySlug)->first();
        $driverPercent    = $pricing->driver_percent    ?? 88.00;
        $platformPercent  = $pricing->platform_percent  ?? 10.00;
        $insurancePercent = $pricing->insurance_percent ?? 2.00;

        if ($driverLevel) {
            $map = ['standard' => 88.00, 'pro' => 91.00, 'elite' => 93.00];
            if (isset($map[$driverLevel])) {
                $driverPercent   = $map[$driverLevel];
                $platformPercent = round(100 - $driverPercent - $insurancePercent, 2);
            }
        }

        return [
            'driver'    => round($totalPrice * $driverPercent / 100, 2),
            'platform'  => round($totalPrice * $platformPercent / 100, 2),
            'insurance' => round($totalPrice * $insurancePercent / 100, 2),
        ];
    }
}
