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
        Schema::create('atk_opex', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_purchase_id')->constrained('atk_purchases')->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->string('opex_category')->default('atk_purchase'); // untuk kategori pengeluaran
            $table->dateTime('recorded_date');
            $table->string('status')->default('recorded'); // recorded, reversed, adjusted
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('recorded_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_opexes');
    }
};
