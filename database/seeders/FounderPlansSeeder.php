<?php

namespace Database\Seeders;

use App\Models\SubscriptionPackage;
use Illuminate\Database\Seeder;

class FounderPlansSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            // ── ⭐ PIONERO ────────────────────────────────────────────────
            [
                'package_name'          => '⭐ Pionero',
                'price'                 => 0.00,
                'validity'              => 36500,       // permanente (100 años)
                'max_order'             => 'unlimited',
                'max_product'           => 'unlimited',
                'pos'                   => true,
                'mobile_app'            => true,
                'chat'                  => true,
                'review'                => true,
                'self_delivery'         => false,
                'status'                => true,
                'default'               => false,
                'colour'                => '#F59E0B',   // amber
                'text'                  => 'Sin costo de entrada. Comisión 14% por pedido para siempre. Badge Pionero visible en la app. Solo 100 cupos disponibles.',
                'module_type'           => 'all',
                // Campos fundadores
                'is_founder'            => true,
                'founder_type'          => 'pionero',
                'max_slots'             => 100,
                'used_slots'            => 0,
                'slots_open'            => true,
                'commission_percent'    => 14.00,
                'payment_type'          => 'free',
                'deposit_amount'        => null,
                'deposit_refund_months' => null,
                'banner_days'           => null,
                'promo_credits'         => 0.00,
                'badge_label'           => 'Pionero',
                'badge_color'           => '#F59E0B',
                'vip_support'           => false,
            ],

            // ── 🏆 ELITE ─────────────────────────────────────────────────
            [
                'package_name'          => '🏆 Elite',
                'price'                 => 1500.00,
                'validity'              => 36500,
                'max_order'             => 'unlimited',
                'max_product'           => 'unlimited',
                'pos'                   => true,
                'mobile_app'            => true,
                'chat'                  => true,
                'review'                => true,
                'self_delivery'         => false,
                'status'                => true,
                'default'               => false,
                'colour'                => '#7C3AED',   // purple
                'text'                  => 'Pago único L 1,500 sin renovaciones. Comisión 10% por pedido para siempre. Banner destacado 90 días. Soporte VIP. Solo 30 cupos.',
                'module_type'           => 'all',
                'is_founder'            => true,
                'founder_type'          => 'elite',
                'max_slots'             => 30,
                'used_slots'            => 0,
                'slots_open'            => true,
                'commission_percent'    => 10.00,
                'payment_type'          => 'one_time',
                'deposit_amount'        => null,
                'deposit_refund_months' => null,
                'banner_days'           => 90,
                'promo_credits'         => 0.00,
                'badge_label'           => 'Elite',
                'badge_color'           => '#7C3AED',
                'vip_support'           => true,
            ],

            // ── 🚀 BOOST ─────────────────────────────────────────────────
            [
                'package_name'          => '🚀 Boost',
                'price'                 => 2500.00,
                'validity'              => 36500,
                'max_order'             => 'unlimited',
                'max_product'           => 'unlimited',
                'pos'                   => true,
                'mobile_app'            => true,
                'chat'                  => true,
                'review'                => true,
                'self_delivery'         => false,
                'status'                => true,
                'default'               => false,
                'colour'                => '#0EA5E9',   // sky blue
                'text'                  => 'Depósito reembolsable L 2,500 en 12 meses. Comisión 12% por pedido. L 3,750 en créditos de promoción dentro de la app. Solo 20 cupos.',
                'module_type'           => 'all',
                'is_founder'            => true,
                'founder_type'          => 'boost',
                'max_slots'             => 20,
                'used_slots'            => 0,
                'slots_open'            => true,
                'commission_percent'    => 12.00,
                'payment_type'          => 'deposit',
                'deposit_amount'        => 2500.00,
                'deposit_refund_months' => 12,
                'banner_days'           => null,
                'promo_credits'         => 3750.00,
                'badge_label'           => 'Boost',
                'badge_color'           => '#0EA5E9',
                'vip_support'           => false,
            ],

            // ── ESTÁNDAR (post-lanzamiento) ───────────────────────────────
            [
                'package_name'          => 'Estándar',
                'price'                 => 0.00,
                'validity'              => 30,          // mensual
                'max_order'             => 'unlimited',
                'max_product'           => 'unlimited',
                'pos'                   => true,
                'mobile_app'            => true,
                'chat'                  => false,
                'review'                => true,
                'self_delivery'         => false,
                'status'                => true,
                'default'               => true,        // plan por defecto
                'colour'                => '#6B7280',   // gray
                'text'                  => 'Plan post-lanzamiento. Operación básica. Comisión 18% por pedido. Sin badge ni beneficios especiales. Cupos ilimitados.',
                'module_type'           => 'all',
                'is_founder'            => false,
                'founder_type'          => 'standard',
                'max_slots'             => null,        // ilimitado
                'used_slots'            => 0,
                'slots_open'            => true,
                'commission_percent'    => 18.00,
                'payment_type'          => 'free',
                'deposit_amount'        => null,
                'deposit_refund_months' => null,
                'banner_days'           => null,
                'promo_credits'         => 0.00,
                'badge_label'           => null,
                'badge_color'           => null,
                'vip_support'           => false,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPackage::updateOrCreate(
                ['founder_type' => $plan['founder_type'], 'module_type' => 'all'],
                $plan
            );
        }

        $this->command->info('✅ Planes Fundadores Zarpya creados: Pionero (100) · Elite (30) · Boost (20) · Estándar (∞)');
    }
}
