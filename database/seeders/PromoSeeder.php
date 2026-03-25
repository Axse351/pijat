<?php

namespace Database\Seeders;

use App\Models\Promo;
use Illuminate\Database\Seeder;

class PromoSeeder extends Seeder
{
    public function run(): void
    {
        $promos = [
            [
                'code' => 'PEMULA10',
                'nama_promo' => 'Diskon 10% Pemula',
                'discount' => 10,
                'status' => 'aktif',
            ],
            [
                'code' => 'WEEKEND15',
                'nama_promo' => 'Diskon 15% Weekend',
                'discount' => 15,
                'status' => 'aktif',
            ],
            [
                'code' => 'FLAT25',
                'nama_promo' => 'Diskon 25rb',
                'discount' => 25000,
                'status' => 'aktif',
            ],
            [
                'code' => 'BELI3GRATIS1',
                'nama_promo' => 'Beli 3 Gratis 1',
                'discount' => 25,
                'status' => 'aktif',
            ],
            [
                'code' => 'MEMBER20',
                'nama_promo' => 'Diskon Member 20%',
                'discount' => 20,
                'status' => 'aktif',
            ],
            [
                'code' => 'EXPIRED',
                'nama_promo' => 'Promo Expired',
                'discount' => 30,
                'status' => 'nonaktif',
            ],
        ];

        foreach ($promos as $promo) {
            Promo::create($promo);
        }
    }
}
