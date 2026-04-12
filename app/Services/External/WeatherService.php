<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5/weather';

    public function __construct()
    {
        $this->apiKey = \App\Models\BusinessSetting::where('key', 'openweather_api_key')->first()?->value;
    }

    /**
     * Get weather status for a location.
     *
     * @param float $lat
     * @param float $lng
     * @return array|null
     */
    public function getWeather(float $lat, float $lng): ?array
    {
        if (!$this->apiKey) return null;

        $cacheKey = "weather_{$lat}_{$lng}";

        return Cache::remember($cacheKey, now()->addMinutes(15), function () use ($lat, $lng) {
            try {
                $response = Http::get($this->baseUrl, [
                    'lat' => $lat,
                    'lon' => $lng,
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Weather API Error: " . $e->getMessage());
            }

            return null;
        });
    }

    /**
     * Check if it's currently raining or bad weather.
     *
     * @param float $lat
     * @param float $lng
     * @return bool
     */
    public function isBadWeather(float $lat, float $lng): bool
    {
        $weather = $this->getWeather($lat, $lng);
        if (!$weather) return false;

        $main = strtolower($weather['weather'][0]['main'] ?? '');
        return in_array($main, ['rain', 'snow', 'thunderstorm', 'drizzle']);
    }
}
