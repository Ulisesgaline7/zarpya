<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZarpyaModulePricingSeeder extends Seeder
{
    public function run(): void
    {
        // ========================
        // DELIVERY (Base + Km)
        // ========================
        $deliveryModules = [
            ['Restaurantes / Comida', 'food', 25.00, 8.00, 15],
            ['Cafeterías / Postres', 'food', 22.00, 7.00, 14],
            ['Farmacias', 'pharmacy', 20.00, 7.00, 12],
            ['Minisúpers / Tiendas', 'grocery', 23.00, 8.00, 13],
            ['Comida Rápida', 'food', 25.00, 8.00, 16],
            ['Repostería / Pasteles', 'food', 28.00, 9.00, 13],
            ['Pet Shop / Mascotas', 'ecommerce', 25.00, 8.00, 12],
            ['Belleza / Cosméticos', 'ecommerce', 22.00, 7.00, 14],
            ['Naturistas / Salud', 'ecommerce', 20.00, 7.00, 12],
            ['Licores / Bebidas', 'ecommerce', 28.00, 9.00, 18],
            ['Paquetería', 'parcel', 35.00, 10.00, 15],
        ];

        foreach ($deliveryModules as $m) {
            DB::table('modules')->insert([
                'module_name' => $m[0],
                'module_type' => $m[1],
                'base_price' => $m[2],
                'price_per_km' => $m[3],
                'price_per_minute' => 0,
                'minimum_fare' => 0,
                'deposit' => 0,
                'commission_percent' => $m[4],
                'status' => 1,
                'theme_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // ========================
        // TAXI (Base + Km + Min)
        // ========================
        DB::table('modules')->insert([
            'module_name' => 'Taxi',
            'module_type' => 'taxi',
            'base_price' => 35.00,      // Base fare
            'price_per_km' => 12.00,    // Por km
            'price_per_minute' => 2.00,   // Por minuto (tráfico)
            'minimum_fare' => 50.00,    // Tarifa mínima
            'deposit' => 0,
            'commission_percent' => 15,
            'status' => 1,
            'theme_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ========================
        // SERVICES (Proveedor define precio)
        // ========================
        DB::table('modules')->insert([
            'module_name' => 'Servicios',
            'module_type' => 'services',
            'base_price' => 0,        // No usa
            'price_per_km' => 0,       // No usa
            'price_per_minute' => 0,
            'minimum_fare' => 0,
            'deposit' => 0,
            'commission_percent' => 15,  // Comisión a Zarpya
            'status' => 1,
            'theme_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ========================
        // RENTAL (Precio por día/hora)
        // ========================
        DB::table('modules')->insert([
            'module_name' => 'Renta de Vehículos',
            'module_type' => 'rental',
            'base_price' => 350.00,    // Por día
            'price_per_km' => 0,
            'price_per_minute' => 0,
            'minimum_fare' => 150.00,    // Por hora mínimo
            'deposit' => 2000.00,       // Depósito
            'commission_percent' => 15,
            'status' => 1,
            'theme_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // ========================
        // MULTIPLICADORES DINÁMICOS
        // ========================
        $multipliers = [
            ['lluvia', 1.4, 'Lluvia'],
            ['noche', 1.25, 'Noche (9pm-12am)'],
            ['hora_pico', 1.3, 'Hora pico (12-1pm / 7-9pm)'],
            ['alta_demanda', 1.5, 'Alta demanda'],
            ['fin_semana', 1.1, 'Fin de semana'],
        ];

        foreach ($multipliers as $m) {
            DB::table('business_settings')->updateOrInsert(
                ['key' => 'dynamic_pricing_' . $m[0]],
                [
                    'key' => 'dynamic_pricing_' . $m[0],
                    'value' => json_encode(['multiplier' => $m[1], 'name' => $m[2], 'active' => 0]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ========================
        // DISTRIBUCIÓN DE GANANCIAS
        // ========================
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'delivery_earning_distribution'],
            [
                'key' => 'delivery_earning_distribution',
                'value' => json_encode([
                    'driver_percent' => 88,
                    'platform_percent' => 10,
                    'insurance_percent' => 2,
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // ========================
        // NIVELES DE REPARTIDOR
        // ========================
        $levels = [
            ['bronze', 88, 0, 0, 0],
            ['silver', 91, 100, 4.0, 3],
            ['gold', 93, 300, 4.5, 6],
        ];

        foreach ($levels as $l) {
            DB::table('business_settings')->updateOrInsert(
                ['key' => 'deliveryman_level_' . $l[0]],
                [
                    'key' => 'deliveryman_level_' . $l[0],
                    'value' => json_encode([
                        'name' => ucfirst($l[0]),
                        'driver_percent' => $l[1],
                        'min_deliveries' => $l[2],
                        'min_rating' => $l[3],
                        'min_months_active' => $l[4],
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // ========================
        // TAXI SURGE
        // ========================
        DB::table('business_settings')->updateOrInsert(
            ['key' => 'taxi_surge_pricing'],
            [
                'key' => 'taxi_surge_pricing',
                'value' => json_encode(['active' => 0, 'multiplier' => 1.0]),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        echo "Seeded: " . count($deliveryModules) + 3 . " modules\n";
    }
}