<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxiZoneRate extends Model
{
    use HasFactory;

    protected $table = 'taxi_zone_rates';

    protected $fillable = [
        'zone_id', 'vehicle_type',
        'base_fare', 'fare_per_km', 'fare_per_min',
        'min_fare', 'platform_percent', 'status',
    ];

    protected $casts = [
        'base_fare'        => 'float',
        'fare_per_km'      => 'float',
        'fare_per_min'     => 'float',
        'min_fare'         => 'float',
        'platform_percent' => 'float',
        'status'           => 'boolean',
    ];

    public function zone(): BelongsTo { return $this->belongsTo(Zone::class); }

    public function scopeActive($query) { return $query->where('status', true); }
}
