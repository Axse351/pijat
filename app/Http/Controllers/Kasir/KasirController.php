<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Therapist;
use Carbon\Carbon;

class KasirDashboardController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        // ── STAT CARDS ────────────────────────────────────────────────────
        $todayBookings = Booking::whereDate('scheduled_at', $today)->count();

        $todayRevenue = Booking::where('status', 'completed')
            ->whereDate('scheduled_at', $today)
            ->sum('final_price');

        $monthRevenue = Booking::where('status', 'completed')
            ->whereMonth('scheduled_at', $now->month)
            ->whereYear('scheduled_at', $now->year)
            ->sum('final_price');

        $pendingCount = Booking::where('status', 'scheduled')
            ->whereDoesntHave('payment')
            ->count();

        // ── CHART HARIAN (per jam, hari ini vs kemarin) ───────────────────
        $chartLabelsHarian = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));

        $harianRaw = Booking::where('status', 'completed')
            ->whereDate('scheduled_at', $today)
            ->selectRaw('HOUR(scheduled_at) as jam, SUM(final_price) as total')
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $harianPrevRaw = Booking::where('status', 'completed')
            ->whereDate('scheduled_at', $now->copy()->subDay()->toDateString())
            ->selectRaw('HOUR(scheduled_at) as jam, SUM(final_price) as total')
            ->groupBy('jam')
            ->pluck('total', 'jam');

        $chartHarian     = array_map(fn($h) => (int) ($harianRaw[$h] ?? 0),     range(0, 23));
        $chartHarianPrev = array_map(fn($h) => (int) ($harianPrevRaw[$h] ?? 0), range(0, 23));

        // ── CHART MINGGUAN (Sen–Min, minggu ini vs minggu lalu) ───────────
        $startWeek     = $now->copy()->startOfWeek();
        $startLastWeek = $now->copy()->subWeek()->startOfWeek();
        $dayLabels     = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        $mingguanRaw = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$startWeek, $startWeek->copy()->endOfWeek()])
            ->selectRaw('DAYOFWEEK(scheduled_at) as dow, SUM(final_price) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $mingguanPrevRaw = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$startLastWeek, $startLastWeek->copy()->endOfWeek()])
            ->selectRaw('DAYOFWEEK(scheduled_at) as dow, SUM(final_price) as total')
            ->groupBy('dow')
            ->pluck('total', 'dow');

        $dowMap              = [2, 3, 4, 5, 6, 7, 1];
        $chartMingguan       = array_map(fn($d) => (int) ($mingguanRaw[$d] ?? 0),     $dowMap);
        $chartMingguanPrev   = array_map(fn($d) => (int) ($mingguanPrevRaw[$d] ?? 0), $dowMap);
        $chartLabelsMingguan = $dayLabels;

        // ── CHART BULANAN (Jan–Des, tahun ini vs tahun lalu) ──────────────
        $chartLabelsBulanan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $bulananRaw = Booking::where('status', 'completed')
            ->whereYear('scheduled_at', $now->year)
            ->selectRaw('MONTH(scheduled_at) as bln, SUM(final_price) as total')
            ->groupBy('bln')
            ->pluck('total', 'bln');

        $bulananPrevRaw = Booking::where('status', 'completed')
            ->whereYear('scheduled_at', $now->year - 1)
            ->selectRaw('MONTH(scheduled_at) as bln, SUM(final_price) as total')
            ->groupBy('bln')
            ->pluck('total', 'bln');

        $chartBulanan     = array_map(fn($m) => (int) ($bulananRaw[$m] ?? 0),     range(1, 12));
        $chartBulananPrev = array_map(fn($m) => (int) ($bulananPrevRaw[$m] ?? 0), range(1, 12));

        // ── BOOKING HARI INI (semua status) ───────────────────────────────
        $todayBookingList = Booking::with(['customer', 'service', 'therapist', 'payment'])
            ->whereDate('scheduled_at', $today)
            ->orderBy('scheduled_at', 'asc')
            ->get();

        // ── BELUM DIBAYAR ─────────────────────────────────────────────────
        $unpaidBookings = Booking::with(['customer', 'service'])
            ->where('status', 'scheduled')
            ->whereDoesntHave('payment')
            ->latest('scheduled_at')
            ->limit(8)
            ->get();

        // ── TRANSAKSI PEMBAYARAN TERBARU ──────────────────────────────────
        $recentPayments = Payment::with(['booking.customer', 'booking.service'])
            ->latest()
            ->limit(8)
            ->get();

        // ── METODE PEMBAYARAN HARI INI ────────────────────────────────────
        $paymentMethods = Payment::whereHas('booking', function ($q) use ($today) {
            $q->whereDate('scheduled_at', $today);
        })
            ->selectRaw('payment_method, COUNT(*) as total, SUM(amount) as jumlah')
            ->groupBy('payment_method')
            ->get();

        // ── STATUS BOOKING HARI INI (ringkasan) ──────────────────────────
        $statusSummary = Booking::whereDate('scheduled_at', $today)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── LAYANAN TERPOPULER HARI INI ────────────────────────────────────
        $topServicesToday = Service::withCount([
            'bookings as bookings_count' => fn($q) =>
            $q->whereDate('scheduled_at', $today),
        ])
            ->having('bookings_count', '>', 0)
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        // ── TERAPIS AKTIF HARI INI ────────────────────────────────────────
        $therapistsToday = Therapist::where('is_active', true)
            ->withCount([
                'bookings as sesi_hari_ini' => fn($q) =>
                $q->whereDate('scheduled_at', $today),
            ])
            ->orderByDesc('sesi_hari_ini')
            ->get();

        return view('kasir.dashboard', compact(
            // Stats
            'todayBookings',
            'todayRevenue',
            'monthRevenue',
            'pendingCount',
            // Chart harian
            'chartLabelsHarian',
            'chartHarian',
            'chartHarianPrev',
            // Chart mingguan
            'chartLabelsMingguan',
            'chartMingguan',
            'chartMingguanPrev',
            // Chart bulanan
            'chartLabelsBulanan',
            'chartBulanan',
            'chartBulananPrev',
            // Tabel & widget
            'todayBookingList',
            'unpaidBookings',
            'recentPayments',
            'paymentMethods',
            'statusSummary',
            'topServicesToday',
            'therapistsToday',
        ));
    }
}
