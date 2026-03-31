<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->unsignedInteger('points')->default(0)
                ->comment('Poin aktif pelanggan saat ini');
            $table->unsignedInteger('total_points_earned')->default(0)
                ->comment('Total poin yang pernah diperoleh sepanjang masa');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['points', 'total_points_earned']);
        });
    }
};
