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
       Schema::create('atk_stock_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_id')->constrained('atks')->onDelete('cascade');
            $table->integer('quantity_before');
            $table->integer('quantity_after');
            $table->integer('quantity_change');
            $table->string('type'); // in, out, adjustment, return
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('atk_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atk_stock_histories');
    }
};
