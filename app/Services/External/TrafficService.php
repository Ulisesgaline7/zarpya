<?php

namespace App\Services\External;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TrafficService
{
    protected $apiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api/distancematrix/json';

    public function __construct()
    {
        $this->apiKey = \App\Models\BusinessSetting::where('key', 'google_maps_traffic_key')->first()?->value;
    }

    /**
     * Get traffic status between two points.
     *
     * @param string $origin "lat,lng"
     * @param string $destination "lat,lng"
     * @return array|null
     */
    public function getTrafficData(string $origin, string $destination): ?array
    {
        if (!$this->apiKey) return null;

        $cacheKey = "traffic_" . md5("{$origin}_{$destination}");

        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($origin, $destination) {
            try {
                $response = Http::get($this->baseUrl, [
                    'origins' => $origin,
                    'destinations' => $destination,
                    'departure_time' => 'now',
                    'traffic_model' => 'best_guess',
                    'key' => $this->apiKey
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    if ($data['status'] === 'OK') {
                        $element = $data['rows'][0]['elements'][0];
                        if ($element['status'] === 'OK') {
                            $duration = $element['duration']['value']; // in seconds
                            $durationInTraffic = $element['duration_in_traffic']['value'] ?? $duration;
                            
                            $trafficRatio = $durationInTraffic / $duration;
                            
                            return [
                                'duration' => $duration,
                                'duration_in_traffic' => $durationInTraffic,
                                'traffic_ratio' => $trafficRatio,
                                'is_heavy' => $trafficRatio > 1.3 // 30% slower than normal
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error("Google Traffic API Error: " . $e->getMessage());
            }

            return null;
        });
    }
}
