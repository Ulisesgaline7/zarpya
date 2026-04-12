<?php

namespace App\Services;

use App\Models\DeliveryMan;
use App\Models\DeliverymanLevel;
use App\Models\DmAchievement;
use App\Models\DmBonus;
use App\Models\DmStat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DmGamificationService
{
    // ---------------------------------------------------------------
    // Configuración de bonos por volumen mensual
    // ---------------------------------------------------------------
    const VOLUME_BONUSES = [
        20  => 200,   // 20 entregas → L 200
        50  => 600,   // 50 entregas → L 600
        100 => 1500,  // 100 entregas → L 1500
    ];

    // Bonos por horas pico (% adicional sobre la tarifa base)
    const PEAK_HOURS = [
        ['start' => '11:00', 'end' => '14:00', 'label' => 'Almuerzo', 'percent' => 15],
        ['start' => '18:00', 'end' => '21:00', 'label' => 'Cena',     'percent' => 20],
    ];

    // Bono de racha (días consecutivos)
    const STREAK_BONUSES = [
        5  => 150,  // 5 días → L 150
        10 => 350,  // 10 días → L 350
    ];

    // ---------------------------------------------------------------
    // Llamar después de cada entrega completada
    // ---------------------------------------------------------------
    public static function onDeliveryCompleted(DeliveryMan $dm, float $orderAmount, float $driverEarning): void
    {
        $stat = self::getOrCreateStat($dm->id);

        // 1. XP
        $xpGained = 10 + (int) floor($orderAmount / 50); // base 10 + 1 por cada L50
        $stat->increment('xp', $xpGained);

        // 2. Racha diaria
        self::updateStreak($stat);

        // 3. Entregas mensuales
        $currentMonth = now()->format('Y-m');
        if ($stat->current_month !== $currentMonth) {
            $stat->monthly_deliveries = 0;
            $stat->current_month      = $currentMonth;
        }
        $stat->increment('monthly_deliveries');
        $stat->refresh();

        // 4. Bono por volumen mensual
        self::checkVolumeBonuses($dm, $stat);

        // 5. Bono por hora pico
        self::checkPeakHourBonus($dm, $driverEarning);

        // 6. Bono de racha
        self::checkStreakBonus($dm, $stat);

        // 7. Actualizar nivel automáticamente
        self::recalculateLevel($dm);

        // 8. Verificar logros
        self::checkAchievements($dm, $stat);

        $stat->save();
    }

    // ---------------------------------------------------------------
    // Recalcular nivel del repartidor según sus métricas
    // ---------------------------------------------------------------
    public static function recalculateLevel(DeliveryMan $dm): void
    {
        $stat   = self::getOrCreateStat($dm->id);
        $rating = $dm->rating->first()?->average ?? 0;
        $deliveries = $stat->monthly_deliveries;

        $level = DeliverymanLevel::active()
            ->where('min_deliveries', '<=', $deliveries)
            ->where('min_rating', '<=', $rating)
            ->orderByDesc('sort_order')
            ->first();

        if ($level && $dm->level_id !== $level->id) {
            $dm->update(['level_id' => $level->id]);
        }
    }

    // ---------------------------------------------------------------
    // Bonos por volumen mensual
    // ---------------------------------------------------------------
    private static function checkVolumeBonuses(DeliveryMan $dm, DmStat $stat): void
    {
        $deliveries   = $stat->monthly_deliveries;
        $currentMonth = now()->format('Y-m');

        foreach (self::VOLUME_BONUSES as $threshold => $amount) {
            // Solo otorgar una vez por mes por umbral
            $alreadyGiven = DmBonus::where('delivery_man_id', $dm->id)
                ->where('type', DmBonus::TYPE_VOLUME)
                ->where('period', $currentMonth)
                ->where('label', "like", "%{$threshold} entregas%")
                ->exists();

            if ($deliveries >= $threshold && ! $alreadyGiven) {
                DmBonus::create([
                    'delivery_man_id' => $dm->id,
                    'type'            => DmBonus::TYPE_VOLUME,
                    'label'           => "Bono volumen: {$threshold} entregas",
                    'amount'          => $amount,
                    'period'          => $currentMonth,
                ]);
            }
        }
    }

    // ---------------------------------------------------------------
    // Bono por hora pico
    // ---------------------------------------------------------------
    private static function checkPeakHourBonus(DeliveryMan $dm, float $driverEarning): void
    {
        $now = now()->format('H:i');

        foreach (self::PEAK_HOURS as $peak) {
            if ($now >= $peak['start'] && $now <= $peak['end']) {
                $bonus = round($driverEarning * $peak['percent'] / 100, 2);
                DmBonus::create([
                    'delivery_man_id' => $dm->id,
                    'type'            => DmBonus::TYPE_PEAK_HOUR,
                    'label'           => "Hora pico {$peak['label']} (+{$peak['percent']}%)",
                    'amount'          => $bonus,
                    'period'          => now()->format('Y-m-d'),
                ]);
                break;
            }
        }
    }

    // ---------------------------------------------------------------
    // Bono de racha
    // ---------------------------------------------------------------
    private static function checkStreakBonus(DeliveryMan $dm, DmStat $stat): void
    {
        foreach (self::STREAK_BONUSES as $days => $amount) {
            if ($stat->streak_days === $days) {
                $alreadyGiven = DmBonus::where('delivery_man_id', $dm->id)
                    ->where('type', DmBonus::TYPE_STREAK)
                    ->where('label', "like", "%{$days} días%")
                    ->whereDate('earned_at', '>=', now()->subDays($days + 1))
                    ->exists();

                if (! $alreadyGiven) {
                    DmBonus::create([
                        'delivery_man_id' => $dm->id,
                        'type'            => DmBonus::TYPE_STREAK,
                        'label'           => "Racha de {$days} días consecutivos",
                        'amount'          => $amount,
                        'period'          => now()->format('Y-W'),
                    ]);
                }
            }
        }
    }

    // ---------------------------------------------------------------
    // Actualizar racha diaria
    // ---------------------------------------------------------------
    private static function updateStreak(DmStat $stat): void
    {
        $today     = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        if ($stat->last_active_date?->toDateString() === $today) {
            return; // ya contado hoy
        }

        if ($stat->last_active_date?->toDateString() === $yesterday) {
            $stat->streak_days++;
        } else {
            $stat->streak_days = 1; // reiniciar racha
        }

        $stat->last_active_date = $today;
    }

    // ---------------------------------------------------------------
    // Verificar y desbloquear logros
    // ---------------------------------------------------------------
    private static function checkAchievements(DeliveryMan $dm, DmStat $stat): void
    {
        $achievements = DmAchievement::where('status', true)->get();
        $unlockedIds  = DB::table('dm_achievement_unlocks')
            ->where('delivery_man_id', $dm->id)
            ->pluck('achievement_id')
            ->toArray();

        $rating = $dm->rating->first()?->average ?? 0;

        foreach ($achievements as $achievement) {
            if (in_array($achievement->id, $unlockedIds)) continue;

            $unlocked = match ($achievement->condition_type) {
                'deliveries' => $stat->xp / 10 >= $achievement->condition_value, // xp/10 ≈ entregas totales
                'rating'     => $rating >= $achievement->condition_value / 10,
                'streak'     => $stat->streak_days >= $achievement->condition_value,
                'acceptance' => self::getAcceptanceRate($dm->id) >= $achievement->condition_value,
                'no_cancel'  => self::getConsecutiveNoCancelCount($dm->id) >= $achievement->condition_value,
                default      => false,
            };

            if ($unlocked) {
                DB::table('dm_achievement_unlocks')->insertOrIgnore([
                    'delivery_man_id' => $dm->id,
                    'achievement_id'  => $achievement->id,
                    'unlocked_at'     => now(),
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ]);
                $stat->increment('xp', $achievement->xp_reward);
            }
        }
    }

    // ---------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------
    public static function getOrCreateStat(int $dmId): DmStat
    {
        return DmStat::firstOrCreate(['delivery_man_id' => $dmId]);
    }

    private static function getAcceptanceRate(int $dmId): float
    {
        $total    = DB::table('orders')->where('delivery_man_id', $dmId)->count();
        $accepted = DB::table('orders')->where('delivery_man_id', $dmId)
            ->whereNotIn('order_status', ['canceled'])->count();
        return $total > 0 ? round($accepted / $total * 100) : 0;
    }

    private static function getConsecutiveNoCancelCount(int $dmId): int
    {
        return DB::table('orders')
            ->where('delivery_man_id', $dmId)
            ->where('order_status', 'delivered')
            ->orderByDesc('id')
            ->count(); // simplificado
    }

    // ---------------------------------------------------------------
    // Bono semanal por calificación (llamar desde un job semanal)
    // ---------------------------------------------------------------
    public static function processWeeklyRatingBonuses(): void
    {
        $week = now()->format('Y-W');

        DeliveryMan::active()->with('rating')->chunk(100, function ($deliveryMen) use ($week) {
            foreach ($deliveryMen as $dm) {
                $rating = $dm->rating->first()?->average ?? 0;

                if ($rating >= 4.9) {
                    $amount = 300;
                    $label  = 'Bono semanal calificación 4.9+';
                } elseif ($rating >= 4.8) {
                    $amount = 150;
                    $label  = 'Bono semanal calificación 4.8+';
                } else {
                    continue;
                }

                $exists = DmBonus::where('delivery_man_id', $dm->id)
                    ->where('type', DmBonus::TYPE_RATING)
                    ->where('period', $week)
                    ->exists();

                if (! $exists) {
                    DmBonus::create([
                        'delivery_man_id' => $dm->id,
                        'type'            => DmBonus::TYPE_RATING,
                        'label'           => $label,
                        'amount'          => $amount,
                        'period'          => $week,
                    ]);
                }
            }
        });
    }

    // ---------------------------------------------------------------
    // Bono por tasa de aceptación (llamar semanalmente)
    // ---------------------------------------------------------------
    public static function processWeeklyAcceptanceBonuses(): void
    {
        $week = now()->format('Y-W');

        DeliveryMan::active()->chunk(100, function ($deliveryMen) use ($week) {
            foreach ($deliveryMen as $dm) {
                $rate = self::getAcceptanceRate($dm->id);
                if ($rate < 90) continue;

                $exists = DmBonus::where('delivery_man_id', $dm->id)
                    ->where('type', DmBonus::TYPE_ACCEPTANCE)
                    ->where('period', $week)
                    ->exists();

                if (! $exists) {
                    DmBonus::create([
                        'delivery_man_id' => $dm->id,
                        'type'            => DmBonus::TYPE_ACCEPTANCE,
                        'label'           => "Bono aceptación +90% ({$rate}%)",
                        'amount'          => 100,
                        'period'          => $week,
                    ]);
                }
            }
        });
    }

    // ---------------------------------------------------------------
    // Resumen de perfil gamificado para vistas
    // ---------------------------------------------------------------
    public static function getProfile(DeliveryMan $dm): array
    {
        $stat        = self::getOrCreateStat($dm->id);
        $level       = $dm->level ?? DeliverymanLevel::where('slug', 'standard')->first();
        $rating      = $dm->rating->first()?->average ?? 0;
        $totalOrders = $dm->order_transaction->count();
        $acceptance  = self::getAcceptanceRate($dm->id);

        $achievements = DB::table('dm_achievement_unlocks')
            ->join('dm_achievements', 'dm_achievements.id', '=', 'dm_achievement_unlocks.achievement_id')
            ->where('dm_achievement_unlocks.delivery_man_id', $dm->id)
            ->select('dm_achievements.*', 'dm_achievement_unlocks.unlocked_at')
            ->get();

        $pendingBonuses = DmBonus::where('delivery_man_id', $dm->id)
            ->where('paid', false)
            ->orderByDesc('earned_at')
            ->get();

        $totalBonusEarned = DmBonus::where('delivery_man_id', $dm->id)->sum('amount');

        // Ranking semanal
        $weeklyRank = DB::table('dm_stats')
            ->join('delivery_men', 'delivery_men.id', '=', 'dm_stats.delivery_man_id')
            ->where('delivery_men.active', 1)
            ->where('delivery_men.application_status', 'approved')
            ->orderByDesc('dm_stats.xp')
            ->pluck('delivery_man_id')
            ->search($dm->id);

        return compact(
            'stat', 'level', 'rating', 'totalOrders',
            'acceptance', 'achievements', 'pendingBonuses',
            'totalBonusEarned', 'weeklyRank'
        );
    }
}
