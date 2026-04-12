<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class TaxiRide extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'driver_id', 'zone_id', 'vehicle_type',
        'pickup_address', 'pickup_lat', 'pickup_lng',
        'dropoff_address', 'dropoff_lat', 'dropoff_lng',
        'distance_km', 'duration_min',
        'base_fare', 'distance_fare', 'dynamic_multiplier', 'total_fare',
        'driver_earning', 'platform_earning',
        'status', 'payment_method', 'payment_ref', 'paid',
        'cancellation_reason', 'accepted_at', 'started_at', 'completed_at',
    ];

    protected $casts = [
        'pickup_lat'         => 'float',
        'pickup_lng'         => 'float',
        'dropoff_lat'        => 'float',
        'dropoff_lng'        => 'float',
        'distance_km'        => 'float',
        'duration_min'       => 'integer',
        'base_fare'          => 'float',
        'distance_fare'      => 'float',
        'dynamic_multiplier' => 'float',
        'total_fare'         => 'float',
        'driver_earning'     => 'float',
        'platform_earning'   => 'float',
        'paid'               => 'boolean',
        'accepted_at'        => 'datetime',
        'started_at'         => 'datetime',
        'completed_at'       => 'datetime',
    ];

    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function driver(): BelongsTo   { return $this->belongsTo(TaxiDriver::class, 'driver_id'); }
    public function zone(): BelongsTo     { return $this->belongsTo(Zone::class); }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }
}
