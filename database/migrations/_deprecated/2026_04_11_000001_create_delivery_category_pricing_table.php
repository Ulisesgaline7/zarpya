<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_category_pricing', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->nullable()->constrained('modules')->onDelete('set null');
            $table->string('category_slug', 80)->unique(); // e.g. 'restaurants', 'pharmacies'
            $table->string('category_name', 120);

            // Formula: Precio = (Base + Km × Tarifa_km) × Multiplicador
            $table->decimal('base_price', 8, 2)->default(25.00);      // L base
            $table->decimal('price_per_km', 8, 2)->default(8.00);     // L por km
            $table->decimal('commission_percent', 5, 2)->default(15.00); // % comision a Zarpya

            // Distribucion por envio (porcentajes)
            $table->decimal('driver_percent', 5, 2)->default(88.00);   // 88–93%
            $table->decimal('platform_percent', 5, 2)->default(10.00); // 10% Zarpya
            $table->decimal('insurance_percent', 5, 2)->default(2.00); // 2% fondo seguro

            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->index('category_slug');
            $table->index('module_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_category_pricing');
    }
};
