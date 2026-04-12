<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryCategoryPricing extends Model
{
    use HasFactory;

    protected $table = 'delivery_category_pricing';

    protected $fillable = [
        'module_id',
        'category_slug',
        'category_name',
        'base_price',
        'price_per_km',
        'commission_percent',
        'driver_percent',
        'platform_percent',
        'insurance_percent',
        'status',
    ];

    protected $casts = [
        'base_price'         => 'float',
        'price_per_km'       => 'float',
        'commission_percent' => 'float',
        'driver_percent'     => 'float',
        'platform_percent'   => 'float',
        'insurance_percent'  => 'float',
        'status'             => 'boolean',
    ];

    // ---------------------------------------------------------------
    // Relaciones
    // ---------------------------------------------------------------

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Precio sin multiplicador dinamico.
     * Precio = Base + (km × Tarifa/km)
     */
    public function rawPrice(float $km): float
    {
        return $this->base_price + ($km * $this->price_per_km);
    }

    /**
     * Precio final aplicando multiplicador.
     * Precio = (Base + km × Tarifa/km) × Multiplicador
     */
    public function finalPrice(float $km, float $multiplier = 1.0): float
    {
        return round($this->rawPrice($km) * $multiplier, 2);
    }

    /**
     * Distribucion del precio: [driver, platform, insurance]
     */
    public function distribute(float $totalPrice): array
    {
        return [
            'driver'    => round($totalPrice * $this->driver_percent / 100, 2),
            'platform'  => round($totalPrice * $this->platform_percent / 100, 2),
            'insurance' => round($totalPrice * $this->insurance_percent / 100, 2),
        ];
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }
}
