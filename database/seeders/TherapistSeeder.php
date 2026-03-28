<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            'Pijat Tradisional',
            'Massage Relaksasi',
            'Refleksologi',
            'Pijat Shiatsu',
            'Spa Therapy',
            'Thai Massage',
            'Pijat Nyeri',
            'Aromatherapy',
            'Hot Stone Massage',
            'Swedish Massage',
        ];

        $firstNames = [
            'Lukman Arif',
            'Defara Yudha',
            'Rossi Pralitha',
            'Diva Putri',
            'Azhar Nur',
            'Putri Isnaeni'
        ];

        $basePhone = 81234567000;

        foreach ($firstNames as $i => $name) {
            $isActive = $i < count($firstNames) - 1; // contoh: terakhir nonaktif

            Therapist::create([
                'name' => $name,
                'specialty' => $specialties[$i % count($specialties)],
                'phone' => '0' . ($basePhone + $i),
                'commission_percent' => 25,
                'photo' => null,
                'is_active' => $isActive,
            ]);
        }
    }
}
