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
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_program');
            $table->text('description')->nullable();
            $table->string('image')->nullable();

            // jenis diskon
            $table->enum('discount_type', ['percent', 'nominal']);

            // nilai diskon
            $table->decimal('discount_value', 10, 2);

            // maksimal potongan (optional)
            $table->decimal('max_discount', 10, 2)->nullable();

            // minimal transaksi
            $table->decimal('min_transaction', 10, 2)->nullable();

            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};
