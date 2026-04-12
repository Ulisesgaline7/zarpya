<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->decimal('base_price', 10, 2)->default(25)->nullable()->after('description');
            $table->decimal('price_per_km', 10, 2)->default(8)->nullable()->after('base_price');
            $table->decimal('commission_percent', 5, 2)->default(15)->nullable()->after('price_per_km');
        });
    }

    public function down(): void
    {
        Schema::table('modules', function (Blueprint $table) {
            $table->dropColumn(['base_price', 'price_per_km', 'commission_percent']);
        });
    }
};