<?php

namespace App\Services;

use App\Services\External\WeatherService;
use App\Services\External\TrafficService;
use App\Models\Order;
use App\Models\DeliveryMan;
use Illuminate\Support\Facades\Cache;

class SurgeCalculationService
{
    protected $weatherService;
    protected $trafficService;

    public function __construct(WeatherService $weatherService, TrafficService $trafficService)
    {
        $this->weatherService = $weatherService;
        $this->trafficService = $trafficService;
    }

    /**
     * Calculate dynamic multiplier based on current conditions.
     *
     * @param float $lat
     * @param float $lng
     * @param float|null $destLat
     * @param float|null $destLng
     * @param int|null $zoneId
     * @return array
     */
    public function calculateMultiplier(float $lat, float $lng, float $destLat = null, float $destLng = null, int $zoneId = null): array
    {
        $multiplier = 1.0;
        $factors = [];

        // 1. Weather Factor (Lluvia)
        if ($this->weatherService->isBadWeather($lat, $lng)) {
            $weatherMultiplier = (float) (\App\Models\BusinessSetting::where('key', 'surge_multiplier_weather')->first()?->value ?? 1.5);
            $multiplier *= $weatherMultiplier;
            $factors[] = "Bad Weather (x{$weatherMultiplier})";
        }

        // 2. Traffic Factor (Tráfico)
        if ($destLat && $destLng) {
            $trafficData = $this->trafficService->getTrafficData("{$lat},{$lng}", "{$destLat},{$destLng}");
            if ($trafficData && ($trafficData['traffic_ratio'] > 1.2)) {
                $trafficMultiplier = 1.0 + (($trafficData['traffic_ratio'] - 1.0) * 0.5); // 50% of the traffic delay as multiplier
                $multiplier *= $trafficMultiplier;
                $factors[] = "Heavy Traffic (x" . round($trafficMultiplier, 2) . ")";
            }
        }

        // 3. Demand/Supply Factor (Demanda)
        if ($zoneId) {
            $demandMultiplier = $this->getDemandMultiplier($zoneId);
            if ($demandMultiplier > 1.0) {
                $multiplier *= $demandMultiplier;
                $factors[] = "High Demand (x" . round($demandMultiplier, 2) . ")";
            }
        }

        // 4. Peak Hour Factor (Hora Pico)
        $peakMultiplier = $this->getPeakHourMultiplier();
        if ($peakMultiplier > 1.0) {
            $multiplier *= $peakMultiplier;
            $factors[] = "Peak Hour (x" . round($peakMultiplier, 2) . ")";
        }

        // 5. Max Cap (To avoid excessive pricing)
        $maxMultiplier = (float) (\App\Models\BusinessSetting::where('key', 'surge_max_multiplier')->first()?->value ?? 2.0);
        if ($multiplier > $maxMultiplier) {
            $multiplier = $maxMultiplier;
            $factors[] = "Maximum Cap Reached (x{$maxMultiplier})";
        }

        return [
            'multiplier' => round($multiplier, 2),
            'factors' => $factors,
            'is_surge' => $multiplier > 1.0
        ];
    }

    /**
     * Calculate multiplier based on active orders vs available riders.
     */
    protected function getDemandMultiplier(int $zoneId): float
    {
        return Cache::remember("demand_multiplier_{$zoneId}", now()->addMinutes(5), function () use ($zoneId) {
            $activeOrders = Order::where('zone_id', $zoneId)->whereIn('order_status', ['pending', 'confirmed'])->count();
            $availableRiders = DeliveryMan::where('zone_id', $zoneId)->where('active', 1)->where('current_order_id', null)->count();

            if ($availableRiders == 0 && $activeOrders > 0) return 1.5;
            if ($availableRiders == 0) return 1.0;

            $ratio = $activeOrders / $availableRiders;
            if ($ratio > 2.0) return 1.3;
            if ($ratio > 1.0) return 1.1;

            return 1.0;
        });
    }

    /**
     * Simple peak hour logic.
     */
    protected function getPeakHourMultiplier(): float
    {
        $hour = (int) now()->format('H');
        // Lunch: 11:00 - 13:30, Dinner: 18:30 - 20:30
        if (($hour >= 11 && $hour <= 13) || ($hour >= 18 && $hour <= 20)) {
            return (float) (\App\Models\BusinessSetting::where('key', 'surge_multiplier_peak_hour')->first()?->value ?? 1.2);
        }
        return 1.0;
    }
}
