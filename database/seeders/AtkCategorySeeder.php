<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AtkCategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('atks')->truncate();
        DB::table('atk_categories')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['name' => 'Alat Terapi',          'code' => '1',  'description' => 'Peralatan dan perlengkapan terapi'],
            ['name' => 'AMDK',                 'code' => '2',  'description' => 'Air Minum Dalam Kemasan'],
            ['name' => 'ATK',                  'code' => '3',  'description' => 'Alat Tulis Kantor'],
            ['name' => 'Entertaint',           'code' => '4',  'description' => 'Biaya hiburan dan kebersamaan'],
            ['name' => 'Gedung & Komunikasi',  'code' => '5',  'description' => 'Biaya gedung, utilitas, dan komunikasi'],
            ['name' => 'Komisi & Training',    'code' => '6',  'description' => 'Komisi, gaji, dan biaya pelatihan'],
            ['name' => 'Promosi',              'code' => '7',  'description' => 'Biaya promosi dan pemasaran'],
            ['name' => 'Sanitary',             'code' => '8',  'description' => 'Perlengkapan kebersihan'],
            ['name' => 'Transportasi',         'code' => '9',  'description' => 'Biaya transportasi operasional'],
            ['name' => 'Others',               'code' => '10', 'description' => 'Pengeluaran lain-lain'],
        ];

        $now = now();
        foreach ($categories as &$c) {
            $c['created_at'] = $now;
            $c['updated_at'] = $now;
        }

        DB::table('atk_categories')->insert($categories);

        // Ambil ID kategori yang baru dibuat
        $cat = DB::table('atk_categories')->pluck('id', 'code');

        // ── ATK ITEMS per kategori ─────────────────────────────────────────────
        $items = [

            // 1 — Alat Terapi
            ['atk_category_id' => $cat['1'],  'code' => '11',  'name' => 'Bedak',                'description' => 'Bedak untuk terapi'],
            ['atk_category_id' => $cat['1'],  'code' => '12',  'name' => 'Garam SPA',            'description' => 'Garam untuk perawatan SPA'],
            ['atk_category_id' => $cat['1'],  'code' => '13',  'name' => 'Laundry',              'description' => 'Biaya atau perlengkapan laundry'],
            ['atk_category_id' => $cat['1'],  'code' => '14',  'name' => 'Wedang',               'description' => 'Minuman wedang untuk tamu'],
            ['atk_category_id' => $cat['1'],  'code' => '110', 'name' => 'Lainnya (Alat Terapi)', 'description' => 'Item alat terapi lainnya'],

            // 2 — AMDK
            ['atk_category_id' => $cat['2'],  'code' => '21',  'name' => 'AMDK',                 'description' => 'Air Minum Dalam Kemasan'],
            ['atk_category_id' => $cat['2'],  'code' => '210', 'name' => 'Lainnya (AMDK)',        'description' => 'Item AMDK lainnya'],

            // 3 — ATK
            ['atk_category_id' => $cat['3'],  'code' => '31',  'name' => 'ATK Umum',             'description' => 'Alat tulis kantor umum'],
            ['atk_category_id' => $cat['3'],  'code' => '310', 'name' => 'Lainnya (ATK)',         'description' => 'Item ATK lainnya'],

            // 4 — Entertaint
            ['atk_category_id' => $cat['4'],  'code' => '41',  'name' => 'Acara Kebersamaan',    'description' => 'Biaya acara kebersamaan tim'],
            ['atk_category_id' => $cat['4'],  'code' => '42',  'name' => 'Makan - Entertaint',   'description' => 'Biaya makan untuk entertainment'],
            ['atk_category_id' => $cat['4'],  'code' => '410', 'name' => 'Lainnya (Entertaint)', 'description' => 'Item entertainment lainnya'],

            // 5 — Gedung & Komunikasi
            ['atk_category_id' => $cat['5'],  'code' => '51',  'name' => 'Perawatan Gedung',     'description' => 'Biaya perawatan dan pemeliharaan gedung'],
            ['atk_category_id' => $cat['5'],  'code' => '52',  'name' => 'Pulsa Telephone',      'description' => 'Pulsa dan biaya telepon'],
            ['atk_category_id' => $cat['5'],  'code' => '53',  'name' => 'Service AC',           'description' => 'Biaya service air conditioner'],
            ['atk_category_id' => $cat['5'],  'code' => '54',  'name' => 'Sewa Gedung',          'description' => 'Biaya sewa gedung / tempat usaha'],
            ['atk_category_id' => $cat['5'],  'code' => '55',  'name' => 'Token Listrik',        'description' => 'Pembelian token listrik'],
            ['atk_category_id' => $cat['5'],  'code' => '56',  'name' => 'WIFI',                 'description' => 'Biaya langganan internet / WiFi'],
            ['atk_category_id' => $cat['5'],  'code' => '510', 'name' => 'Lainnya (Gedung)',     'description' => 'Item gedung & komunikasi lainnya'],

            // 6 — Komisi & Training
            ['atk_category_id' => $cat['6'],  'code' => '61',  'name' => 'Biaya Training',       'description' => 'Biaya pelatihan karyawan'],
            ['atk_category_id' => $cat['6'],  'code' => '62',  'name' => 'Gaji',                 'description' => 'Gaji karyawan'],
            ['atk_category_id' => $cat['6'],  'code' => '63',  'name' => 'Honor Training',       'description' => 'Honor untuk pelatih / instruktur'],
            ['atk_category_id' => $cat['6'],  'code' => '64',  'name' => 'Komisi SGM',           'description' => 'Komisi untuk SGM'],
            ['atk_category_id' => $cat['6'],  'code' => '65',  'name' => 'Komisi Therapyst',     'description' => 'Komisi untuk terapis'],
            ['atk_category_id' => $cat['6'],  'code' => '66',  'name' => 'Lisensi',              'description' => 'Biaya lisensi software atau sertifikasi'],
            ['atk_category_id' => $cat['6'],  'code' => '67',  'name' => 'Uang Hadir',           'description' => 'Bonus uang kehadiran terapis'],
            ['atk_category_id' => $cat['6'],  'code' => '610', 'name' => 'Lainnya (Komisi)',     'description' => 'Item komisi & training lainnya'],

            // 7 — Promosi
            ['atk_category_id' => $cat['7'],  'code' => '71',  'name' => 'Collab',               'description' => 'Biaya kolaborasi promosi'],
            ['atk_category_id' => $cat['7'],  'code' => '72',  'name' => 'InstaGram',            'description' => 'Biaya iklan Instagram'],
            ['atk_category_id' => $cat['7'],  'code' => '73',  'name' => 'Influencer',           'description' => 'Biaya endorsement influencer'],
            ['atk_category_id' => $cat['7'],  'code' => '74',  'name' => 'Sponsor',              'description' => 'Biaya sponsorship'],
            ['atk_category_id' => $cat['7'],  'code' => '710', 'name' => 'Lainnya (Promosi)',    'description' => 'Item promosi lainnya'],

            // 8 — Sanitary
            ['atk_category_id' => $cat['8'],  'code' => '81',  'name' => 'Karbol',               'description' => 'Cairan pembersih lantai karbol'],
            ['atk_category_id' => $cat['8'],  'code' => '82',  'name' => 'Pengharum Ruangan',    'description' => 'Pengharum ruangan / aroma terapi'],
            ['atk_category_id' => $cat['8'],  'code' => '83',  'name' => 'Sabun Tangan',         'description' => 'Sabun cuci tangan'],
            ['atk_category_id' => $cat['8'],  'code' => '84',  'name' => 'Tissue',               'description' => 'Tissue / tisu kebersihan'],
            ['atk_category_id' => $cat['8'],  'code' => '810', 'name' => 'Lainnya (Sanitary)',   'description' => 'Item sanitary lainnya'],

            // 9 — Transportasi
            ['atk_category_id' => $cat['9'],  'code' => '91',  'name' => 'Bensin',               'description' => 'Bahan bakar kendaraan operasional'],
            ['atk_category_id' => $cat['9'],  'code' => '92',  'name' => 'Travel - Tol',         'description' => 'Biaya perjalanan dan tol'],
            ['atk_category_id' => $cat['9'],  'code' => '910', 'name' => 'Lainnya (Transportasi)', 'description' => 'Item transportasi lainnya'],

            // 10 — Others
            ['atk_category_id' => $cat['10'], 'code' => '100', 'name' => 'Others',               'description' => 'Pengeluaran lain-lain yang tidak terklasifikasi'],
        ];

        foreach ($items as &$item) {
            $item['stock']               = 0;
            $item['last_purchase_price'] = null;
            $item['created_at']          = $now;
            $item['updated_at']          = $now;
        }

        DB::table('atks')->insert($items);

        $this->command->info('✅ ' . count($categories) . ' kategori & ' . count($items) . ' item ATK/COA berhasil di-seed.');
    }
}
