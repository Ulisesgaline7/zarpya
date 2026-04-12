<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCategory extends Model
{
    use HasFactory;

    protected $table = 'service_categories';

    protected $fillable = ['slug', 'name', 'icon', 'platform_commission', 'status', 'sort_order'];

    protected $casts = [
        'platform_commission' => 'float',
        'status'              => 'boolean',
        'sort_order'          => 'integer',
    ];

    public function providers(): HasMany { return $this->hasMany(ServiceProvider::class, 'category_id'); }

    public function scopeActive($query) { return $query->where('status', true)->orderBy('sort_order'); }
}
