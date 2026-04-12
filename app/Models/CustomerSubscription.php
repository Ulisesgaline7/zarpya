<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerSubscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'type', 'expires_at', 'status'];

    protected $casts = [
        'expires_at' => 'datetime',
        'status' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    /**
     * Get benefits based on subscription type.
     */
    public function getBenefits(): array
    {
        return match($this->type) {
            'plus' => [
                'delivery_free_threshold'  => (float) (\App\Models\BusinessSetting::where('key', 'sub_plus_delivery_threshold')->first()?->value ?? 150.0),
                'discount_percentage'      => (float) (\App\Models\BusinessSetting::where('key', 'sub_plus_discount')->first()?->value ?? 5.0),
                'monthly_free_deliveries'  => (int)   (\App\Models\BusinessSetting::where('key', 'sub_plus_free_deliveries')->first()?->value ?? 1),
            ],
            'premium' => [
                'delivery_free_threshold'  => 0.0,
                'discount_percentage'      => (float) (\App\Models\BusinessSetting::where('key', 'sub_premium_discount')->first()?->value ?? 10.0),
                'cashback_percentage'      => (float) (\App\Models\BusinessSetting::where('key', 'sub_premium_cashback')->first()?->value ?? 2.0),
                'monthly_free_deliveries'  => PHP_INT_MAX, // todos los envíos gratis
            ],
            default => [
                'delivery_free_threshold'  => PHP_INT_MAX, // sin envío gratis
                'discount_percentage'      => 0.0,
                'monthly_free_deliveries'  => 0,
            ],
        };
    }
}
