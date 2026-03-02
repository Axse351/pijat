<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Simpan jadwal asli pertama kali booking dibuat
            // Tidak berubah meski dijadwal ulang berkali-kali
            $table->datetime('original_scheduled_at')->nullable()->after('scheduled_at');

            // Flag: apakah booking ini pernah dijadwal ulang
            $table->boolean('is_rescheduled')->default(false)->after('original_scheduled_at');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['original_scheduled_at', 'is_rescheduled']);
        });
    }
};
