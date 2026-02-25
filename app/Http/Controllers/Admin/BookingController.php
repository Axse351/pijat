<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\Promo;
use App\Models\Commission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
        $bookings   = Booking::with(['customer', 'therapist', 'service'])->latest()->get();
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();

        return view('admin.bookings.index', compact('bookings', 'customers', 'therapists', 'services'));
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
        $date     = $request->get('date', now()->toDateString());
        $today    = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();

        $bookings = Booking::with(['customer', 'therapist', 'service'])
            ->whereDate('scheduled_at', $date)
            ->whereIn('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])
            ->get()
            ->map(function ($b) use ($date, $today, $tomorrow) {
                $waUrl = null;

                // Tampilkan tombol WA hanya untuk booking H-0 atau H-1 yang masih scheduled
                $isRemindable = in_array($date, [$today, $tomorrow]) && $b->status === 'scheduled';
                $phone        = $b->customer->phone ?? null;

                if ($isRemindable && $phone) {
                    $phone = preg_replace('/\D/', '', $phone);
                    if (str_starts_with($phone, '0')) {
                        $phone = '62' . substr($phone, 1);
                    }
                    $jadwal  = Carbon::parse($b->scheduled_at)->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i');
                    $msg     = urlencode(
                        "Halo {$b->customer->name}, kami ingin mengingatkan booking Anda:\n\n"
                            . "📋 Layanan : {$b->service->name}\n"
                            . "👤 Terapis : {$b->therapist->name}\n"
                            . "🗓 Jadwal  : {$jadwal}\n\n"
                            . "Mohon hadir tepat waktu. Terima kasih! 🙏"
                    );
                    $waUrl = "https://wa.me/{$phone}?text={$msg}";
                }

                return [
                    'id'            => $b->id,
                    'therapist_id'  => $b->therapist_id,
                    'hour'          => (int) Carbon::parse($b->scheduled_at)->format('H'),
                    'status'        => $b->status,
                    'customer_name' => $b->customer->name ?? '—',
                    'service_name'  => $b->service->name  ?? '—',
                    'final_price'   => $b->final_price,
                    'edit_url'      => route('admin.bookings.edit', $b->id),
                    'wa_url'        => $waUrl,
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

        return view('admin.bookings.create', compact('customers', 'therapists', 'services', 'promos'));
    }

    public function store(Request $request)
    {
        $service   = Service::findOrFail($request->service_id);
        $price     = $service->price;
        $promoDisc = 0;
        $promoId   = $request->promo_id ?: null;

        // Hitung diskon dari promo (kolom `discount` = persentase)
        if ($promoId) {
            $promo = Promo::where('id', $promoId)->where('status', 'aktif')->first();
            if ($promo) {
                $promoDisc = round($price * $promo->discount / 100);
            } else {
                $promoId = null;
            }
        }

        $manualDisc = (int) ($request->discount ?? 0);
        $totalDisc  = $promoDisc + $manualDisc;
        $finalPrice = max(0, $price - $totalDisc);

        Booking::create([
            'customer_id'  => $request->customer_id,
            'therapist_id' => $request->therapist_id,
            'service_id'   => $request->service_id,
            'order_source' => $request->order_source,
            'scheduled_at' => $request->scheduled_at,
            'price'        => $price,
            'promo_id'     => $promoId,
            'discount'     => $totalDisc,
            'final_price'  => $finalPrice,
            'status'       => 'scheduled',
            'notes'        => $request->notes,
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Booking berhasil dibuat.');
    }

    public function edit(Booking $booking)
    {
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update([
            'scheduled_at' => $request->scheduled_at ?? $booking->scheduled_at,
            'status'       => $request->status,
            'notes'        => $request->notes,
        ]);

        if ($booking->status === 'completed' && !$booking->commission()->exists()) {
            $percent = $booking->therapist->commission_percent;
            $amount  = ($booking->price * $percent) / 100;

            Commission::create([
                'booking_id'         => $booking->id,
                'therapist_id'       => $booking->therapist_id,
                'commission_percent' => $percent,
                'commission_amount'  => $amount,
            ]);
        }

        return redirect()->route('admin.bookings.index')->with('success', 'Booking diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();
        return back()->with('success', 'Booking dihapus.');
    }
}
