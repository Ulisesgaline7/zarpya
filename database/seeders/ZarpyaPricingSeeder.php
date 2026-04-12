<?php

namespace Database\Seeders;

use App\Models\DeliveryCategoryPricing;
use App\Models\DeliverymanLevel;
use App\Models\DynamicPricingRule;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ZarpyaPricingSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedCategoryPricing();
        $this->seedDynamicRules();
        $this->seedDeliverymanLevels();
        $this->seedServiceCategories();
    }

    // ---------------------------------------------------------------
    // Precios de envio por categoria
    // CATEGORIA | COMISION | BASE | X KM | 3KM | 5KM | 8KM
    // ---------------------------------------------------------------
    private function seedCategoryPricing(): void
    {
        $categories = [
            // slug                   name                        commission  base   km    driver  platform  insurance
            ['restaurants',           'Restaurantes / Comida',    15.00,  25.00, 8.00,  88.00, 10.00, 2.00],
            ['cafeterias',            'Cafeterias / Postres',     14.00,  22.00, 7.00,  88.00, 10.00, 2.00],
            ['pharmacies',            'Farmacias',                12.00,  20.00, 7.00,  88.00, 10.00, 2.00],
            ['minisupers',            'Minisupers / Tiendas',     13.00,  23.00, 8.00,  88.00, 10.00, 2.00],
            ['fast_food',             'Comida Rapida',            16.00,  25.00, 8.00,  88.00, 10.00, 2.00],
            ['bakeries',              'Reposteria / Pasteles',    13.00,  28.00, 9.00,  88.00, 10.00, 2.00],
            ['pet_shops',             'Pet Shop / Mascotas',      12.00,  25.00, 8.00,  88.00, 10.00, 2.00],
            ['beauty',                'Belleza / Cosmeticos',     14.00,  22.00, 7.00,  88.00, 10.00, 2.00],
            ['health',                'Naturistas / Salud',       12.00,  20.00, 7.00,  88.00, 10.00, 2.00],
            ['liquor',                'Licores / Bebidas',        18.00,  28.00, 9.00,  88.00, 10.00, 2.00],
            ['parcel_small',          'Paqueteria Pequena',        0.00,  35.00,10.00,  88.00, 10.00, 2.00], // tarifa fija (sin comision %)
        ];

        foreach ($categories as [$slug, $name, $commission, $base, $km, $driver, $platform, $insurance]) {
            DeliveryCategoryPricing::updateOrCreate(
                ['category_slug' => $slug],
                [
                    'category_name'      => $name,
                    'commission_percent' => $commission,
                    'base_price'         => $base,
                    'price_per_km'       => $km,
                    'driver_percent'     => $driver,
                    'platform_percent'   => $platform,
                    'insurance_percent'  => $insurance,
                    'status'             => true,
                ]
            );
        }

        $this->command->info('✅ DeliveryCategoryPricing: ' . count($categories) . ' categorias creadas.');
    }

    // ---------------------------------------------------------------
    // Multiplicadores dinamicos
    // ---------------------------------------------------------------
    private function seedDynamicRules(): void
    {
        $rules = [
            [
                'rule_type'    => 'rain',
                'label'        => 'Lluvia',
                'multiplier'   => 1.40,
                'time_start'   => null,
                'time_end'     => null,
                'days_of_week' => null,
                'priority'     => 10,
            ],
            [
                'rule_type'    => 'rush_hour',
                'label'        => 'Hora Pico (12-1pm / 7-9pm)',
                'multiplier'   => 1.30,
                'time_start'   => '12:00:00',
                'time_end'     => '13:00:00',
                'days_of_week' => null, // Todos los dias
                'priority'     => 8,
            ],
            [
                'rule_type'    => 'night',
                'label'        => 'Noche (9pm-12am)',
                'multiplier'   => 1.25,
                'time_start'   => '21:00:00',
                'time_end'     => '23:59:59',
                'days_of_week' => null,
                'priority'     => 7,
            ],
            [
                'rule_type'        => 'high_demand',
                'label'            => 'Alta Demanda',
                'multiplier'       => 1.30,
                'time_start'       => null,
                'time_end'         => null,
                'days_of_week'     => null,
                'demand_threshold' => 50,  // 50+ pedidos activos en la zona
                'multiplier_min'   => 1.20,
                'multiplier_max'   => 1.50,
                'priority'         => 9,
            ],
            [
                'rule_type'    => 'weekend',
                'label'        => 'Fin de Semana',
                'multiplier'   => 1.10,
                'time_start'   => null,
                'time_end'     => null,
                'days_of_week' => [0, 6], // Domingo=0, Sabado=6
                'priority'     => 5,
            ],
            // Segunda ventana hora pico vespertina (separada)
            [
                'rule_type'    => 'rush_hour_evening',
                'label'        => 'Hora Pico Nocturna (7-9pm)',
                'multiplier'   => 1.30,
                'time_start'   => '19:00:00',
                'time_end'     => '21:00:00',
                'days_of_week' => null,
                'priority'     => 8,
            ],
        ];

        foreach ($rules as $rule) {
            DynamicPricingRule::updateOrCreate(
                ['rule_type' => $rule['rule_type']],
                array_merge($rule, ['status' => true])
            );
        }

        $this->command->info('✅ DynamicPricingRules: ' . count($rules) . ' reglas creadas.');
    }

    // ---------------------------------------------------------------
    // Niveles de repartidor
    // ---------------------------------------------------------------
    private function seedDeliverymanLevels(): void
    {
        $levels = [
            [
                'slug'               => 'standard',
                'name'               => 'Repartidor Estandar',
                'description'        => 'Nivel de entrada. Ideal para nuevos repartidores.',
                'driver_percent'     => 88.00,
                'min_deliveries'     => 0,
                'min_rating'         => 0.00,
                'min_months_active'  => 0,
                'benefits'           => ['acceso_basico' => true],
                'sort_order'         => 1,
            ],
            [
                'slug'               => 'pro',
                'name'               => 'Repartidor Pro',
                'description'        => 'Mayor porcentaje por buen historial de entregas.',
                'driver_percent'     => 91.00,
                'min_deliveries'     => 200,
                'min_rating'         => 4.00,
                'min_months_active'  => 2,
                'benefits'           => [
                    'priority_orders' => true,
                    'bonus_weekends'  => 5, // L5 extra por entrega en fin de semana
                ],
                'sort_order'         => 2,
            ],
            [
                'slug'               => 'elite',
                'name'               => 'Repartidor Elite',
                'description'        => 'El mejor nivel. Maxima ganancia y beneficios premium.',
                'driver_percent'     => 93.00,
                'min_deliveries'     => 500,
                'min_rating'         => 4.50,
                'min_months_active'  => 6,
                'benefits'           => [
                    'priority_orders'   => true,
                    'bonus_weekends'    => 10,
                    'insurance_upgrade' => true,
                    'dedicated_support' => true,
                ],
                'sort_order'         => 3,
            ],
        ];

        foreach ($levels as $level) {
            DeliverymanLevel::updateOrCreate(
                ['slug' => $level['slug']],
                array_merge($level, ['status' => true])
            );
        }

        $this->command->info('✅ DeliverymanLevels: standard (88%), pro (91%), elite (93%) creados.');
    }

    // ---------------------------------------------------------------
    // Categorias de proveedores de servicios
    // ---------------------------------------------------------------
    private function seedServiceCategories(): void
    {
        $categories = [
            ['fontaneria',      'Fontaneria / Plomeria',     '🔧', 15.00],
            ['electricidad',    'Electricidad',              '⚡', 15.00],
            ['limpieza',        'Limpieza del Hogar',        '🧹', 12.00],
            ['ac_refrigeracion','A/C y Refrigeracion',       '❄️', 18.00],
            ['pintura',         'Pintura',                   '🖌️', 13.00],
            ['carpinteria',     'Carpinteria',               '🪚', 14.00],
            ['cerrajeria',      'Cerrajeria',                '🔑', 15.00],
            ['jardineria',      'Jardineria',                '🌿', 12.00],
            ['mecanica',        'Mecanica Automotriz',       '🚗', 18.00],
            ['mudanzas',        'Mudanzas y Fletes',         '📦', 20.00],
            ['computadoras',    'Reparacion de Computadoras','💻', 15.00],
            ['television',      'Electrodomesticos / TV',    '📺', 15.00],
        ];

        foreach ($categories as $i => [$slug, $name, $icon, $commission]) {
            ServiceCategory::updateOrCreate(
                ['slug' => $slug],
                [
                    'name'                => $name,
                    'icon'                => $icon,
                    'platform_commission' => $commission,
                    'status'              => true,
                    'sort_order'          => $i + 1,
                ]
            );
        }

        $this->command->info('✅ ServiceCategories: ' . count($categories) . ' categorias creadas.');
    }
}
