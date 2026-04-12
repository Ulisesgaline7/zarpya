<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Separa los conductores de taxi de los repartidores.
 * Cambia driver_id en taxi_rides para apuntar a taxi_drivers en lugar de delivery_men.
 *
 * NOTA: Si taxi_rides aún no existe (la migración _deprecated no se ejecutó),
 * esta migración no hace nada y la tabla se creará correctamente desde cero.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('taxi_rides')) {
            return; // Se creará con la FK correcta desde la migración original
        }

        Schema::table('taxi_rides', function (Blueprint $table) {
            // Eliminar FK antigua hacia delivery_men
            $table->dropForeign(['driver_id']);

            // Agregar FK nueva hacia taxi_drivers
            $table->foreign('driver_id')
                ->references('id')
                ->on('taxi_drivers')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('taxi_rides')) {
            return;
        }

        Schema::table('taxi_rides', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);

            $table->foreign('driver_id')
                ->references('id')
                ->on('delivery_men')
                ->onDelete('set null');
        });
    }
};
