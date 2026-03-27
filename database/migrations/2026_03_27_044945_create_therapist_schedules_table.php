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
        Schema::create('therapist_schedules', function (Blueprint $table) {
            $table->id();

            // Foreign key ke therapists
            $table->foreignId('therapist_id')
                ->constrained('therapists')
                ->cascadeOnDelete();

            // Tanggal jadwal
            $table->date('schedule_date');

            // Hari dalam minggu (0=Sunday, 1=Monday, ..., 6=Saturday)
            $table->integer('day_of_week');

            // Status hari (working, off, sick, vacation, etc)
            $table->enum('status', ['working', 'off', 'sick', 'vacation', 'cuti_bersama'])
                ->default('working');

            // Jam kerja (jika status = working)
            $table->time('start_time')->nullable(); // Misal: 09:00
            $table->time('end_time')->nullable();   // Misal: 18:00

            // Jumlah jam kerja (auto calculated)
            $table->integer('working_hours')->nullable();

            // Catatan
            $table->text('notes')->nullable();

            // Dibuat oleh admin
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Timestamp
            $table->timestamps();

            // Index untuk query cepat
            $table->index(['therapist_id', 'schedule_date']);
            $table->index(['therapist_id', 'status']);
            $table->unique(['therapist_id', 'schedule_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('therapist_schedules');
    }
};
