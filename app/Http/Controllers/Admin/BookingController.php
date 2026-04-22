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
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
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
                    $phone = preg_replace('/\D/', '', $b->customer->phone);
                    if (str_starts_with($phone, '0')) $phone = '62' . substr($phone, 1);
                    $jadwalFormatted = $scheduledDate->translatedFormat('l, d F Y \p\u\k\u\l H:i');
                    $rescheduleNote  = $b->is_rescheduled && $b->original_scheduled_at
                        ? "\n⚠️ *Jadwal diubah* dari " . Carbon::parse($b->original_scheduled_at)->translatedFormat('d F Y H:i') . "\n"
                        : '';
                    $waMsg = urlencode(
                        "Halo {$b->customer->name}, kami ingin mengingatkan booking Anda:\n\n"
                            . "📋 Layanan : {$b->service->name}\n"
                            . "👤 Terapis : {$b->therapist->name}\n"
                            . "🗓 Jadwal  : {$jadwalFormatted}\n"
                            . $rescheduleNote
                            . "\nMohon hadir tepat waktu. Terima kasih! 🙏"
                    );
                    $waUrl = "https://wa.me/{$phone}?text={$waMsg}";
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

        // Deteksi reschedule
        $oldScheduled        = $booking->scheduled_at;
        $newScheduled        = $validated['scheduled_at'];
        $dateChanged         = Carbon::parse($oldScheduled)->ne(Carbon::parse($newScheduled));
        $isRescheduled       = $dateChanged && $request->boolean('is_rescheduled');
        $originalScheduledAt = $booking->original_scheduled_at ?? $booking->scheduled_at;

        // Catat status lama SEBELUM update untuk deteksi perubahan ke completed
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

        // ✅ Berikan poin ke pelanggan hanya saat pertama kali status berubah ke completed
        if (!$wasCompleted && $becomesCompleted) {
            $rewardPoints = $service->reward_points ?? 0;
            if ($rewardPoints > 0) {
                $booking->customer->addPoints($rewardPoints);
            }
        }

        $message = 'Booking berhasil diperbarui.';
        if ($isRescheduled) $message .= ' Jadwal telah diubah dan ditandai.';
        if (!$wasCompleted && $becomesCompleted && ($service->reward_points ?? 0) > 0) {
            $message .= " +{$service->reward_points} poin diberikan ke {$booking->customer->name}.";
        }

        return redirect()->route('admin.bookings.index')
            ->with('success', $message);
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Booking dihapus.');
    }
}
