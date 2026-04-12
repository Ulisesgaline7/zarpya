<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tarifas base del modulo taxi por zona
        Schema::create('taxi_zone_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->constrained('zones')->onDelete('cascade');
            $table->string('vehicle_type', 40)->default('standard'); // standard, premium, moto
            $table->decimal('base_fare', 8, 2)->default(30.00);      // L base al abordar
            $table->decimal('fare_per_km', 8, 2)->default(12.00);    // L por km
            $table->decimal('fare_per_min', 6, 2)->default(2.00);    // L por minuto espera
            $table->decimal('min_fare', 8, 2)->default(40.00);       // tarifa minima
            $table->decimal('platform_percent', 5, 2)->default(15.00);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unique(['zone_id', 'vehicle_type']);
        });

        // Viajes
        Schema::create('taxi_rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('taxi_drivers')->onDelete('set null');
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');

            $table->string('vehicle_type', 40)->default('standard');
            $table->string('pickup_address');
            $table->decimal('pickup_lat', 10, 7);
            $table->decimal('pickup_lng', 10, 7);
            $table->string('dropoff_address');
            $table->decimal('dropoff_lat', 10, 7);
            $table->decimal('dropoff_lng', 10, 7);

            $table->decimal('distance_km', 8, 3)->default(0);
            $table->unsignedInteger('duration_min')->default(0);
            $table->decimal('base_fare', 8, 2)->default(0);
            $table->decimal('distance_fare', 8, 2)->default(0);
            $table->decimal('dynamic_multiplier', 4, 2)->default(1.00);
            $table->decimal('total_fare', 10, 2)->default(0);
            $table->decimal('driver_earning', 10, 2)->default(0);
            $table->decimal('platform_earning', 10, 2)->default(0);

            $table->enum('status', [
                'searching', 'accepted', 'arriving', 'in_progress', 'completed', 'cancelled'
            ])->default('searching');

            $table->string('payment_method', 40)->nullable();
            $table->string('payment_ref', 100)->nullable();
            $table->boolean('paid')->default(false);

            $table->string('cancellation_reason')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['driver_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxi_rides');
        Schema::dropIfExists('taxi_zone_rates');
    }
};
