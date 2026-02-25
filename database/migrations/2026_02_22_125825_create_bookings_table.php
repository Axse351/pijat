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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('therapist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->foreignId('promo_id')->constrained()->cascadeOnDelete();

            $table->enum('order_source', ['wa', 'walkin', 'web']);
            $table->dateTime('scheduled_at');

            $table->decimal('price', 12, 2); // sebelum discount
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('final_price', 12, 2);
            $table->text('notes')->nullable();

            $table->enum('status', ['pending', 'scheduled', 'completed', 'cancelled', 'ongoing'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
