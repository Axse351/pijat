<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\Promo;
use App\Models\Program;
use App\Models\Commission;
use App\Models\WaMessageTemplate;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    // ─────────────────────────────────────────────
    // COMMISSION HELPER
    // ─────────────────────────────────────────────

    /**
     * Hitung dan simpan komisi berdasarkan tipe layanan.
     * Home service → 30%, reguler → 25%
     */
    private function createCommission(Booking $booking): void
    {
        // Hindari duplikasi jika sudah ada komisi untuk booking ini
        if ($booking->commission()->exists()) {
            return;
        }

        $isHomeService     = (bool) ($booking->service->is_home_service ?? false);
        $commissionPercent = $isHomeService ? 30.00 : 25.00;
        $commissionAmount  = round($booking->final_price * $commissionPercent / 100, 2);

        // Hitung rentang minggu (Senin–Minggu) dari tanggal selesai
        $completedDate = Carbon::now();
        $weekStart     = $completedDate->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
        $weekEnd       = $completedDate->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $booking->commission()->create([
            'therapist_id'       => $booking->therapist_id,
            'commission_percent' => $commissionPercent,
            'commission_amount'  => $commissionAmount,
            'is_paid'            => false,
            'week_start'         => $weekStart,
            'week_end'           => $weekEnd,
        ]);
    }

    // ─────────────────────────────────────────────
    // INDEX & CALENDAR
    // ─────────────────────────────────────────────

    public function index()
    {
        $bookings   = Booking::with(['customer', 'therapist', 'service', 'program'])->latest()->get();
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();
        $promos     = Promo::where('status', 'aktif')->get();
        $programs   = Program::where('is_active', 1)->get();

        return view('admin.bookings.index', compact(
            'bookings',
            'customers',
            'therapists',
            'services',
            'promos',
            'programs'
        ));
    }

    public function calendar()
    {
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();

        return view('admin.bookings.calendar', compact('customers', 'therapists', 'services'));
    }

    public function calendarData(Request $request)
    {
        $date = $request->input('date', today()->toDateString());

        $bookings = Booking::with(['customer', 'service', 'therapist'])
            ->whereDate('scheduled_at', $date)
            ->get()
            ->map(function ($b) {
                $scheduledDate = Carbon::parse($b->scheduled_at);
                $today         = Carbon::today();
                $tomorrow      = Carbon::tomorrow();
                $isToday       = $scheduledDate->isSameDay($today);
                $isTomorrow    = $scheduledDate->isSameDay($tomorrow);

                $waUrl = null;
                if (($isToday || $isTomorrow) && $b->status === 'scheduled' && !empty($b->customer?->phone)) {
                    $jadwalFormatted = $scheduledDate->translatedFormat('l, d F Y \p\u\k\u\l H:i');
                    $isRescheduled   = (bool) $b->is_rescheduled;

                    $vars = [
                        'nama_pelanggan' => $b->customer->name,
                        'layanan'        => $b->service->name,
                        'terapis'        => $b->therapist->name,
                        'jadwal'         => $jadwalFormatted,
                    ];

                    $templateKey = $isRescheduled ? 'booking_reminder_reschedule' : 'booking_reminder';
                    if ($isRescheduled && $b->original_scheduled_at) {
                        $vars['jadwal_lama'] = Carbon::parse($b->original_scheduled_at)->translatedFormat('d F Y H:i');
                    }

                    $waMsg = WaMessageTemplate::render($templateKey, $vars);
                    if ($waMsg) {
                        $phone = WaMessageTemplate::normalizePhone($b->customer->phone);
                        if ($phone) {
                            $waUrl = "https://wa.me/{$phone}?text=" . urlencode($waMsg);
                        }
                    }
                }

                return [
                    'id'             => $b->id,
                    'therapist_id'   => $b->therapist_id,
                    'hour'           => $scheduledDate->hour,
                    'customer_name'  => $b->customer?->name ?? '—',
                    'service_name'   => $b->service?->name ?? '—',
                    'status'         => $b->status,
                    'is_rescheduled' => (bool) $b->is_rescheduled,
                    'wa_url'         => $waUrl,
                    'edit_url'       => route('admin.bookings.edit', $b->id),
                ];
            });

        return response()->json(['bookings' => $bookings]);
    }

    // ─────────────────────────────────────────────
    // CREATE & STORE
    // ─────────────────────────────────────────────

    public function create()
    {
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();
        $promos     = Promo::where('status', 'aktif')->get();
        $programs   = Program::where('is_active', 1)->get();

        return view('admin.bookings.create', compact('customers', 'therapists', 'services', 'promos', 'programs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'therapist_id'   => 'required|exists:therapists,id',
            'service_id'     => 'required|exists:services,id',
            'scheduled_at'   => 'required|date',
            'order_source'   => 'nullable|in:walkin,wa,web',
            'discount'       => 'nullable|numeric|min:0',
            'promo_id'       => 'nullable|exists:promos,id',
            'program_id'     => 'nullable|exists:programs,id',
            'notes'          => 'nullable|string|max:500',
            'is_rescheduled' => 'nullable|boolean',
        ]);

        $service  = Service::findOrFail($validated['service_id']);
        $discount = $validated['discount'] ?? 0;

        if (!empty($validated['program_id'])) {
            $program = Program::findOrFail($validated['program_id']);

            if (!$program->is_active) {
                return back()->withErrors(['program_id' => 'Program tidak aktif.'])->withInput();
            }

            $now = Carbon::now();
            if ($program->start_date && Carbon::parse($program->start_date)->isAfter($now)) {
                return back()->withErrors(['program_id' => 'Program belum dimulai.'])->withInput();
            }
            if ($program->end_date && Carbon::parse($program->end_date)->isBefore($now)) {
                return back()->withErrors(['program_id' => 'Program sudah berakhir.'])->withInput();
            }

            if ($program->discount_type === 'percent') {
                $programDisc = round($service->price * $program->discount_value / 100);
                if ($program->max_discount && $programDisc > $program->max_discount) {
                    $programDisc = $program->max_discount;
                }
            } else {
                $programDisc = $program->discount_value;
            }

            $discount += $programDisc;
        }

        $finalPrice = max(0, $service->price - $discount);
        $hasPromo   = !empty($validated['promo_id']);

        if ($finalPrice === 0 && $service->price > 0 && !$hasPromo) {
            return back()->withErrors(['discount' => 'Total tidak boleh Rp 0 kecuali menggunakan promo.'])->withInput();
        }

        Booking::create([
            'customer_id'           => $validated['customer_id'],
            'therapist_id'          => $validated['therapist_id'],
            'service_id'            => $validated['service_id'],
            'scheduled_at'          => $validated['scheduled_at'],
            'original_scheduled_at' => $validated['scheduled_at'],
            'is_rescheduled'        => false,
            'order_source'          => $validated['order_source'] ?? 'walkin',
            'discount'              => $discount,
            'final_price'           => $finalPrice,
            'promo_id'              => $validated['promo_id'] ?? null,
            'program_id'            => $validated['program_id'] ?? null,
            'notes'                 => $validated['notes'] ?? null,
            'price'                 => $service->price,
            'status'                => 'scheduled',
        ]);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Booking berhasil dibuat.');
    }

    // ─────────────────────────────────────────────
    // EDIT & UPDATE
    // ─────────────────────────────────────────────

    public function edit(Booking $booking)
    {
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();
        $promos     = Promo::where('status', 'aktif')->get();
        $programs   = Program::where('is_active', 1)->get();

        return view('admin.bookings.edit', compact('booking', 'customers', 'therapists', 'services', 'promos', 'programs'));
    }

    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'therapist_id'   => 'required|exists:therapists,id',
            'service_id'     => 'required|exists:services,id',
            'scheduled_at'   => 'required|date',
            'order_source'   => 'nullable|in:walkin,wa,web',
            'status'         => 'nullable|in:scheduled,ongoing,completed,cancelled',
            'discount'       => 'nullable|numeric|min:0',
            'promo_id'       => 'nullable|exists:promos,id',
            'program_id'     => 'nullable|exists:programs,id',
            'notes'          => 'nullable|string|max:500',
            'is_rescheduled' => 'nullable|boolean',
        ]);

        $service  = Service::findOrFail($validated['service_id']);
        $discount = $validated['discount'] ?? 0;

        if (!empty($validated['program_id'])) {
            $program = Program::findOrFail($validated['program_id']);

            if (!$program->is_active) {
                return back()->withErrors(['program_id' => 'Program tidak aktif.'])->withInput();
            }

            if ($program->discount_type === 'percent') {
                $programDisc = round($service->price * $program->discount_value / 100);
                if ($program->max_discount && $programDisc > $program->max_discount) {
                    $programDisc = $program->max_discount;
                }
            } else {
                $programDisc = $program->discount_value;
            }

            $discount += $programDisc;
        }

        $finalPrice = max(0, $service->price - $discount);
        $hasPromo   = !empty($validated['promo_id']);

        if ($finalPrice === 0 && $service->price > 0 && !$hasPromo) {
            return back()->withErrors(['discount' => 'Total tidak boleh Rp 0 kecuali menggunakan promo.'])->withInput();
        }

        $oldScheduled        = $booking->scheduled_at;
        $newScheduled        = $validated['scheduled_at'];
        $dateChanged         = Carbon::parse($oldScheduled)->ne(Carbon::parse($newScheduled));
        $isRescheduled       = $dateChanged && $request->boolean('is_rescheduled');
        $originalScheduledAt = $booking->original_scheduled_at ?? $booking->scheduled_at;

        $oldStatus        = $booking->status;
        $newStatus        = $validated['status'] ?? $booking->status;
        $wasCompleted     = $oldStatus === 'completed';
        $becomesCompleted = $newStatus === 'completed';

        $booking->update([
            'customer_id'           => $validated['customer_id'],
            'therapist_id'          => $validated['therapist_id'],
            'service_id'            => $validated['service_id'],
            'scheduled_at'          => $newScheduled,
            'original_scheduled_at' => $originalScheduledAt,
            'is_rescheduled'        => $isRescheduled || $booking->is_rescheduled,
            'order_source'          => $validated['order_source'] ?? $booking->order_source,
            'status'                => $newStatus,
            'discount'              => $discount,
            'final_price'           => $finalPrice,
            'promo_id'              => $validated['promo_id'] ?? null,
            'program_id'            => $validated['program_id'] ?? null,
            'price'                 => $service->price,
            'notes'                 => $validated['notes'] ?? $booking->notes,
        ]);

        if (!$wasCompleted && $becomesCompleted) {
            // Beri poin reward
            $rewardPoints = $service->reward_points ?? 0;
            if ($rewardPoints > 0) {
                $booking->customer->addPoints($rewardPoints);
            }

            // Buat record komisi — refresh dulu agar relasi service terbaru
            $booking->refresh();
            $this->createCommission($booking);
        }

        $message = 'Booking berhasil diperbarui.';
        if ($isRescheduled) {
            $message .= ' Jadwal telah diubah dan ditandai.';
        }
        if (!$wasCompleted && $becomesCompleted) {
            $rewardPoints = $service->reward_points ?? 0;
            if ($rewardPoints > 0) {
                $message .= " +{$rewardPoints} poin diberikan ke {$booking->customer->name}.";
            }

            $isHomeService     = (bool) ($service->is_home_service ?? false);
            $commissionPercent = $isHomeService ? 30 : 25;
            $message .= " Komisi {$commissionPercent}% tercatat untuk {$booking->therapist->name}.";
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────
    // COMPLETE (quick action)
    // ─────────────────────────────────────────────

    public function complete(Booking $booking)
    {
        if ($booking->status === 'completed') {
            return redirect()->route('admin.bookings.index')
                ->with('success', 'Booking sudah berstatus selesai.');
        }

        $booking->update(['status' => 'completed']);
        $booking->refresh();

        // Beri poin reward jika ada
        $service      = $booking->service;
        $rewardPoints = $service->reward_points ?? 0;
        if ($rewardPoints > 0) {
            $booking->customer->addPoints($rewardPoints);
        }

        // Buat record komisi
        $this->createCommission($booking);

        // Bangun URL WA ucapan terima kasih
        $waUrl = null;
        if (!empty($booking->customer?->phone)) {
            $vars = [
                'nama_pelanggan' => $booking->customer->name,
                'layanan'        => $service->name ?? 'layanan',
                'terapis'        => $booking->therapist->name ?? '',
                'poin'           => $rewardPoints > 0 ? $rewardPoints : '',
            ];

            $waMsg = WaMessageTemplate::render('booking_complete', $vars);

            // Fallback jika template belum ada di database
            if (!$waMsg) {
                $pointInfo = $rewardPoints > 0
                    ? "\n\n🎁 Kamu mendapatkan *{$rewardPoints} poin* dari sesi ini!"
                    : '';
                $waMsg = "Halo {$vars['nama_pelanggan']}! 😊\n\n"
                    . "Terima kasih sudah mempercayakan perawatanmu kepada kami hari ini.\n\n"
                    . "✅ Sesi *{$vars['layanan']}* bersama *{$vars['terapis']}* telah selesai."
                    . $pointInfo
                    . "\n\nSemoga kamu merasa lebih segar & relaks. Sampai jumpa lagi! 🌸\n\n"
                    . "_— Tim Koichi_";
            }

            $phone = WaMessageTemplate::normalizePhone($booking->customer->phone);
            if ($phone) {
                $waUrl = "https://wa.me/{$phone}?text=" . urlencode($waMsg);
            }
        }

        $isHomeService     = (bool) ($service->is_home_service ?? false);
        $commissionPercent = $isHomeService ? 30 : 25;

        session()->flash('complete_wa_url', $waUrl);
        session()->flash('complete_customer_name', $booking->customer->name);
        session()->flash(
            'success',
            "Booking {$booking->customer->name} berhasil diselesaikan."
                . ($rewardPoints > 0 ? " +{$rewardPoints} poin diberikan." : '')
                . " Komisi {$commissionPercent}% tercatat untuk {$booking->therapist->name}."
        );

        return redirect()->route('admin.bookings.index');
    }

    // ─────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Booking dihapus.');
    }
}
