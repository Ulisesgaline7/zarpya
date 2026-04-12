<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DmStat extends Model
{
    protected $table = 'dm_stats';

    protected $fillable = [
        'delivery_man_id', 'xp', 'streak_days', 'last_active_date',
        'monthly_deliveries', 'current_month',
        'weekly_bonus_earned', 'current_week',
    ];

    protected $casts = [
        'xp'                  => 'integer',
        'streak_days'         => 'integer',
        'monthly_deliveries'  => 'integer',
        'weekly_bonus_earned' => 'float',
        'last_active_date'    => 'date',
    ];

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class);
    }

    /** Nivel XP calculado */
    public function getLevelXpAttribute(): int
    {
        return (int) floor($this->xp / 100);
    }

    /** Progreso hacia el siguiente nivel (0-100) */
    public function getXpProgressAttribute(): int
    {
        return $this->xp % 100;
    }
}
