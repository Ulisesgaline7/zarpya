<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DynamicPricingRule extends Model
{
    use HasFactory;

    protected $table = 'dynamic_pricing_rules';

    protected $fillable = [
        'rule_type',
        'label',
        'multiplier',
        'time_start',
        'time_end',
        'days_of_week',
        'demand_threshold',
        'multiplier_min',
        'multiplier_max',
        'status',
        'priority',
    ];

    protected $casts = [
        'multiplier'       => 'float',
        'multiplier_min'   => 'float',
        'multiplier_max'   => 'float',
        'days_of_week'     => 'array',
        'demand_threshold' => 'integer',
        'status'           => 'boolean',
        'priority'         => 'integer',
    ];

    // ---------------------------------------------------------------
    // Tipos pre-definidos
    // ---------------------------------------------------------------

    const TYPE_RAIN        = 'rain';        // ×1.4
    const TYPE_NIGHT       = 'night';       // ×1.25  (9pm-12am)
    const TYPE_RUSH_HOUR   = 'rush_hour';   // ×1.3   (12-1pm / 7-9pm)
    const TYPE_HIGH_DEMAND = 'high_demand'; // ×1.2–1.5 dinamico
    const TYPE_WEEKEND     = 'weekend';     // ×1.1

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------

    /**
     * Verifica si esta regla aplica ahora mismo.
     */
    public function isActive(?Carbon $at = null): bool
    {
        if (! $this->status) {
            return false;
        }

        $now = $at ?? Carbon::now();

        // Verificar dia de la semana
        if ($this->days_of_week && ! in_array($now->dayOfWeek, $this->days_of_week)) {
            return false;
        }

        // Verificar ventana de tiempo
        if ($this->time_start && $this->time_end) {
            $currentTime = $now->format('H:i:s');
            if ($currentTime < $this->time_start || $currentTime > $this->time_end) {
                return false;
            }
        }

        return true;
    }

    // ---------------------------------------------------------------
    // Scopes
    // ---------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', true)->orderByDesc('priority');
    }
}
