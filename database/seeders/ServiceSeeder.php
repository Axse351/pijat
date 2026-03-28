<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('services')->truncate();

        $services = [
            // ── REFLEKSI ──────────────────────────────────────────────────────
            [
                'name'        => "Energizing Therapy - 60'",
                'category'    => 'Refleksi',
                'description' => 'Terapi Refleksi menyeluruh kaki, tangan, punggung, bahu, dan kepala, selama 60 menit.',
                'price'       => 110000,
                'duration'    => 60,
                'sku'         => 'R 60',
                'is_active'   => true,
            ],
            [
                'name'        => "Excellence Therapy 90'",
                'category'    => 'Refleksi',
                'description' => 'Terapi Refleksi menyeluruh kaki, tangan, punggung, bahu, dan kepala, selama 90 menit.',
                'price'       => 145000,
                'duration'    => 90,
                'sku'         => 'R 90',
                'is_active'   => true,
            ],
            [
                'name'        => "Exclusive Therapy - 120'",
                'category'    => 'Refleksi',
                'description' => 'Terapi Refleksi menyeluruh kaki, tangan, punggung, bahu, dan kepala, selama 120 menit.',
                'price'       => 180000,
                'duration'    => 120,
                'sku'         => 'R 120',
                'is_active'   => true,
            ],
            [
                'name'        => "Extra Therapy 30'",
                'category'    => 'Refleksi',
                'description' => 'Tambahan waktu terapi selama 30 menit.',
                'price'       => 35000,
                'duration'    => 30,
                'sku'         => 'R +30',
                'is_active'   => true,
            ],
            [
                'name'        => "Extra Therapy 60'",
                'category'    => 'Refleksi',
                'description' => 'Tambahan waktu terapi selama 60 menit.',
                'price'       => 70000,
                'duration'    => 60,
                'sku'         => 'R +60',
                'is_active'   => true,
            ],
            [
                'name'        => 'FITNESS PLUS MEMBER VOUCHER 90 MENIT',
                'category'    => 'Refleksi',
                'description' => 'Wajib menyerahkan voucher fisik asli. Menunjukkan POS Membership Fitness Plus lewat aplikasi. Mengisi Google Review dan Follow IG.',
                'price'       => 95000,
                'duration'    => 90,
                'sku'         => 'menit',
                'is_active'   => true,
            ],
            [
                'name'        => "Home Service 60'",
                'category'    => 'Refleksi',
                'description' => 'Therapy panggilan ke rumah selama 60 menit. Area Kota Cirebon.',
                'price'       => 145000,
                'duration'    => 60,
                'sku'         => 'HS 60',
                'is_active'   => true,
            ],
            [
                'name'        => "Home Service 90'",
                'category'    => 'Refleksi',
                'description' => 'Panggilan Therapy Reflexology yang dilakukan di rumah customer selama 90 menit. Area Kota Cirebon.',
                'price'       => 180000,
                'duration'    => 90,
                'sku'         => 'HS 90',
                'is_active'   => true,
            ],
            [
                'name'        => "Home Service 120'",
                'category'    => 'Refleksi',
                'description' => 'Panggilan Therapy Reflexology yang dilakukan di rumah customer selama 120 menit. Area Kota Cirebon.',
                'price'       => 215000,
                'duration'    => 120,
                'sku'         => 'HS 120',
                'is_active'   => true,
            ],
            [
                'name'        => 'VIP MEMBERSHIP PROGRAMME 1 BULAN',
                'category'    => 'Refleksi',
                'description' => 'Paket VIP Membership berlaku 1 bulan.',
                'price'       => 320000,
                'duration'    => null,
                'sku'         => 'BULANAN',
                'is_active'   => true,
            ],
            [
                'name'        => 'VOUCHER 98.000 EXCELLENT THERAPY 90 MENIT',
                'category'    => 'Refleksi',
                'description' => 'Voucher Excellent Therapy 90 menit harga spesial Rp 98.000.',
                'price'       => 98000,
                'duration'    => 90,
                'sku'         => 'MENIT',
                'is_active'   => true,
            ],
            [
                'name'        => "VOUCHER DISC 50% - ENERGIZING THERAPY 60'",
                'category'    => 'Refleksi',
                'description' => 'Voucher diskon 50% untuk Energizing Therapy 60 menit.',
                'price'       => 55000,
                'duration'    => 60,
                'sku'         => '60',
                'is_active'   => true,
            ],
            [
                'name'        => "VOUCHER DISC 50% - EXCELLENCE THERAPY 90'",
                'category'    => 'Refleksi',
                'description' => 'Voucher diskon 50% untuk Excellence Therapy 90 menit.',
                'price'       => 72500,
                'duration'    => 90,
                'sku'         => '90',
                'is_active'   => true,
            ],
            [
                'name'        => "VOUCHER DISC 50% - EXCLUSIVE THERAPY 120'",
                'category'    => 'Refleksi',
                'description' => 'Voucher diskon 50% untuk Exclusive Therapy 120 menit.',
                'price'       => 90000,
                'duration'    => 120,
                'sku'         => '120',
                'is_active'   => true,
            ],

            // ── MINUMAN ───────────────────────────────────────────────────────
            [
                'name'        => 'Liang Teh 16 oz',
                'category'    => 'Minuman',
                'description' => 'Minuman herbal Liang Teh asli Medan. Menyegarkan. Baik untuk mencegah panas dalam.',
                'price'       => 10000,
                'duration'    => null,
                'sku'         => 'MC-02',
                'is_active'   => true,
            ],
            [
                'name'        => 'Lo Han Kuo 14 oz',
                'category'    => 'Minuman',
                'description' => 'Minuman sehat asli dari buah Lo Han Kuo. Tanpa gula tambahan.',
                'price'       => 8000,
                'duration'    => null,
                'sku'         => 'MC-01',
                'is_active'   => true,
            ],
            [
                'name'        => 'Mountoya 600 ml',
                'category'    => 'Minuman',
                'description' => 'Air Mineral 600 ml.',
                'price'       => 5000,
                'duration'    => null,
                'sku'         => 'AMDK600',
                'is_active'   => true,
            ],
        ];

        // Tambahkan timestamps
        $now = now();
        foreach ($services as &$s) {
            $s['created_at'] = $now;
            $s['updated_at'] = $now;
        }

        DB::table('services')->insert($services);

        $this->command->info('✅ ' . count($services) . ' layanan berhasil di-seed.');
    }
}
