<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

trait ActivationClass
{
    public function is_local(): bool
    {
        // Always return false to bypass local checks
        return false;
    }

    public function getDomain(): string
    {
        return str_replace(["http://", "https://", "www."], "", url('/'));
    }

    public function getSystemAddonCacheKey(string|null $app = 'default'): string
    {
        $appName = env('APP_NAME').'_cache';
        return str_replace('-', '_', Str::slug($appName.'cache_system_addons_for_' . $app . '_' . $this->getDomain()));
    }

    public function getAddonsConfig(): array
    {
        if (file_exists(base_path('config/system-addons.php'))) {
            return include(base_path('config/system-addons.php'));
        }

        $apps = ['admin_panel', 'vendor_app', 'deliveryman_app', 'react_web'];
        $appConfig = [];
        foreach ($apps as $app) {
            $appConfig[$app] = [
                "active" => "1", // Set all to active by default
                "username" => "bypassed",
                "purchase_key" => "bypassed",
                "software_id" => "bypassed",
                "domain" => $this->getDomain(),
                "software_type" => $app == 'admin_panel' ? "product" : 'addon',
            ];
        }
        return $appConfig;
    }

    public function getCacheTimeoutByDays(int $days = 3): int
    {
        return 60 * 60 * 24 * $days;
    }

    public function getRequestConfig(string|null $username = null, string|null $purchaseKey = null, string|null $softwareId = null, string|null $softwareType = null): array
    {
        // Always return active status
        return [
            "active" => "1",
            "username" => "bypassed",
            "purchase_key" => "bypassed",
            "software_id" => "bypassed",
            "domain" => $this->getDomain(),
            "software_type" => $softwareType ?? 'product',
        ];
    }

    public function checkActivationCache(string|null $app)
    {
        // Always return true (activated)
        return true;
    }

    public function updateActivationConfig($app, $response): void
    {
        // No need to update config when bypassing
    }
}
