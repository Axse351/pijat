<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\PaymentAmountLog;
use Illuminate\Console\Command;

class SyncPaymentAmounts extends Command
{
    protected $signature = 'payments:sync {--dry-run : Tampilkan saja tanpa mengubah data}';
    protected $description = 'Sinkronkan payments.amount dengan bookings.final_price yang mismatch, dan catat ke log';

    public function handle()
    {
        $dryRun = $this->option('dry-run');

        $payments   = Payment::with('booking')->get();
        $mismatched = 0;

        foreach ($payments as $payment) {
            $booking = $payment->booking;

            if (!$booking) {
                $this->warn("Payment #{$payment->id} — booking tidak ditemukan, dilewati.");
                continue;
            }

            $oldAmount = round((float) $payment->amount, 2);
            $newAmount = round((float) $booking->final_price, 2);

            if ($oldAmount !== $newAmount) {
                $mismatched++;
                $this->line(
                    "Payment #{$payment->id} (booking #{$booking->id}): "
                        . "Rp " . number_format($oldAmount, 0, ',', '.')
                        . " → Rp " . number_format($newAmount, 0, ',', '.')
                );

                if (!$dryRun) {
                    PaymentAmountLog::create([
                        'payment_id' => $payment->id,
                        'booking_id' => $booking->id,
                        'old_amount' => $oldAmount,
                        'new_amount' => $newAmount,
                        'reason'     => 'Fix massal via php artisan payments:sync',
                        'changed_by' => null,
                    ]);

                    $payment->update(['amount' => $newAmount]);
                }
            }
        }

        if ($mismatched === 0) {
            $this->info('Semua payment sudah sinkron dengan final_price booking-nya. ✓');
        } else {
            $this->info(
                $dryRun
                    ? "Ditemukan {$mismatched} payment mismatch (dry-run, belum diubah)."
                    : "{$mismatched} payment berhasil disinkronkan & dicatat ke log."
            );
        }

        return self::SUCCESS;
    }
}
