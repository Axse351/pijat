<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom `method` awalnya enum('qris','cash') — tapi form pembayaran
     * sudah punya opsi 'transfer' dan 'debit' juga, jadi insert dengan
     * value tersebut ditolak MySQL (data truncated / gagal masuk).
     * Migration ini memperluas enum tanpa mengubah data yang sudah ada.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE payments
            MODIFY COLUMN method ENUM('qris', 'cash', 'transfer', 'debit') NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * PERHATIAN: rollback ini akan gagal (atau memaksa MySQL mengubah value
     * yang tidak dikenali jadi '') kalau sudah ada baris dengan method
     * 'transfer' atau 'debit'. Pastikan cek data dulu sebelum rollback
     * di production.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE payments
            MODIFY COLUMN method ENUM('qris', 'cash') NOT NULL
        ");
    }
};
