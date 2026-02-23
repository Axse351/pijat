<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\Commission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index()
    {
    $bookings   = Booking::with(['customer','therapist','service'])->latest()->get();
    $customers  = Customer::all();
    $therapists = Therapist::where('is_active', 1)->get();
    $services   = Service::all();

    return view('admin.bookings.index', compact('bookings', 'customers', 'therapists', 'services'));
    }

    /**
     * Tampilkan halaman kalender booking.
     */
    public function calendar()
    {
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();

        return view('admin.bookings.calendar', compact('customers', 'therapists', 'services'));
    }

    /**
     * API: kembalikan booking pada tanggal tertentu (format JSON untuk kalender).
     * GET /admin/bookings/calendar-data?date=2025-06-01
     */
    public function calendarData(Request $request)
    {
        $date = $request->get('date', now()->toDateString());

        $bookings = Booking::with(['customer','therapist','service'])
            ->whereDate('scheduled_at', $date)
            ->whereIn('status', ['scheduled','ongoing','completed','cancelled'])
            ->get()
            ->map(function ($b) {
                return [
                    'id'            => $b->id,
                    'therapist_id'  => $b->therapist_id,
                    'hour'          => (int) Carbon::parse($b->scheduled_at)->format('H'),
                    'status'        => $b->status,
                    'customer_name' => $b->customer->name ?? '—',
                    'service_name'  => $b->service->name  ?? '—',
                    'final_price'   => $b->final_price,
                    'edit_url'      => route('admin.bookings.edit', $b->id),
                ];
            });

        return response()->json(['bookings' => $bookings]);
    }

    public function create()
    {
        $customers  = Customer::all();
        $therapists = Therapist::where('is_active', 1)->get();
        $services   = Service::all();

        return view('admin.bookings.create', compact('customers', 'therapists', 'services'));
    }

    public function store(Request $request)
    {
        $service = Service::findOrFail($request->service_id);

        $price      = $service->price;
        $discount   = $request->discount ?? 0;
        $finalPrice = max(0, $price - $discount);

        Booking::create([
            'customer_id'  => $request->customer_id,
            'therapist_id' => $request->therapist_id,
            'service_id'   => $request->service_id,
            'order_source' => $request->order_source,
            'scheduled_at' => $request->scheduled_at,
            'price'        => $price,
            'discount'     => $discount,
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

        // Jika status completed → buat komisi (jika belum ada)
        if ($booking->status === 'completed' && !$booking->commission()->exists()) {
            $percent = $booking->therapist->commission_percent;
            $amount  = ($booking->price * $percent) / 100;

            Commission::create([
                'booking_id'        => $booking->id,
                'therapist_id'      => $booking->therapist_id,
                'commission_percent'=> $percent,
                'commission_amount' => $amount,
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
