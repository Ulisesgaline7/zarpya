<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('taxi_drivers', function (Blueprint $table) {
            $table->id();
            $table->string('f_name');
            $table->string('l_name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('auth_token', 100)->nullable();

            $table->foreignId('zone_id')->nullable()->constrained('zones')->onDelete('set null');
            $table->string('vehicle_type', 40)->default('standard'); // standard, premium, moto
            $table->string('license_plate', 20)->nullable();
            $table->string('license_number', 50)->nullable();

            $table->string('image')->nullable();
            $table->json('identity_image')->nullable();

            $table->boolean('status')->default(true);
            $table->tinyInteger('active')->default(0);
            $table->tinyInteger('available')->default(0);
            $table->decimal('earning', 12, 2)->default(0);
            $table->string('application_status', 30)->default('pending'); // pending, approved, denied

            $table->decimal('current_lat', 10, 7)->nullable();
            $table->decimal('current_lng', 10, 7)->nullable();

            $table->rememberToken();
            $table->timestamps();

            $table->index(['zone_id', 'active', 'available']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('taxi_drivers');
    }
};
