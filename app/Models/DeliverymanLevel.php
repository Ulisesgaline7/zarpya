<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeliverymanLevel extends Model
{
    use HasFactory;

    protected $table = 'deliveryman_levels';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'driver_percent',
        'min_deliveries',
        'min_rating',
        'min_months_active',
        'benefits',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'driver_percent'     => 'float',
        'min_deliveries'     => 'integer',
        'min_rating'         => 'float',
        'min_months_active'  => 'integer',
        'benefits'           => 'array',
        'status'             => 'boolean',
        'sort_order'         => 'integer',
    ];

    // Slugs predefinidos
    const STANDARD = 'standard'; // 88%
    const PRO       = 'pro';      // 91%
    const ELITE     = 'elite';    // 93%

    public function deliverymen(): HasMany
    {
        return $this->hasMany(DeliveryMan::class, 'level_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderBy('sort_order');
    }
}
