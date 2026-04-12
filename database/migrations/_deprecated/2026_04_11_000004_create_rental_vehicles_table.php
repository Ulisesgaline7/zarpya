<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Flota disponible para renta
        Schema::create('rental_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');
            $table->string('type', 40);          // car, moto, pickup, van
            $table->string('brand', 80)->nullable();
            $table->string('model', 80)->nullable();
            $table->string('plate', 20)->nullable();
            $table->string('color', 40)->nullable();
            $table->string('image')->nullable();

            // Tarifas
            $table->decimal('price_per_hour', 8, 2)->default(0);
            $table->decimal('price_per_day', 8, 2)->default(0);
            $table->decimal('deposit', 8, 2)->default(0); // deposito reembolsable

            $table->integer('seats')->default(4);
            $table->boolean('with_driver')->default(false);
            $table->enum('status', ['available', 'rented', 'maintenance', 'inactive'])->default('available');

            // Propietario (puede ser externo o Zarpya)
            $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('owner_percent', 5, 2)->default(80.00); // % al propietario
            $table->decimal('platform_percent', 5, 2)->default(20.00);

            $table->timestamps();
            $table->softDeletes();
            $table->index(['zone_id', 'status']);
        });

        // Reservas de renta
        Schema::create('rental_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('rental_vehicles')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained('delivery_men')->onDelete('set null');

            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->decimal('total_price', 10, 2);
            $table->decimal('deposit_paid', 8, 2)->default(0);
            $table->string('pickup_address')->nullable();
            $table->string('dropoff_address')->nullable();

            $table->enum('status', ['pending', 'confirmed', 'active', 'completed', 'cancelled'])->default('pending');
            $table->string('payment_method', 40)->nullable();
            $table->string('payment_ref', 100)->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['vehicle_id', 'status']);
            $table->index(['customer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rental_bookings');
        Schema::dropIfExists('rental_vehicles');
    }
};
