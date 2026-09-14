<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('therapist_attendances', function (Blueprint $table) {
            $table->unsignedInteger('late_minutes')->default(0)->after('status');
            $table->unsignedInteger('denda_amount')->default(0)->after('late_minutes');
            $table->boolean('bonus_hadir_eligible')->default(true)->after('denda_amount');
        });
    }

    public function down(): void
    {
        Schema::table('therapist_attendances', function (Blueprint $table) {
            $table->dropColumn(['late_minutes', 'denda_amount', 'bonus_hadir_eligible']);
        });
    }
};
