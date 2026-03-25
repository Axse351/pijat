<?php

namespace Database\Seeders;

use App\Models\Program;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            [
                'nama_program' => 'Promo New Member',
                'description' => 'Diskon 20% untuk member baru',
                'discount_type' => 'percent',
                'discount_value' => 20,
                'max_discount' => 100000,
                'min_transaction' => 100000,
                'is_active' => true,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(3), // BENAR
            ],
            [
                'nama_program' => 'Promo Hemat',
                'description' => 'Potongan Rp 50.000',
                'discount_type' => 'nominal',
                'discount_value' => 50000,
                'max_discount' => null,
                'min_transaction' => 200000,
                'is_active' => true,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
            ],
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
