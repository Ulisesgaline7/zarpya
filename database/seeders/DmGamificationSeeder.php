<?php

namespace Database\Seeders;

use App\Models\DeliverymanLevel;
use App\Models\DmAchievement;
use Illuminate\Database\Seeder;

class DmGamificationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedLevels();
        $this->seedAchievements();
    }

    private function seedLevels(): void
    {
        $levels = [
            [
                'slug'              => 'standard',
                'name'              => '🟢 Zarpero Base',
                'description'       => 'Repartidor nuevo o con poca actividad. Acceso básico a pedidos y pago estándar.',
                'driver_percent'    => 88.00,
                'min_deliveries'    => 0,
                'min_rating'        => 0.0,
                'min_months_active' => 0,
                'benefits'          => [
                    'acceso_basico'    => true,
                    'pago_estandar'    => true,
                    'priority_level'   => 1,
                ],
                'sort_order' => 1,
            ],
            [
                'slug'              => 'pro',
                'name'              => '🔵 Zarpero Pro',
                'description'       => '+50 entregas/mes, calificación 4.6+. Prioridad media, bonificación semanal y acceso a pedidos mejor pagados.',
                'driver_percent'    => 91.00,
                'min_deliveries'    => 50,
                'min_rating'        => 4.6,
                'min_months_active' => 1,
                'benefits'          => [
                    'priority_orders'    => true,
                    'priority_level'     => 2,
                    'bonus_weekly'       => true,
                    'better_orders'      => true,
                ],
                'sort_order' => 2,
            ],
            [
                'slug'              => 'elite',
                'name'              => '🟣 Zarpero Elite',
                'description'       => '+150 entregas/mes, calificación 4.8+. Máxima prioridad, bonos VIP, soporte prioritario.',
                'driver_percent'    => 93.00,
                'min_deliveries'    => 150,
                'min_rating'        => 4.8,
                'min_months_active' => 3,
                'benefits'          => [
                    'priority_orders'    => true,
                    'priority_level'     => 3,
                    'bonus_weekly'       => true,
                    'bonus_peak_hours'   => true,
                    'vip_orders'         => true,
                    'dedicated_support'  => true,
                    'insurance_upgrade'  => true,
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($levels as $level) {
            DeliverymanLevel::updateOrCreate(
                ['slug' => $level['slug']],
                array_merge($level, ['status' => true])
            );
        }

        $this->command->info('✅ Niveles Zarpero: Base / Pro / Elite creados.');
    }

    private function seedAchievements(): void
    {
        $achievements = [
            // Entregas
            ['primer-zarpe',        '🚀 Primer Zarpe',          'Completa tu primera entrega',                  '🚀', 'bronze',  'deliveries',  1,   50],
            ['10-entregas',         '📦 10 Entregas',            'Completa 10 entregas',                         '📦', 'bronze',  'deliveries',  10,  100],
            ['50-entregas',         '⚡ Velocista',              'Completa 50 entregas',                         '⚡', 'silver',  'deliveries',  50,  250],
            ['100-entregas',        '💯 Centenario',             'Completa 100 entregas sin cancelación',        '💯', 'gold',    'no_cancel',   100, 500],
            ['500-entregas',        '🏆 Leyenda Zarpya',         'Completa 500 entregas',                        '🏆', 'gold',    'deliveries',  500, 1000],
            // Rating
            ['estrella-4-8',        '⭐ Estrella 4.8',           'Mantén calificación de 4.8 o más',             '⭐', 'gold',    'rating',      48,  200],
            ['estrella-perfecta',   '🌟 Estrella Perfecta',      'Alcanza calificación de 4.9+',                 '🌟', 'gold',    'rating',      49,  400],
            // Racha
            ['racha-5',             '🔥 En Llamas',              'Trabaja 5 días consecutivos',                  '🔥', 'silver',  'streak',      5,   150],
            ['racha-10',            '💎 Imparable',              'Trabaja 10 días consecutivos',                 '💎', 'gold',    'streak',      10,  350],
            ['racha-30',            '👑 Rey del Zarpe',          'Trabaja 30 días consecutivos',                 '👑', 'purple',  'streak',      30,  1000],
            // Aceptación
            ['aceptacion-90',       '✅ Siempre Listo',          'Mantén +90% de aceptación de pedidos',         '✅', 'silver',  'acceptance',  90,  200],
            ['aceptacion-95',       '🎯 Puntería Perfecta',      'Mantén +95% de aceptación de pedidos',         '🎯', 'gold',    'acceptance',  95,  400],
            // Sin cancelaciones
            ['sin-cancel-20',       '🛡️ Confiable',              '20 entregas consecutivas sin cancelar',        '🛡', 'silver',  'no_cancel',   20,  200],
        ];

        foreach ($achievements as [$slug, $name, $desc, $icon, $color, $type, $value, $xp]) {
            DmAchievement::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'             => $name,
                    'description'      => $desc,
                    'icon'             => $icon,
                    'color'            => $color,
                    'condition_type'   => $type,
                    'condition_value'  => $value,
                    'xp_reward'        => $xp,
                    'status'           => true,
                ]
            );
        }

        $this->command->info('✅ Logros: ' . count($achievements) . ' medallas creadas.');
    }
}
