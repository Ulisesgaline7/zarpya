<?php

namespace App\Services;

use App\Models\RestaurantCredit;
use App\Models\CreditTransaction;
use Illuminate\Support\Facades\DB;

class AdCreditService
{
    /**
     * Deduct credits from a restaurant/store.
     *
     * @param int $storeId
     * @param float $amount
     * @param string $type ('impression' or 'click')
     * @param string|null $reference
     * @return bool
     */
    public function deductCredits(int $storeId, string $type, ?string $reference = null): bool
    {
        return DB::transaction(function () use ($storeId, $type, $reference) {
            $credits = RestaurantCredit::firstOrCreate(['store_id' => $storeId]);

            // Define costs (Ideally these should be in BusinessSetting)
            $costPerImpression = (float) (\App\Models\BusinessSetting::where('key', 'ad_cost_impression')->first()?->value ?? 0.10);
            $costPerClick = (float) (\App\Models\BusinessSetting::where('key', 'ad_cost_click')->first()?->value ?? 0.50);
            
            $amount = $type === 'impression' ? $costPerImpression : $costPerClick;
            
            if ($credits->amount < $amount) {
                return false;
            }

            $credits->decrement('amount', $amount);

            CreditTransaction::create([
                'store_id' => $storeId,
                'amount' => $amount,
                'type' => 'deduct',
                'reference' => $reference,
                'description' => "Deducción por {$type} de anuncio",
            ]);

            return true;
        });
    }

    /**
     * Add credits to a restaurant/store.
     *
     * @param int $storeId
     * @param float $amount
     * @param string|null $reference
     * @param string|null $description
     * @return void
     */
    public function addCredits(int $storeId, float $amount, ?string $reference = null, ?string $description = null): void
    {
        DB::transaction(function () use ($storeId, $amount, $reference, $description) {
            $credits = RestaurantCredit::firstOrCreate(['store_id' => $storeId]);
            $credits->increment('amount', $amount);

            CreditTransaction::create([
                'store_id' => $storeId,
                'amount' => $amount,
                'type' => 'add',
                'reference' => $reference,
                'description' => $description ?? "Adición de créditos",
            ]);
        });
    }
}
