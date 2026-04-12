<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->boolean('is_founder')->default(false)->after('module_type');
            $table->string('founder_type', 30)->nullable()->after('is_founder'); // pionero, elite, boost, standard
            $table->integer('max_slots')->nullable()->after('founder_type');     // cupos máximos (null = ilimitado)
            $table->integer('used_slots')->default(0)->after('max_slots');       // cupos usados
            $table->boolean('slots_open')->default(true)->after('used_slots');   // si aún acepta nuevos
            $table->decimal('commission_percent', 5, 2)->default(18.00)->after('slots_open'); // % comisión permanente
            $table->string('payment_type', 20)->default('free')->after('commission_percent'); // free, one_time, deposit
            $table->decimal('deposit_amount', 10, 2)->nullable()->after('payment_type');      // monto depósito reembolsable
            $table->integer('deposit_refund_months')->nullable()->after('deposit_amount');     // meses para reembolso
            $table->integer('banner_days')->nullable()->after('deposit_refund_months');        // días de banner destacado
            $table->decimal('promo_credits', 10, 2)->default(0)->after('banner_days');        // créditos de ads incluidos
            $table->string('badge_label', 50)->nullable()->after('promo_credits');             // etiqueta visible en app
            $table->string('badge_color', 20)->nullable()->after('badge_label');               // color del badge
            $table->boolean('vip_support')->default(false)->after('badge_color');
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->string('founder_plan', 30)->nullable()->after('package_id');  // pionero, elite, boost
            $table->string('founder_badge', 50)->nullable()->after('founder_plan');
            $table->boolean('founder_active')->default(false)->after('founder_badge');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_packages', function (Blueprint $table) {
            $table->dropColumn([
                'is_founder','founder_type','max_slots','used_slots','slots_open',
                'commission_percent','payment_type','deposit_amount','deposit_refund_months',
                'banner_days','promo_credits','badge_label','badge_color','vip_support',
            ]);
        });
        Schema::table('stores', function (Blueprint $table) {
            $table->dropColumn(['founder_plan','founder_badge','founder_active']);
        });
    }
};
