<?php

namespace Database\Seeders;

use App\Models\Membership;
use Illuminate\Database\Seeder;

class MembershipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Buat 3 tier membership: Regular, VIP, VVIP
     * Masing-masing dengan 3 durasi: 30 hari, 90 hari, 365 hari
     * Total: 9 membership records
     */
    public function run(): void
    {
        $memberships = [
            // REGULAR - Pengunjung Biasa
            [
                'name' => 'Regular',
                'duration_days' => 30,
            ],
            [
                'name' => 'Regular',
                'duration_days' => 90,
            ],
            [
                'name' => 'Regular',
                'duration_days' => 365,
            ],
            // VIP
            [
                'name' => 'VIP',
                'duration_days' => 30,
            ],
            [
                'name' => 'VIP',
                'duration_days' => 90,
            ],
            [
                'name' => 'VIP',
                'duration_days' => 365,
            ],
            // VVIP
            [
                'name' => 'VVIP',
                'duration_days' => 30,
            ],
            [
                'name' => 'VVIP',
                'duration_days' => 90,
            ],
            [
                'name' => 'VVIP',
                'duration_days' => 365,
            ],
        ];

        foreach ($memberships as $membership) {
            Membership::create($membership);
        }
    }
}
