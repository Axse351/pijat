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
        Schema::create('therapist_leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('therapist_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('type', ['sakit', 'pribadi', 'cuti', 'izin_khusus'])->default('pribadi');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->text('approval_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('therapist_id')->references('id')->on('therapists')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            $table->index('therapist_id');
            $table->index('status');
            $table->index('start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::dropIfExists('therapist_leave_requests');
    }
};
