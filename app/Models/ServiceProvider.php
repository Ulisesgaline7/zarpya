<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceProvider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'zone_id',
        'business_name', 'description', 'phone', 'avatar', 'portfolio_images',
        'avg_rating', 'total_reviews', 'total_jobs',
        'hourly_rate', 'fixed_rate', 'availability_schedule',
        'status', 'verified', 'featured', 'lat', 'lng',
    ];

    protected $casts = [
        'portfolio_images'      => 'array',
        'availability_schedule' => 'array',
        'avg_rating'            => 'float',
        'total_reviews'         => 'integer',
        'total_jobs'            => 'integer',
        'hourly_rate'           => 'float',
        'fixed_rate'            => 'float',
        'verified'              => 'boolean',
        'featured'              => 'boolean',
        'lat'                   => 'float',
        'lng'                   => 'float',
    ];

    public function user(): BelongsTo           { return $this->belongsTo(User::class); }
    public function category(): BelongsTo       { return $this->belongsTo(ServiceCategory::class, 'category_id'); }
    public function zone(): BelongsTo           { return $this->belongsTo(Zone::class); }
    public function serviceRequests(): HasMany  { return $this->hasMany(ServiceRequest::class, 'provider_id'); }

    public function scopeActive($query)  { return $query->where('status', 'active'); }
    public function scopeFeatured($query){ return $query->where('featured', true); }
}
