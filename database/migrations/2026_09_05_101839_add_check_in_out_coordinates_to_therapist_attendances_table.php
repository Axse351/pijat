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
        Schema::table('therapist_attendances', function (Blueprint $table) {
            // Koordinat & jarak saat check-in
            $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in_confidence');
            $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            $table->float('check_in_distance_meters')->nullable()->after('check_in_longitude');

            // Koordinat & jarak saat check-out
            $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out_confidence');
            $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            $table->float('check_out_distance_meters')->nullable()->after('check_out_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('therapist_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_latitude',
                'check_in_longitude',
                'check_in_distance_meters',
                'check_out_latitude',
                'check_out_longitude',
                'check_out_distance_meters',
            ]);
        });
    }
};
