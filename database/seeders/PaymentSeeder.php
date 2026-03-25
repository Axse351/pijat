<?php

namespace Database\Seeders;

use App\Models\Payment;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Buat 50+ payment records untuk bookings
     */
    public function run(): void
    {
        $bookings = Booking::all();

        if ($bookings->isEmpty()) {
            echo "Pastikan BookingSeeder sudah dijalankan terlebih dahulu\n";
            return;
        }

        $paymentMethods = ['qris', 'cash'];

        // Buat payment untuk setiap booking
        foreach ($bookings as $index => $booking) {
            $method = $paymentMethods[$index % count($paymentMethods)];

            // Beberapa payment sudah dibayar, beberapa belum
            $isPaid = $index % 3 !== 0; // ~66% sudah dibayar

            // Jika status cancelled, jangan ada payment
            if ($booking->status === 'cancelled') {
                continue;
            }

            Payment::create([
                'booking_id' => $booking->id,
                'method' => $method,
                'amount' => $booking->final_price,
                'paid_at' => $isPaid ? Carbon::now()->subHours(rand(1, 72)) : null,
            ]);
        }
    }
}
