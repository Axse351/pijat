<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use App\Models\Promo;
use App\Models\Program;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Buat 50+ bookings dengan berbagai status dan kombinasi
     */
    public function run(): void
    {
        $customers = Customer::all();
        $services = Service::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $promos = Promo::where('status', 'aktif')->get();
        $programs = Program::where('is_active', 1)->get();

        if ($customers->isEmpty() || $services->isEmpty() || $therapists->isEmpty()) {
            echo "Pastikan Seeder lain sudah dijalankan terlebih dahulu\n";
            return;
        }

        $bookings = [];
        $statuses = ['scheduled', 'completed', 'cancelled', 'ongoing', 'pending'];
        $orderSources = ['web', 'wa', 'walkin'];
        $baseDiscount = 0;

        // Generate 50 bookings
        for ($i = 0; $i < 50; $i++) {
            $customer = $customers[$i % $customers->count()];
            $service = $services[$i % $services->count()];
            $therapist = $therapists[$i % $therapists->count()];
            $status = $statuses[$i % count($statuses)];
            $orderSource = $orderSources[$i % count($orderSources)];

            // Tentukan scheduled_at berdasarkan status
            if ($status === 'scheduled') {
                $scheduledAt = Carbon::now()->addDays(rand(1, 30))->setTime(rand(9, 17), rand(0, 59));
                $originalScheduledAt = $scheduledAt;
                $isRescheduled = false;
            } elseif ($status === 'completed') {
                $scheduledAt = Carbon::now()->subDays(rand(1, 60))->setTime(rand(9, 17), rand(0, 59));
                $originalScheduledAt = $scheduledAt;
                $isRescheduled = false;
            } elseif ($status === 'cancelled') {
                $scheduledAt = Carbon::now()->subDays(rand(1, 30))->setTime(rand(9, 17), rand(0, 59));
                $originalScheduledAt = $scheduledAt;
                $isRescheduled = false;
            } elseif ($status === 'ongoing') {
                $scheduledAt = Carbon::now()->setTime(9, 0);
                $originalScheduledAt = $scheduledAt;
                $isRescheduled = false;
            } else { // pending
                $scheduledAt = Carbon::now()->addHours(rand(1, 24));
                $originalScheduledAt = $scheduledAt;
                $isRescheduled = false;
            }

            // Beberapa ada reschedule
            if ($i % 8 === 0 && $status === 'scheduled') {
                $originalScheduledAt = Carbon::parse($scheduledAt)->subDays(rand(2, 7));
                $isRescheduled = true;
            }

            // Hitung discount
            $discount = 0;
            $hasPromo = $i % 3 === 0 && $promos->isNotEmpty();
            $hasProgram = $i % 4 === 0 && $programs->isNotEmpty();

            if ($hasProgram) {
                $program = $programs->random();
                if ($program->discount_type === 'percent') {
                    $discount = round($service->price * $program->discount_value / 100);
                    if ($program->max_discount && $discount > $program->max_discount) {
                        $discount = $program->max_discount;
                    }
                } else {
                    $discount = $program->discount_value;
                }
            }

            $finalPrice = max(0, $service->price - $discount);

            $bookings[] = [
                'customer_id' => $customer->id,
                'therapist_id' => $therapist->id,
                'service_id' => $service->id,
                'scheduled_at' => $scheduledAt,
                'original_scheduled_at' => $originalScheduledAt,
                'is_rescheduled' => $isRescheduled,
                'order_source' => $orderSource,
                'discount' => $discount,
                'price' => $service->price,
                'final_price' => $finalPrice,
                'promo_id' => $hasPromo ? $promos->random()->id : null,
                'program_id' => $hasProgram ? $programs->random()->id : null,
                'notes' => 'Booking #' . ($i + 1),
                'status' => $status,
            ];
        }

        foreach ($bookings as $booking) {
            Booking::create($booking);
        }
    }
}
