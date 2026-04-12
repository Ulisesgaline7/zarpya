<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id', 'customer_id', 'driver_id',
        'start_at', 'end_at', 'total_price', 'deposit_paid',
        'pickup_address', 'dropoff_address',
        'status', 'payment_method', 'payment_ref', 'notes',
    ];

    protected $casts = [
        'start_at'     => 'datetime',
        'end_at'       => 'datetime',
        'total_price'  => 'float',
        'deposit_paid' => 'float',
    ];

    public function vehicle(): BelongsTo  { return $this->belongsTo(RentalVehicle::class, 'vehicle_id'); }
    public function customer(): BelongsTo { return $this->belongsTo(User::class, 'customer_id'); }
    public function driver(): BelongsTo   { return $this->belongsTo(DeliveryMan::class, 'driver_id'); }

    /** Duracion en horas (para calculo de precio) */
    public function durationHours(): float
    {
        return $this->start_at->diffInHours($this->end_at);
    }
}
