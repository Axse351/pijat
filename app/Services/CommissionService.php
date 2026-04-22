<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Commission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CommissionService
 * ─────────────────
 * Semua logika perhitungan & pencatatan komisi terapis ada di sini.
 *
 * ATURAN BISNIS:
 * ┌──────────────────────────────────────────────────────────────────┐
 * │  Tipe         │ Komisi Terapis │ Income Koichi                   │
 * ├──────────────────────────────────────────────────────────────────┤
 * │  standard     │     25%        │     75%                         │
 * │  program      │     30%        │     70%                         │
 * ├──────────────────────────────────────────────────────────────────┤
 * │  CANCEL (sudah bayar + terapis spesifik)                        │
 * │  → Semua uang hangus → 100% ke terapis yg di-assign             │
 * └──────────────────────────────────────────────────────────────────┘
 */
class CommissionService
{
    // ── Konstanta persentase ──────────────────────────────────────────────
    const RATE_STANDARD = 25.00;   // persen
    const RATE_PROGRAM  = 30.00;   // persen untuk paket program

    // ── Hitung persentase berdasarkan tipe booking ────────────────────────
    public function getRate(Booking $booking): float
    {
        // Jika booking pakai program → 30%
        if ($booking->program_id || $booking->commission_type === 'program') {
            return self::RATE_PROGRAM;
        }
        return self::RATE_STANDARD;
    }

    // ── Hitung nominal komisi ─────────────────────────────────────────────
    public function calculateAmount(Booking $booking, float $rate = null): float
    {
        $rate = $rate ?? $this->getRate($booking);
        return round($booking->final_price * ($rate / 100), 2);
    }

    // ── Hitung income bersih Koichi ───────────────────────────────────────
    public function calculateKoichiIncome(Booking $booking, float $commissionAmount = null): float
    {
        $commission = $commissionAmount ?? $this->calculateAmount($booking);
        return round($booking->final_price - $commission, 2);
    }

    /**
     * Catat komisi NORMAL (saat booking selesai / completed)
     * Dipanggil dari: BookingController saat status → completed
     */
    public function recordNormalCommission(Booking $booking): Commission
    {
        // Jangan duplikat — cek dulu
        $existing = Commission::where('booking_id', $booking->id)
            ->where('commission_source', 'normal')
            ->first();

        if ($existing) {
            Log::info("Commission already exists for booking #{$booking->id}");
            return $existing;
        }

        $rate        = $this->getRate($booking);
        $amount      = $this->calculateAmount($booking, $rate);
        $serviceName = $booking->service->name ?? 'Layanan';

        [$weekStart, $weekEnd] = $this->getCurrentWeekRange();

        return DB::transaction(function () use ($booking, $rate, $amount, $weekStart, $weekEnd, $serviceName) {
            $commission = Commission::create([
                'booking_id'           => $booking->id,
                'therapist_id'         => $booking->therapist_id,
                'commission_percent'   => $rate,
                'commission_amount'    => $amount,
                'is_paid'              => false,
                'is_cancel_commission' => false,
                'commission_source'    => 'normal',
                'notes'                => "Komisi sesi selesai — {$serviceName}",
                'week_start'           => $weekStart,
                'week_end'             => $weekEnd,
            ]);

            Log::info("Normal commission recorded", [
                'booking_id'    => $booking->id,
                'therapist_id'  => $booking->therapist_id,
                'rate'          => $rate . '%',
                'amount'        => $amount,
                'koichi_income' => $this->calculateKoichiIncome($booking, $amount),
            ]);

            return $commission;
        });
    }

