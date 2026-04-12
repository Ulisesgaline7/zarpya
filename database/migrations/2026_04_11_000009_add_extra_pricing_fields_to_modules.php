<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->decimal('price_per_minute', 10, 2)->default(0)->nullable()->after('price_per_km');
            $table->decimal('minimum_fare', 10, 2)->default(0)->nullable()->after('price_per_minute');
            $table->decimal('deposit', 10, 2)->default(0)->nullable()->after('minimum_fare');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['price_per_minute', 'minimum_fare', 'deposit']);
        });
    }
};