<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categorias de servicio: fontaneria, electricidad, limpieza, etc.
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->string('name', 120);
            $table->string('icon')->nullable();
            $table->decimal('platform_commission', 5, 2)->default(15.00); // % Zarpya
            $table->boolean('status')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Proveedores de servicio (fontaneros, electricistas, etc.)
        Schema::create('service_providers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');
            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');

            $table->string('business_name', 150)->nullable();
            $table->text('description')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('avatar')->nullable();
            $table->json('portfolio_images')->nullable(); // hasta 5 fotos

            // Calificaciones
            $table->decimal('avg_rating', 3, 2)->default(0);
            $table->unsignedInteger('total_reviews')->default(0);
            $table->unsignedInteger('total_jobs')->default(0);

            // Disponibilidad y tarifas
            $table->decimal('hourly_rate', 8, 2)->nullable();
            $table->decimal('fixed_rate', 8, 2)->nullable(); // si cobra precio fijo
            $table->json('availability_schedule')->nullable(); // {mon:[{from:'08:00',to:'18:00'}],...}

            $table->enum('status', ['pending', 'active', 'suspended', 'inactive'])->default('pending');
            $table->boolean('verified')->default(false);
            $table->boolean('featured')->default(false);

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->index(['category_id', 'status']);
            $table->index(['zone_id', 'status']);
        });

        // Solicitudes de servicio
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->nullable()->constrained('service_providers')->onDelete('set null');
            $table->foreignId('category_id')->constrained('service_categories')->onDelete('cascade');

            $table->text('description');
            $table->string('address');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->dateTime('scheduled_at')->nullable();

            $table->decimal('quoted_price', 10, 2)->nullable();
            $table->decimal('final_price', 10, 2)->nullable();
            $table->decimal('platform_fee', 8, 2)->nullable();
            $table->decimal('provider_earning', 10, 2)->nullable();

            $table->enum('status', [
                'open', 'quoted', 'accepted', 'in_progress', 'completed', 'cancelled', 'disputed'
            ])->default('open');

            $table->string('payment_method', 40)->nullable();
            $table->boolean('paid')->default(false);
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('review')->nullable();

            $table->timestamps();
            $table->index(['customer_id', 'status']);
            $table->index(['provider_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('service_providers');
        Schema::dropIfExists('service_categories');
    }
};
