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
       Schema::create('atk_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_id')->constrained('atks')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2);
            $table->decimal('total_price', 12, 2);
            $table->dateTime('purchase_date');
            $table->string('status')->default('completed'); // completed, pending, cancelled
            $table->text('notes')->nullable();
            $table->string('receipt_number')->nullable();
            $table->timestamps();

            $table->index('purchase_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_purchases');
    }
};
