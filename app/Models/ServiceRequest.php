<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'provider_id', 'category_id',
        'description', 'address', 'lat', 'lng', 'scheduled_at',
        'quoted_price', 'final_price', 'platform_fee', 'provider_earning',
        'status', 'payment_method', 'paid', 'rating', 'review',
    ];

    protected $casts = [
        'lat'              => 'float',
        'lng'              => 'float',
        'scheduled_at'     => 'datetime',
        'quoted_price'     => 'float',
        'final_price'      => 'float',
        'platform_fee'     => 'float',
        'provider_earning' => 'float',
        'paid'             => 'boolean',
        'rating'           => 'float',
    ];

    public function customer(): BelongsTo  { return $this->belongsTo(User::class, 'customer_id'); }
    public function provider(): BelongsTo  { return $this->belongsTo(ServiceProvider::class, 'provider_id'); }
    public function category(): BelongsTo  { return $this->belongsTo(ServiceCategory::class, 'category_id'); }
}
