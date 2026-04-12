<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DmAchievement extends Model
{
    protected $table = 'dm_achievements';

    protected $fillable = [
        'slug', 'name', 'description', 'icon', 'color',
        'condition_type', 'condition_value', 'xp_reward', 'status',
    ];

    protected $casts = [
        'condition_value' => 'integer',
        'xp_reward'       => 'integer',
        'status'          => 'boolean',
    ];

    public function deliveryMen(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryMan::class, 'dm_achievement_unlocks')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }
}
