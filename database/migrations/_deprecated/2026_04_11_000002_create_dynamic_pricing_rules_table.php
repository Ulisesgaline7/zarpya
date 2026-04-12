<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dynamic_pricing_rules', function (Blueprint $table) {
            $table->id();
            // Tipos: rain, night, rush_hour, high_demand, weekend
            $table->string('rule_type', 40)->unique();
            $table->string('label', 80); // Etiqueta legible
            $table->decimal('multiplier', 4, 2)->default(1.00); // ej. 1.30

            // Ventanas de tiempo (null = aplica todo el dia)
            $table->time('time_start')->nullable(); // ej. 19:00:00
            $table->time('time_end')->nullable();   // ej. 21:00:00

            // Dias de la semana (JSON array: [0=Domingo..6=Sabado], null = todos)
            $table->json('days_of_week')->nullable();

            // Para alta demanda: umbral de pedidos activos en zona
            $table->unsignedInteger('demand_threshold')->nullable();

            // Multiplicador minimo y maximo para alta demanda dinamica
            $table->decimal('multiplier_min', 4, 2)->nullable();
            $table->decimal('multiplier_max', 4, 2)->nullable();

            $table->boolean('status')->default(true);
            $table->integer('priority')->default(0); // Mayor prioridad gana si se solapan

            $table->timestamps();
            $table->index('rule_type');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dynamic_pricing_rules');
    }
};
