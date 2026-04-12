<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveryman_levels', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();  // standard, pro, elite
            $table->string('name', 80);
            $table->text('description')->nullable();

            // Porcentaje del envio que recibe el repartidor
            $table->decimal('driver_percent', 5, 2)->default(88.00); // 88 / 91 / 93

            // Requisitos para subir de nivel
            $table->unsignedInteger('min_deliveries')->default(0);   // entregas completadas
            $table->decimal('min_rating', 3, 2)->default(0.00);      // calificacion minima
            $table->unsignedInteger('min_months_active')->default(0);

            // Beneficios adicionales (JSON: {"priority_orders": true, "bonus_weekends": 5})
            $table->json('benefits')->nullable();

            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Columna de nivel en deliverymen
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->foreignId('level_id')
                ->nullable()
                ->constrained('deliveryman_levels')
                ->onDelete('set null')
                ->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_men', function (Blueprint $table) {
            $table->dropConstrainedForeignId('level_id');
        });
        Schema::dropIfExists('deliveryman_levels');
    }
};
