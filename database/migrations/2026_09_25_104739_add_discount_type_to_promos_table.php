<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->enum('discount_type', ['percent', 'fixed'])->default('percent')->after('discount');
            $table->unsignedBigInteger('max_discount')->nullable()->after('discount_type');
        });
    }

    public function down(): void
    {
        Schema::table('promos', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'max_discount']);
        });
    }
};
