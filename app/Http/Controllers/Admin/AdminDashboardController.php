<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Service;
use App\Models\Therapist;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $now   = Carbon::now();
        $today = $now->toDateString();

        // ── STAT CARDS ────────────────────────────────────────────────────
        $todayBookings  = Booking::whereDate('scheduled_at', $today)->count();
        $monthRevenue   = Booking::where('status', 'completed')
            ->whereMonth('scheduled_at', $now->month)
            ->whereYear('scheduled_at', $now->year)
            ->sum('final_price');
        $todayRevenue   = Booking::where('status', 'completed')
            ->whereDate('scheduled_at', $today)
            ->sum('final_price');
        $totalTherapists = Therapist::where('is_active', true)->count();
        $totalCustomers  = Customer::count();

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

        $chartHarian     = array_map(fn($h) => (int)($harianRaw[$h] ?? 0),     range(0, 23));
        $chartHarianPrev = array_map(fn($h) => (int)($harianPrevRaw[$h] ?? 0), range(0, 23));

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

        // MySQL DAYOFWEEK: 1=Sun,2=Mon,...,7=Sat — mapping ke Mon(2)..Sun(1)
        $dowMap = [2, 3, 4, 5, 6, 7, 1];
        $chartMingguan       = array_map(fn($d) => (int)($mingguanRaw[$d] ?? 0),     $dowMap);
        $chartMingguanPrev   = array_map(fn($d) => (int)($mingguanPrevRaw[$d] ?? 0), $dowMap);
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

        $chartBulanan     = array_map(fn($m) => (int)($bulananRaw[$m] ?? 0),     range(1, 12));
        $chartBulananPrev = array_map(fn($m) => (int)($bulananPrevRaw[$m] ?? 0), range(1, 12));

        // ── STOK TERENDAH ─────────────────────────────────────────────────
        // Model Barang menggunakan field: stok_awal, stok_masuk, stok_keluar,
        // stok_minimum, nama_barang, kode_barang, satuan, kategori, status.
        // Accessor kondisi_stok & stok_sistem sudah ada di model Barang.
        $lowStockProducts = Barang::aktif()
            ->whereRaw('(stok_awal + stok_masuk - stok_keluar) <= (IFNULL(stok_minimum, 5) * 2)')
            ->orderByRaw('(stok_awal + stok_masuk - stok_keluar) ASC')
            ->limit(6)
            ->get();

        // ── TABEL & WIDGET LAIN ───────────────────────────────────────────
        $recentBookings = Booking::with(['customer', 'service'])
            ->latest('scheduled_at')
            ->limit(8)
            ->get();

        $unpaidBookings = Booking::with(['customer', 'service'])
            ->where('status', 'scheduled')
            ->whereDoesntHave('payment')
            ->latest('scheduled_at')
            ->limit(5)
            ->get();

        $topServices = Service::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->limit(5)
            ->get();

        $therapists = Therapist::where('is_active', true)->get();

        // Prioritas terapis hari ini (sesi kemarin paling sedikit)
        $prioritasHariIni = Therapist::where('is_active', true)
            ->withCount([
                'bookings as sesi_kemarin' => fn($q) =>
                $q->whereDate('scheduled_at', $now->copy()->subDay()->toDateString()),
                'bookings as sesi_hari_ini' => fn($q) =>
                $q->whereDate('scheduled_at', $today),
            ])
            ->orderBy('sesi_kemarin', 'asc')
            ->limit(6)
            ->get();

        return view('admin.dashboard', compact(
            // Stats
            'todayBookings',
            'monthRevenue',
            'todayRevenue',
            'totalTherapists',
            'totalCustomers',
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
            // Stok
            'lowStockProducts',
            // Tabel
            'recentBookings',
            'unpaidBookings',
            'topServices',
            'therapists',
            'prioritasHariIni',
        ));
    }
}
