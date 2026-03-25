<?php

namespace Database\Seeders;

use App\Models\Therapist;
use Illuminate\Database\Seeder;

class TherapistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Membuat 50+ terapis dengan komisi FLAT 25.000 per pijat
     */
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
            'Ibu Siti', 'Ibu Dewi', 'Ibu Rini', 'Ibu Ani', 'Ibu Erna',
            'Ibu Hana', 'Ibu Yuni', 'Ibu Lisa', 'Ibu Mira', 'Ibu Nita',
            'Ibu Rosa', 'Ibu Sarah', 'Ibu Tina', 'Ibu Vina', 'Ibu Wilis',
            'Ibu Xenia', 'Ibu Yani', 'Ibu Zana', 'Ibu Bella', 'Ibu Citra',
            'Ibu Diana', 'Ibu Elsa', 'Ibu Fiona', 'Ibu Gina', 'Ibu Hesti',
            'Ibu Ina', 'Ibu Jasmine', 'Ibu Kathy', 'Ibu Linda', 'Ibu Maya',
            'Ibu Nadia', 'Ibu Olivia', 'Ibu Putri', 'Ibu Queena', 'Ibu Rita',
            'Ibu Sofia', 'Ibu Tania', 'Ibu Ulfa', 'Ibu Valerie', 'Ibu Windy',
            'Ibu Xenia', 'Ibu Yuki', 'Ibu Zahra', 'Ibu Agnes', 'Ibu Berta',
            'Ibu Catrin', 'Ibu Dina', 'Ibu Eka', 'Ibu Fatin', 'Ibu Giselle',
        ];

        $therapists = [];
        $basePhone = 81234567000;

        // Buat 50 terapis
        for ($i = 0; $i < 50; $i++) {
            $isActive = $i < 45; // 45 aktif, 5 nonaktif

            $therapists[] = [
                'name' => $firstNames[$i],
                'specialty' => $specialties[$i % count($specialties)],
                'phone' => '0' . ($basePhone + $i), // Phone unik untuk setiap terapis
                'commission_percent' => 25, // 25% - ini akan disimpan, tapi gunakan flat Rp 25k di komisi calculation
                'photo' => null,
                'is_active' => $isActive,
            ];
        }

        foreach ($therapists as $therapist) {
            Therapist::create($therapist);
        }
    }
}
