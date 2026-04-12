<?php

namespace App\Observers;

use App\Models\StoreSubscription;
use App\Models\SubscriptionPackage;
use App\Services\AdCreditService;
use Illuminate\Support\Facades\Log;

class StoreSubscriptionObserver
{
    public function __construct(protected AdCreditService $creditService) {}

    /**
     * Se dispara cuando se crea una nueva suscripción.
     * Si el paquete es el plan Boost fundador, carga los créditos de ads automáticamente.
     */
    public function created(StoreSubscription $subscription): void
    {
        $this->handleBoostCredits($subscription, 'created');
    }

    /**
     * Se dispara cuando se actualiza una suscripción (ej: cambio de plan).
     * Solo aplica si el package_id cambió y el nuevo plan es Boost.
     */
    public function updated(StoreSubscription $subscription): void
    {
        // Solo actuar si el package_id cambió
        if (! $subscription->wasChanged('package_id')) {
            return;
        }

        $this->handleBoostCredits($subscription, 'updated');
    }

    // ---------------------------------------------------------------

    private function handleBoostCredits(StoreSubscription $subscription, string $event): void
    {
        try {
            $package = SubscriptionPackage::withoutGlobalScope('translate')
                ->find($subscription->package_id);

            if (! $package || $package->founder_type !== 'boost' || $package->promo_credits <= 0) {
                return;
            }

            // Verificar que no se hayan cargado ya los créditos para este store
            $alreadyLoaded = \App\Models\CreditTransaction::where('store_id', $subscription->store_id)
                ->where('reference', 'founder-boost')
                ->where('type', 'add')
                ->exists();

            if ($alreadyLoaded) {
                return; // Idempotente: no cargar dos veces
            }

            $this->creditService->addCredits(
                storeId:     $subscription->store_id,
                amount:      $package->promo_credits,
                reference:   'founder-boost',
                description: "Créditos de promoción plan Boost fundador — L {$package->promo_credits} automáticos",
            );

            Log::info("BoostCredits: L{$package->promo_credits} cargados al store #{$subscription->store_id} ({$event})");

        } catch (\Throwable $e) {
            Log::error("BoostCredits error store #{$subscription->store_id}: " . $e->getMessage());
        }
    }
}
