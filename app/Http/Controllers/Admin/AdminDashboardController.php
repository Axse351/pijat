<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\Customer;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $today     = Carbon::today();
        $yesterday = Carbon::yesterday();

        // ── Stats ──────────────────────────────────────────────────────
        $todayBookings   = Booking::whereDate('scheduled_at', $today)->count();
        $monthRevenue    = Booking::whereMonth('scheduled_at', $today->month)
            ->whereYear('scheduled_at', $today->year)
            ->where('status', 'completed')
            ->sum('final_price');
        $totalTherapists = Therapist::where('is_active', 1)->count();
        $totalCustomers  = Customer::count();

        // ── Recent bookings ────────────────────────────────────────────
        $recentBookings = Booking::with(['customer', 'service'])
            ->latest()
            ->take(6)
            ->get();

        // ── Unpaid bookings ────────────────────────────────────────────
        $unpaidBookings = Booking::with(['customer', 'service'])
            ->whereDoesntHave('payment')
            ->where('status', 'completed')
            ->latest()
            ->take(5)
            ->get();

        // ── Top services ───────────────────────────────────────────────
        $topServices = Service::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->take(5)
            ->get();

        // ── Therapists (for status card) ───────────────────────────────
        $therapists = Therapist::where('is_active', 1)->take(6)->get();

        // ── Terapis paling sedikit sesi KEMARIN → prioritas hari ini ──
        //    Hitung booking completed/scheduled/ongoing kemarin per terapis.
        //    Terapis aktif yang tidak punya booking kemarin tetap masuk (count = 0).
        $therapistYesterdayCount = Booking::selectRaw('therapist_id, COUNT(*) as sesi_kemarin')
            ->whereDate('scheduled_at', $yesterday)
            ->whereIn('status', ['scheduled', 'ongoing', 'completed'])
            ->groupBy('therapist_id')
            ->pluck('sesi_kemarin', 'therapist_id');

        // Gabungkan semua terapis aktif dengan jumlah sesi kemarin
        $prioritasHariIni = Therapist::where('is_active', 1)
            ->get()
            ->map(function ($t) use ($therapistYesterdayCount) {
                $t->sesi_kemarin = $therapistYesterdayCount->get($t->id, 0);
                return $t;
            })
            ->sortBy('sesi_kemarin')   // paling sedikit → paling atas
            ->take(6)
            ->values();

        // Jumlah sesi terapis hari ini (untuk info tambahan)
        $therapistTodayCount = Booking::selectRaw('therapist_id, COUNT(*) as sesi_hari_ini')
            ->whereDate('scheduled_at', $today)
            ->whereIn('status', ['scheduled', 'ongoing', 'completed'])
            ->groupBy('therapist_id')
            ->pluck('sesi_hari_ini', 'therapist_id');

        $prioritasHariIni = $prioritasHariIni->map(function ($t) use ($therapistTodayCount) {
            $t->sesi_hari_ini = $therapistTodayCount->get($t->id, 0);
            return $t;
        });

        return view('admin.dashboard', compact(
            'todayBookings',
            'monthRevenue',
            'totalTherapists',
            'totalCustomers',
            'recentBookings',
            'unpaidBookings',
            'topServices',
            'therapists',
            'prioritasHariIni',
        ));
    }
}
