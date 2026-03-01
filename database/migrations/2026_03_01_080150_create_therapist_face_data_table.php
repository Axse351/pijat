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
        Schema::create('therapist_face_data', function (Blueprint $table) {
            // Primary Key
            $table->id();

            // Foreign Key ke therapists table (UNIQUE - 1 terapis 1 data wajah)
            $table->foreignId('therapist_id')
                ->unique()
                ->constrained('therapists')
                ->cascadeOnDelete();

            // JSON untuk menyimpan array embeddings wajah
            // Hasil dari face recognition (array of numbers)
            $table->json('face_embeddings');

            // Path ke foto referensi wajah terapis
            $table->string('reference_image');

            // Jumlah sampel foto wajah yang direkam
            // Semakin banyak sampel = semakin akurat
            $table->integer('samples_count')->default(1);

            // Status: verified, pending, failed
            // verified = siap untuk matching di absensi
            // pending = menunggu verifikasi manual
            // failed = gagal di verifikasi
            $table->enum('status', ['verified', 'pending', 'failed'])->default('pending');

            // Timestamp (created_at, updated_at)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_face_data');
    }
};
