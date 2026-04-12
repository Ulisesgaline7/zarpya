<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\AdminRole;
use App\Models\DeliveryMan;
use App\Models\DeliverymanLevel;
use App\Models\ServiceCategory;
use App\Models\ServiceProvider;
use App\Models\Store;
use App\Models\StoreSubscription;
use App\Models\SubscriptionPackage;
use App\Models\TaxiDriver;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AdCreditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoProfilesSeeder extends Seeder
{
    const ZONE_ID    = 2;  // Tegucigalpa
    const VEHICLE_ID = 1;  // Motocicleta

    public function run(): void
    {
        $this->seedAdminRoles();
        $this->seedAdmins();
        $this->seedCustomers();
        $this->seedVendors();
        $this->seedDeliveryMen();
        $this->seedTaxiDrivers();
        $this->seedServiceCategories();
        $this->seedServiceProviders();

        $this->command->newLine();
        $this->command->info('✅ Perfiles de ejemplo creados. Credenciales:');
        $this->printCredentials();
    }

    // ── 1. ROLES DE ADMIN ────────────────────────────────────────
    private function seedAdminRoles(): void
    {
        // Rol de empleado con acceso limitado
        AdminRole::firstOrCreate(
            ['name' => 'Empleado'],
            [
                'modules' => json_encode([
                    'order', 'store', 'item', 'customer', 'delivery_man',
                    'advertisement', 'banner', 'coupon', 'notification',
                ]),
                'status' => 1,
            ]
        );

        $this->command->info('  → Roles de admin OK');
    }

    // ── 2. ADMINS ────────────────────────────────────────────────
    private function seedAdmins(): void
    {
        $employeeRole = AdminRole::where('name', 'Empleado')->first();

        // Admin maestro (ya existe, solo asegurar)
        Admin::firstOrCreate(
            ['email' => 'admin@zarpya.com'],
            [
                'f_name'   => 'Admin',
                'l_name'   => 'Zarpya',
                'phone'    => '+50492515326',
                'password' => Hash::make('Zarpya2026!'),
                'role_id'  => 1,
                'zone_id'  => self::ZONE_ID,
            ]
        );

        // Empleado de operaciones
        Admin::firstOrCreate(
            ['email' => 'operaciones@zarpya.com'],
            [
                'f_name'   => 'Carlos',
                'l_name'   => 'Operaciones',
                'phone'    => '+50488001001',
                'password' => Hash::make('Operaciones2026!'),
                'role_id'  => $employeeRole?->id ?? 1,
                'zone_id'  => self::ZONE_ID,
            ]
        );

        $this->command->info('  → Admins OK');
    }

    // ── 3. CLIENTES ──────────────────────────────────────────────
    private function seedCustomers(): void
    {
        $customers = [
            // Plan Free
            [
                'f_name' => 'María',    'l_name' => 'González',
                'email'  => 'maria@demo.zarpya.com',
                'phone'  => '+50499001001',
                'plan'   => 'free',
            ],
            // Plan Plus
            [
                'f_name' => 'José',     'l_name' => 'Martínez',
                'email'  => 'jose@demo.zarpya.com',
                'phone'  => '+50499001002',
                'plan'   => 'plus',
            ],
            // Plan Premium
            [
                'f_name' => 'Ana',      'l_name' => 'Rodríguez',
                'email'  => 'ana@demo.zarpya.com',
                'phone'  => '+50499001003',
                'plan'   => 'premium',
            ],
        ];

        foreach ($customers as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'f_name'            => $data['f_name'],
                    'l_name'            => $data['l_name'],
                    'phone'             => $data['phone'],
                    'password'          => Hash::make('Demo2026!'),
                    'zone_id'           => self::ZONE_ID,
                    'status'            => 1,
                    'is_phone_verified' => 1,
                    'login_medium'      => 'manual',
                ]
            );

            // Asignar suscripción si no es free
            if ($data['plan'] !== 'free') {
                \App\Models\CustomerSubscription::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'type'       => $data['plan'],
                        'expires_at' => now()->addYear(),
                        'status'     => 1,
                    ]
                );
            }
        }

        $this->command->info('  → Clientes OK (Free / Plus / Premium)');
    }

    // ── 4. VENDORS / NEGOCIOS ────────────────────────────────────
    private function seedVendors(): void
    {
        $plans = [
            'pionero' => SubscriptionPackage::where('founder_type', 'pionero')->first(),
            'elite'   => SubscriptionPackage::where('founder_type', 'elite')->first(),
            'boost'   => SubscriptionPackage::where('founder_type', 'boost')->first(),
            'standard'=> SubscriptionPackage::where('founder_type', 'standard')->first(),
        ];

        $vendors = [
            [
                'f_name' => 'Roberto',  'l_name' => 'Flores',
                'email'  => 'pionero@demo.zarpya.com',
                'phone'  => '+50488002001',
                'store'  => 'Baleadas Don Roberto',
                'module' => 2,   // Restaurantes
                'plan'   => 'pionero',
                'commission' => 14,
            ],
            [
                'f_name' => 'Lucía',    'l_name' => 'Hernández',
                'email'  => 'elite@demo.zarpya.com',
                'phone'  => '+50488002002',
                'store'  => 'Farmacia San Lucas',
                'module' => 6,   // Farmacias
                'plan'   => 'elite',
                'commission' => 10,
            ],
            [
                'f_name' => 'Miguel',   'l_name' => 'Torres',
                'email'  => 'boost@demo.zarpya.com',
                'phone'  => '+50488002003',
                'store'  => 'Supermercado El Ahorro',
                'module' => 3,   // Supermercado
                'plan'   => 'boost',
                'commission' => 12,
            ],
            [
                'f_name' => 'Sandra',   'l_name' => 'López',
                'email'  => 'estandar@demo.zarpya.com',
                'phone'  => '+50488002004',
                'store'  => 'Pizzería La Colonia',
                'module' => 8,   // Comida Rápida
                'plan'   => 'standard',
                'commission' => 18,
            ],
        ];

        foreach ($vendors as $data) {
            $vendor = Vendor::firstOrCreate(
                ['email' => $data['email']],
                [
                    'f_name'   => $data['f_name'],
                    'l_name'   => $data['l_name'],
                    'phone'    => $data['phone'],
                    'password' => Hash::make('Demo2026!'),
                    'status'   => 1,
                ]
            );

            $plan = $plans[$data['plan']];

            $store = Store::firstOrCreate(
                ['vendor_id' => $vendor->id, 'module_id' => $data['module']],
                [
                    'name'                => $data['store'],
                    'phone'               => $data['phone'],
                    'email'               => $data['email'],
                    'latitude'            => 14.0818,
                    'longitude'           => -87.2068,
                    'address'             => 'Tegucigalpa, Honduras',
                    'zone_id'             => self::ZONE_ID,
                    'status'              => 1,
                    'active'              => 1,
                    'comission'           => $data['commission'],
                    'store_business_model'=> 'subscription',
                    'package_id'          => $plan?->id,
                    'founder_plan'        => $data['plan'],
                    'founder_badge'       => $plan?->badge_label,
                    'founder_active'      => $data['plan'] !== 'standard',
                    'minimum_order'       => 50,
                    'delivery_time'       => '20-40 min',
                    'minimum_shipping_charge' => 25,
                    'per_km_shipping_charge'  => 8,
                    'maximum_shipping_charge' => 80,
                    'schedule_order'      => 1,
                    'delivery'            => 1,
                    'take_away'           => 1,
                    'reviews_section'     => 1,
                    'item_section'        => 1,
                    'slug'                => \Illuminate\Support\Str::slug($data['store']) . '-' . rand(100,999),
                ]
            );

            // Crear StoreSubscription (el observer cargará créditos si es Boost)
            if ($plan) {
                StoreSubscription::firstOrCreate(
                    ['store_id' => $store->id],
                    [
                        'package_id'    => $plan->id,
                        'expiry_date'   => now()->addYears(100)->toDateString(),
                        'validity'      => $plan->validity,
                        'max_order'     => $plan->max_order,
                        'max_product'   => $plan->max_product,
                        'pos'           => $plan->pos,
                        'mobile_app'    => $plan->mobile_app,
                        'chat'          => $plan->chat,
                        'review'        => $plan->review,
                        'self_delivery' => $plan->self_delivery,
                        'status'        => 1,
                        'is_trial'      => 0,
                    ]
                );

                // Incrementar cupos usados del plan fundador
                if ($data['plan'] !== 'standard' && $plan->is_founder) {
                    $plan->increment('used_slots');
                }
            }
        }

        $this->command->info('  → Vendors/Negocios OK (Pionero / Elite / Boost / Estándar)');
    }

    // ── 5. ZARPEROS (REPARTIDORES) ───────────────────────────────
    private function seedDeliveryMen(): void
    {
        $levels = DeliverymanLevel::orderBy('sort_order')->get()->keyBy('slug');

        $deliveryMen = [
            [
                'f_name' => 'Kevin',    'l_name' => 'Reyes',
                'email'  => 'zarpero.base@demo.zarpya.com',
                'phone'  => '+50499003001',
                'level'  => 'standard',
                'note'   => '🟢 Zarpero Base',
            ],
            [
                'f_name' => 'Diego',    'l_name' => 'Mejía',
                'email'  => 'zarpero.pro@demo.zarpya.com',
                'phone'  => '+50499003002',
                'level'  => 'pro',
                'note'   => '🔵 Zarpero Pro',
            ],
            [
                'f_name' => 'Fernanda', 'l_name' => 'Castillo',
                'email'  => 'zarpero.elite@demo.zarpya.com',
                'phone'  => '+50499003003',
                'level'  => 'elite',
                'note'   => '🟣 Zarpero Elite',
            ],
        ];

        foreach ($deliveryMen as $data) {
            $level = $levels[$data['level']] ?? null;

            DeliveryMan::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'f_name'             => $data['f_name'],
                    'l_name'             => $data['l_name'],
                    'email'              => $data['email'],
                    'password'           => Hash::make('Demo2026!'),
                    'zone_id'            => self::ZONE_ID,
                    'vehicle_id'         => self::VEHICLE_ID,
                    'status'             => 1,
                    'active'             => 1,
                    'earning'            => 1,
                    'type'               => 'zone_wise',
                    'application_status' => 'approved',
                    'identity_type'      => 'nid',
                    'identity_number'    => '0801' . rand(1990, 2005) . rand(10000, 99999),
                    'identity_image'     => json_encode([]),
                    'level_id'           => $level?->id,
                ]
            );
        }

        $this->command->info('  → Zarperos OK (Base / Pro / Elite)');
    }

    // ── 6. CONDUCTORES DE TAXI ───────────────────────────────────
    private function seedTaxiDrivers(): void
    {
        $drivers = [
            [
                'f_name'        => 'Héctor',   'l_name' => 'Aguilar',
                'phone'         => '+50499004001',
                'vehicle_type'  => 'standard',
                'license_plate' => 'HND-1234',
                'note'          => 'Taxi Standard',
            ],
            [
                'f_name'        => 'Patricia', 'l_name' => 'Soto',
                'phone'         => '+50499004002',
                'vehicle_type'  => 'premium',
                'license_plate' => 'HND-5678',
                'note'          => 'Taxi Premium',
            ],
            [
                'f_name'        => 'Omar',     'l_name' => 'Vásquez',
                'phone'         => '+50499004003',
                'vehicle_type'  => 'moto',
                'license_plate' => 'HND-9012',
                'note'          => 'Taxi Moto',
            ],
        ];

        foreach ($drivers as $data) {
            TaxiDriver::firstOrCreate(
                ['phone' => $data['phone']],
                [
                    'f_name'             => $data['f_name'],
                    'l_name'             => $data['l_name'],
                    'password'           => Hash::make('Demo2026!'),
                    'zone_id'            => self::ZONE_ID,
                    'vehicle_type'       => $data['vehicle_type'],
                    'license_plate'      => $data['license_plate'],
                    'license_number'     => 'LIC-' . rand(100000, 999999),
                    'status'             => 1,
                    'active'             => 1,
                    'available'          => 1,
                    'application_status' => 'approved',
                ]
            );
        }

        $this->command->info('  → Conductores de Taxi OK (Standard / Premium / Moto)');
    }

    // ── 7. CATEGORÍAS DE SERVICIO ────────────────────────────────
    private function seedServiceCategories(): void
    {
        $categories = [
            ['slug' => 'fontaneria',   'name' => 'Fontanería',    'icon' => '🔧', 'platform_commission' => 15.00],
            ['slug' => 'electricidad', 'name' => 'Electricidad',  'icon' => '⚡', 'platform_commission' => 15.00],
            ['slug' => 'limpieza',     'name' => 'Limpieza',      'icon' => '🧹', 'platform_commission' => 12.00],
            ['slug' => 'mecanica',     'name' => 'Mecánica',      'icon' => '🚗', 'platform_commission' => 18.00],
            ['slug' => 'computadoras', 'name' => 'Computadoras',  'icon' => '💻', 'platform_commission' => 15.00],
        ];

        foreach ($categories as $i => $cat) {
            ServiceCategory::firstOrCreate(
                ['slug' => $cat['slug']],
                array_merge($cat, ['status' => true, 'sort_order' => $i + 1])
            );
        }

        $this->command->info('  → Categorías de Servicio OK');
    }

    // ── 8. PROVEEDORES DE SERVICIO ───────────────────────────────
    private function seedServiceProviders(): void
    {
        $categories = ServiceCategory::whereIn('slug', ['fontaneria', 'electricidad', 'limpieza'])->get()->keyBy('slug');

        $providers = [
            [
                'f_name'        => 'Ramón',   'l_name' => 'Pineda',
                'email'         => 'fontanero@demo.zarpya.com',
                'phone'         => '+50499005001',
                'business_name' => 'Fontanería Pineda',
                'category'      => 'fontaneria',
                'hourly_rate'   => 250.00,
                'status'        => 'active',
                'note'          => 'Proveedor Fontanería',
            ],
            [
                'f_name'        => 'Ingrid',  'l_name' => 'Morales',
                'email'         => 'electricista@demo.zarpya.com',
                'phone'         => '+50499005002',
                'business_name' => 'Electricidad Morales',
                'category'      => 'electricidad',
                'hourly_rate'   => 300.00,
                'status'        => 'active',
                'note'          => 'Proveedor Electricidad',
            ],
            [
                'f_name'        => 'Claudia', 'l_name' => 'Paz',
                'email'         => 'limpieza@demo.zarpya.com',
                'phone'         => '+50499005003',
                'business_name' => 'Limpieza Express Paz',
                'category'      => 'limpieza',
                'hourly_rate'   => 180.00,
                'status'        => 'pending',
                'note'          => 'Proveedor Limpieza (pendiente)',
            ],
        ];

        foreach ($providers as $data) {
            // Crear usuario base para el proveedor
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'f_name'            => $data['f_name'],
                    'l_name'            => $data['l_name'],
                    'phone'             => $data['phone'],
                    'password'          => Hash::make('Demo2026!'),
                    'zone_id'           => self::ZONE_ID,
                    'status'            => 1,
                    'is_phone_verified' => 1,
                    'login_medium'      => 'manual',
                ]
            );

            $category = $categories[$data['category']] ?? null;

            ServiceProvider::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'category_id'   => $category?->id,
                    'zone_id'       => self::ZONE_ID,
                    'business_name' => $data['business_name'],
                    'phone'         => $data['phone'],
                    'description'   => 'Proveedor de servicios en Tegucigalpa. Experiencia comprobada.',
                    'hourly_rate'   => $data['hourly_rate'],
                    'status'        => $data['status'],
                    'verified'      => $data['status'] === 'active',
                    'avg_rating'    => $data['status'] === 'active' ? round(rand(42, 50) / 10, 1) : 0,
                    'total_reviews' => $data['status'] === 'active' ? rand(5, 30) : 0,
                    'total_jobs'    => $data['status'] === 'active' ? rand(10, 80) : 0,
                    'lat'           => 14.0818 + (rand(-100, 100) / 10000),
                    'lng'           => -87.2068 + (rand(-100, 100) / 10000),
                ]
            );
        }

        $this->command->info('  → Proveedores de Servicio OK (Fontanería / Electricidad / Limpieza)');
    }

    // ── IMPRIMIR CREDENCIALES ────────────────────────────────────
    private function printCredentials(): void
    {
        $this->command->newLine();
        $this->command->line('╔══════════════════════════════════════════════════════════════╗');
        $this->command->line('║              CREDENCIALES DE PERFILES DE EJEMPLO             ║');
        $this->command->line('╠══════════════════════════════════════════════════════════════╣');

        $this->command->line('║  ADMINISTRADORES                                             ║');
        $this->command->line('║  Admin Maestro    admin@zarpya.com          Zarpya2026!      ║');
        $this->command->line('║  Empleado         operaciones@zarpya.com    Operaciones2026! ║');
        $this->command->line('╠══════════════════════════════════════════════════════════════╣');

        $this->command->line('║  CLIENTES (app móvil)                                        ║');
        $this->command->line('║  Free             maria@demo.zarpya.com     Demo2026!        ║');
        $this->command->line('║  Plus             jose@demo.zarpya.com      Demo2026!        ║');
        $this->command->line('║  Premium          ana@demo.zarpya.com       Demo2026!        ║');
        $this->command->line('╠══════════════════════════════════════════════════════════════╣');

        $this->command->line('║  NEGOCIOS / VENDORS (panel vendor)                           ║');
        $this->command->line('║  ⭐ Pionero 14%   pionero@demo.zarpya.com   Demo2026!        ║');
        $this->command->line('║  🏆 Elite 10%     elite@demo.zarpya.com     Demo2026!        ║');
        $this->command->line('║  🚀 Boost 12%     boost@demo.zarpya.com     Demo2026!        ║');
        $this->command->line('║  Estándar 18%     estandar@demo.zarpya.com  Demo2026!        ║');
        $this->command->line('╠══════════════════════════════════════════════════════════════╣');

        $this->command->line('║  ZARPEROS / REPARTIDORES (app repartidor)                    ║');
        $this->command->line('║  🟢 Base          +50499003001               Demo2026!       ║');
        $this->command->line('║  🔵 Pro           +50499003002               Demo2026!       ║');
        $this->command->line('║  🟣 Elite         +50499003003               Demo2026!       ║');
        $this->command->line('╠══════════════════════════════════════════════════════════════╣');

        $this->command->line('║  CONDUCTORES DE TAXI (app taxi)                              ║');
        $this->command->line('║  Standard         +50499004001               Demo2026!       ║');
        $this->command->line('║  Premium          +50499004002               Demo2026!       ║');
        $this->command->line('║  Moto             +50499004003               Demo2026!       ║');
        $this->command->line('╠══════════════════════════════════════════════════════════════╣');

        $this->command->line('║  PROVEEDORES DE SERVICIO (app servicios)                     ║');
        $this->command->line('║  Fontanería       fontanero@demo.zarpya.com  Demo2026!       ║');
        $this->command->line('║  Electricidad     electricista@demo.zarpya.com Demo2026!     ║');
        $this->command->line('║  Limpieza         limpieza@demo.zarpya.com   Demo2026!       ║');
        $this->command->line('╚══════════════════════════════════════════════════════════════╝');
    }
}
