<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Tambahkan kolom program_id setelah promo_id
            $table->unsignedBigInteger('program_id')->nullable()->after('promo_id');

            // Jika table programs sudah ada, tambahkan foreign key
            // Jika belum ada, uncomment setelah table programs dibuat
            // $table->foreign('program_id')->references('id')->on('programs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop foreign key jika ada
            // $table->dropForeign(['program_id']);

            // Drop kolom
            $table->dropColumn('program_id');
        });
    }
};
