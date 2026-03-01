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
        Schema::create('therapist_attendances', function (Blueprint $table) {
            $table->id();

            // Foreign key ke therapists
            $table->foreignId('therapist_id')
                ->constrained('therapists')
                ->cascadeOnDelete();

            // Waktu check-in dan check-out
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();

            // Path foto untuk face recognition
            $table->string('check_in_image')->nullable();
            $table->string('check_out_image')->nullable();

            // Confidence score dari face recognition (0-1)
            $table->float('check_in_confidence')->nullable();
            $table->float('check_out_confidence')->nullable();

            // Status absensi
            $table->enum('status', ['present', 'late', 'absent'])->default('absent');

            // Catatan (opsional)
            $table->text('notes')->nullable();

            // Tanggal absensi (untuk grouping per hari)
            $table->date('attendance_date');

            // Timestamp
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['therapist_id', 'attendance_date']);

            // Unique constraint - 1 terapis hanya 1 record per hari
            $table->unique(['therapist_id', 'attendance_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_attendances');
    }
};
