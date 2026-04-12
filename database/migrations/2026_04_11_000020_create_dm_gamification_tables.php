<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bonificaciones ganadas por repartidor
        Schema::create('dm_bonuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id')->constrained('delivery_men')->onDelete('cascade');
            $table->string('type', 60); // volume, peak_hour, rating, acceptance, streak
            $table->string('label', 120);
            $table->decimal('amount', 10, 2);
            $table->string('period', 20)->nullable(); // 2026-W15, 2026-04, etc.
            $table->boolean('paid')->default(false);
            $table->timestamp('earned_at')->useCurrent();
            $table->timestamps();
            $table->index(['delivery_man_id', 'paid']);
            $table->index(['delivery_man_id', 'type', 'period']);
        });

        // Logros / Medallas
        Schema::create('dm_achievements', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name', 120);
            $table->string('description', 255);
            $table->string('icon', 10)->default('🏅'); // emoji
            $table->string('color', 20)->default('gold'); // gold, silver, bronze, purple
            $table->string('condition_type', 40); // deliveries, rating, streak, acceptance, no_cancel
            $table->integer('condition_value');   // umbral numérico
            $table->integer('xp_reward')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Logros desbloqueados por repartidor
        Schema::create('dm_achievement_unlocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id')->constrained('delivery_men')->onDelete('cascade');
            $table->foreignId('achievement_id')->constrained('dm_achievements')->onDelete('cascade');
            $table->timestamp('unlocked_at')->useCurrent();
            $table->unique(['delivery_man_id', 'achievement_id']);
        });

        // XP y racha diaria
        Schema::create('dm_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_man_id')->constrained('delivery_men')->onDelete('cascade')->unique();
            $table->integer('xp')->default(0);
            $table->integer('streak_days')->default(0);       // días consecutivos activos
            $table->date('last_active_date')->nullable();
            $table->integer('monthly_deliveries')->default(0); // reset mensual
            $table->string('current_month', 7)->nullable();    // 2026-04
            $table->decimal('weekly_bonus_earned', 10, 2)->default(0);
            $table->string('current_week', 8)->nullable();     // 2026-W15
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dm_stats');
        Schema::dropIfExists('dm_achievement_unlocks');
        Schema::dropIfExists('dm_achievements');
        Schema::dropIfExists('dm_bonuses');
    }
};
