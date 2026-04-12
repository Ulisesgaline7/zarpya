<?php

namespace App\Jobs;

use App\CentralLogics\DeliveryPricingService;
use App\Models\BusinessSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Consulta OpenWeatherMap cada 15 minutos y activa/desactiva
 * el multiplicador de lluvia automáticamente.
 *
 * Programar en Kernel.php:
 *   $schedule->job(CheckWeatherConditionJob::class)->everyFifteenMinutes();
 */
class CheckWeatherConditionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Códigos de clima de OpenWeather que activan el multiplicador
    // 2xx = tormenta, 3xx = llovizna, 5xx = lluvia, 6xx = nieve
    public const RAIN_CODES = [200, 201, 202, 210, 211, 212, 221, 230, 231, 232,
                        300, 301, 302, 310, 311, 312, 313, 314, 321,
                        500, 501, 502, 503, 504, 511, 520, 521, 522, 531,
                        600, 601, 602, 611, 612, 613, 615, 616, 620, 621, 622];

    public function handle(): void
    {
        $apiKey = BusinessSetting::where('key', 'openweather_api_key')->first()?->value;

        if (! $apiKey) {
            Log::info('CheckWeather: sin API key configurada, saltando.');
            return;
        }

        // Coordenadas de Tegucigalpa (o leer de business settings)
        $defaultLocation = BusinessSetting::where('key', 'default_location')->first();
        $location        = $defaultLocation?->value ? json_decode($defaultLocation->value, true) : null;
        $lat             = $location['lat'] ?? 14.0818;
        $lng             = $location['lng'] ?? -87.2068;

        try {
            $response = Http::timeout(10)->get('https://api.openweathermap.org/data/2.5/weather', [
                'lat'   => $lat,
                'lon'   => $lng,
                'appid' => $apiKey,
                'units' => 'metric',
            ]);

            if (! $response->successful()) {
                Log::warning('CheckWeather: respuesta no exitosa — HTTP ' . $response->status() . ($response->status() === 401 ? ' (API key inválida o no activada aún — espera hasta 2h después de crearla)' : ''));
                return;
            }

            $data       = $response->json();
            $weatherId  = $data['weather'][0]['id'] ?? 800;
            $weatherDesc = $data['weather'][0]['description'] ?? 'clear';
            $isRaining  = in_array($weatherId, self::RAIN_CODES);

            $wasRaining = DeliveryPricingService::isRainActive();

            if ($isRaining && ! $wasRaining) {
                DeliveryPricingService::setRain(true, 3600); // 1 hora TTL
                Log::info("CheckWeather: 🌧️ Lluvia detectada ({$weatherDesc}, código {$weatherId}) — multiplicador ×1.4 ACTIVADO");
            } elseif (! $isRaining && $wasRaining) {
                DeliveryPricingService::setRain(false);
                Log::info("CheckWeather: ☀️ Sin lluvia ({$weatherDesc}) — multiplicador de lluvia DESACTIVADO");
            } else {
                Log::debug("CheckWeather: sin cambio — {$weatherDesc} (código {$weatherId}), lluvia activa: " . ($wasRaining ? 'sí' : 'no'));
            }

            // Guardar último estado en business_settings para mostrarlo en el panel
            \App\CentralLogics\Helpers::businessUpdateOrInsert(
                ['key' => 'weather_last_check'],
                ['value' => json_encode([
                    'checked_at'  => now()->toDateTimeString(),
                    'description' => $weatherDesc,
                    'code'        => $weatherId,
                    'is_raining'  => $isRaining,
                    'temp'        => $data['main']['temp'] ?? null,
                    'humidity'    => $data['main']['humidity'] ?? null,
                ])]
            );

        } catch (\Throwable $e) {
            Log::error('CheckWeather error: ' . $e->getMessage());
        }
    }
}
