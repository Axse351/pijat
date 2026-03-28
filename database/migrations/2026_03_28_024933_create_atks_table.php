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
         Schema::create('atks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('atk_category_id')->constrained('atk_categories')->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('last_purchase_price', 12, 2)->nullable();
            $table->timestamps();
            $table->index('code');
            $table->index('atk_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('atks');
    }
};
