<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom pendukung komisi & cancel policy
     * Tidak mengubah kolom yang sudah ada — aman untuk production
     */
    public function up(): void
    {
        // ── BOOKINGS: tambah info sumber komisi & cancel ──────────────────
        Schema::table('bookings', function (Blueprint $table) {
            // Apakah terapis di-assign secara spesifik oleh customer?
            $table->boolean('is_specific_therapist')->default(false)->after('therapist_id');

            // Tipe komisi: 'standard' = 25%, 'program' = 30%
            $table->enum('commission_type', ['standard', 'program'])->default('standard')->after('is_specific_therapist');

            // Kalau cancel: catat alasan & waktu cancel
            $table->string('cancel_reason')->nullable()->after('notes');
            $table->timestamp('cancelled_at')->nullable()->after('cancel_reason');
        });

        // ── COMMISSIONS: tambah info cancel commission ────────────────────
        Schema::table('commissions', function (Blueprint $table) {
            // Apakah komisi ini berasal dari booking yang dibatalkan?
            $table->boolean('is_cancel_commission')->default(false)->after('is_paid');

            // Tipe: 'normal' = sesi selesai, 'cancel_forfeit' = uang hangus saat cancel
            $table->enum('commission_source', ['normal', 'cancel_forfeit'])->default('normal')->after('is_cancel_commission');

            // Catatan tambahan
            $table->string('notes')->nullable()->after('commission_source');

            // Tanggal bayar komisi
            $table->timestamp('paid_at')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_specific_therapist', 'commission_type', 'cancel_reason', 'cancelled_at']);
        });

        Schema::table('commissions', function (Blueprint $table) {
            $table->dropColumn(['is_cancel_commission', 'commission_source', 'notes', 'paid_at']);
        });
    }
};
