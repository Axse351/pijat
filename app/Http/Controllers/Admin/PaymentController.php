<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Models\Commission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['booking.customer', 'booking.therapist', 'booking.service'])
            ->latest()
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    public function create()
    {
        $bookings = Booking::with(['customer', 'therapist', 'service'])
            ->where('status', 'completed')
            ->whereDoesntHave('payment')
            ->latest()
            ->get();

        return view('admin.payments.create', compact('bookings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'method'     => 'required|in:qris,cash,transfer,debit',
            'amount'     => 'required|numeric|min:0',
        ]);

        $booking = Booking::with('therapist')->findOrFail($request->booking_id);

        // Buat payment
        Payment::create([
            'booking_id' => $booking->id,
            'method'     => $request->method,
            'amount'     => $request->amount,
            'paid_at'    => now(),
        ]);

        // ── Auto-generate komisi ──────────────────────────────────────────
        //
        // ⭐ PENTING: komisi terapis dihitung dari HARGA ASLI ($booking->price),
        // BUKAN dari $booking->final_price (harga setelah diskon customer).
        //
        // Contoh: harga asli 100rb, customer dapat diskon 10% jadi bayar 90rb.
        // Komisi 25% tetap dihitung dari 100rb = 25rb, BUKAN dari 90rb (22.5rb).
        // Diskon yang diberikan ke customer adalah tanggungan Koichi, bukan
        // mengurangi hak komisi terapis.
        $therapist         = $booking->therapist;
        $commissionPercent = $therapist->commission_percent ?? 0;

        if ($commissionPercent > 0) {
            $commissionAmount = round($booking->price * $commissionPercent / 100, 2);

            $now       = Carbon::now();
            $weekStart = $now->clone()->startOfWeek(Carbon::MONDAY)->toDateString();
            $weekEnd   = $now->clone()->endOfWeek(Carbon::SUNDAY)->toDateString();

            Commission::updateOrCreate(
                [
                    'booking_id'   => $booking->id,
                    'therapist_id' => $therapist->id,
                ],
                [
                    'commission_percent' => $commissionPercent,
                    'commission_amount'  => $commissionAmount,
                    'is_paid'            => false,
                    'week_start'         => $weekStart,
                    'week_end'           => $weekEnd,
                ]
            );

            $msg = "Pembayaran berhasil dicatat dan komisi {$therapist->name} sebesar Rp "
                . number_format($commissionAmount, 0, ',', '.') . " otomatis dihitung.";
        } else {
            $msg = 'Pembayaran berhasil dicatat.';
        }

        return redirect()->route('admin.payments.index')->with('success', $msg);
    }

    public function show(Payment $payment)
    {
        $payment->load(['booking.customer', 'booking.therapist', 'booking.service']);

        return view('admin.payments.show', compact('payment'));
    }

    // ── ✅ Admin only: tampilkan form edit dengan relasi booking sudah di-load ──
    public function edit(Payment $payment)
    {
        $payment->load(['booking.customer', 'booking.therapist', 'booking.service']);

        return view('admin.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'method' => 'required|in:qris,cash,transfer,debit',
            'amount' => 'required|numeric|min:0',
        ]);

        $payment->update([
            'method' => $request->method,
            'amount' => $request->amount,
        ]);

        // Recalculate komisi jika amount berubah
        // ⭐ Sama seperti store(): pakai harga asli, bukan final_price
        $booking           = $payment->booking()->with('therapist')->first();
        $therapist         = $booking->therapist;
        $commissionPercent = $therapist->commission_percent ?? 0;

        if ($commissionPercent > 0) {
            $commissionAmount = round($booking->price * $commissionPercent / 100, 2);

            Commission::where('booking_id', $booking->id)
                ->where('therapist_id', $therapist->id)
                ->update(['commission_amount' => $commissionAmount]);
        }

        return redirect()->route('admin.payments.index')->with('success', 'Pembayaran berhasil diperbarui.');
    }

    public function destroy(Payment $payment)
    {
        // Hapus komisi terkait juga agar tidak menggantung
        Commission::where('booking_id', $payment->booking_id)->delete();

        $payment->delete();

        return back()->with('success', 'Pembayaran dihapus.');
    }
}