    /**
     * Catat komisi CANCEL FORFEIT
     * Dipanggil saat booking di-cancel SETELAH bayar & terapis SPESIFIK
     *
     * Aturan: Semua uang hangus → 100% ke terapis yg di-assign
     * (Tidak ada kredit customer, tidak ada sisa ke Koichi)
     */
    public function recordCancelForfeitCommission(Booking $booking, string $reason = ''): ?Commission
    {
        // Validasi: hanya berlaku jika sudah bayar & terapis spesifik
        if (! $booking->payment) {
            Log::warning("Cancel forfeit skipped — booking #{$booking->id} belum bayar");
            return null;
        }

        if (! $booking->is_specific_therapist) {
            Log::info("Cancel forfeit skipped — booking #{$booking->id} terapis tidak spesifik, proses refund normal");
            return null;
        }

        // Cek duplikat
        $existing = Commission::where('booking_id', $booking->id)
            ->where('commission_source', 'cancel_forfeit')
            ->first();

        if ($existing) {
            return $existing;
        }

        // Saat cancel forfeit: rate 100% (seluruh uang ke terapis)
        $amount = $booking->payment->amount; // ambil dari nominal yang benar-benar dibayar

        [$weekStart, $weekEnd] = $this->getCurrentWeekRange();

        return DB::transaction(function () use ($booking, $amount, $weekStart, $weekEnd, $reason) {
            $commission = Commission::create([
                'booking_id'           => $booking->id,
                'therapist_id'         => $booking->therapist_id,
                'commission_percent'   => 100.00,   // seluruh uang ke terapis
                'commission_amount'    => $amount,
                'is_paid'              => false,
                'is_cancel_commission' => true,
                'commission_source'    => 'cancel_forfeit',
                'notes'                => "Cancel forfeit — " . ($reason ?: 'Customer cancel setelah bayar, terapis spesifik'),
                'week_start'           => $weekStart,
                'week_end'             => $weekEnd,
            ]);

            Log::info("Cancel forfeit commission recorded", [
                'booking_id'   => $booking->id,
                'therapist_id' => $booking->therapist_id,
                'amount'       => $amount,
                'reason'       => $reason,
            ]);

            return $commission;
        });
    }

    /**
     * Ambil ringkasan income untuk satu booking
     * Berguna untuk ditampilkan di UI / laporan
     */
    public function getBookingSummary(Booking $booking): array
    {
        $rate          = $this->getRate($booking);
        $commissionAmt = $this->calculateAmount($booking, $rate);
        $koichiIncome  = $this->calculateKoichiIncome($booking, $commissionAmt);

        return [
            'booking_id'        => $booking->id,
            'final_price'       => $booking->final_price,
            'commission_type'   => $booking->commission_type ?? 'standard',
            'commission_rate'   => $rate,
            'commission_amount' => $commissionAmt,
            'koichi_income'     => $koichiIncome,
            'koichi_percent'    => 100 - $rate,
            'is_program'        => $booking->program_id ? true : false,
        ];
    }

    /**
     * Hitung total komisi belum dibayar untuk seorang terapis
     */
    public function getUnpaidTotal(int $therapistId): array
    {
        $unpaid = Commission::where('therapist_id', $therapistId)
            ->where('is_paid', false)
            ->selectRaw('
                SUM(commission_amount) as total,
                SUM(CASE WHEN commission_source = "normal" THEN commission_amount ELSE 0 END) as from_sessions,
                SUM(CASE WHEN commission_source = "cancel_forfeit" THEN commission_amount ELSE 0 END) as from_cancels,
                COUNT(*) as count
            ')
            ->first();

        return [
            'total'         => (float) ($unpaid->total ?? 0),
            'from_sessions' => (float) ($unpaid->from_sessions ?? 0),
            'from_cancels'  => (float) ($unpaid->from_cancels ?? 0),
            'count'         => (int)   ($unpaid->count ?? 0),
        ];
    }

    // ── Helper: minggu berjalan (Senin – Minggu) ──────────────────────────
    private function getCurrentWeekRange(): array
    {
        $now = Carbon::now('Asia/Jakarta');
        return [
            $now->copy()->startOfWeek()->toDateString(),
            $now->copy()->endOfWeek()->toDateString(),
        ];
    }
}
