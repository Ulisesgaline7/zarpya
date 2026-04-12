<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DmBonus extends Model
{
    protected $table = 'dm_bonuses';

    protected $fillable = [
        'delivery_man_id', 'type', 'label', 'amount', 'period', 'paid', 'earned_at',
    ];

    protected $casts = [
        'amount'    => 'float',
        'paid'      => 'boolean',
        'earned_at' => 'datetime',
    ];

    // Tipos de bono
    const TYPE_VOLUME     = 'volume';
    const TYPE_PEAK_HOUR  = 'peak_hour';
    const TYPE_RATING     = 'rating';
    const TYPE_ACCEPTANCE = 'acceptance';
    const TYPE_STREAK     = 'streak';

    public function deliveryMan(): BelongsTo
    {
        return $this->belongsTo(DeliveryMan::class);
    }
}
