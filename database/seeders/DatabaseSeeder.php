<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

         $this->call([
            UserSeeder::class,
            TherapistSeeder::class,
            ServiceSeeder::class,
            PromoSeeder::class,
            ProgramSeeder::class,
            MembershipSeeder::class,
            CustomerSeeder::class,
            CustomerMembershipSeeder::class,
            BookingSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
