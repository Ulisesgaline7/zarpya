<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RentalVehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'zone_id', 'type', 'brand', 'model', 'plate', 'color', 'image',
        'price_per_hour', 'price_per_day', 'deposit', 'seats',
        'with_driver', 'status', 'owner_id', 'owner_percent', 'platform_percent',
    ];

    protected $casts = [
        'price_per_hour'   => 'float',
        'price_per_day'    => 'float',
        'deposit'          => 'float',
        'seats'            => 'integer',
        'with_driver'      => 'boolean',
        'owner_percent'    => 'float',
        'platform_percent' => 'float',
    ];

    public function zone(): BelongsTo    { return $this->belongsTo(Zone::class); }
    public function owner(): BelongsTo   { return $this->belongsTo(User::class, 'owner_id'); }
    public function bookings(): HasMany  { return $this->hasMany(RentalBooking::class, 'vehicle_id'); }

    public function scopeAvailable($query) { return $query->where('status', 'available'); }
}
