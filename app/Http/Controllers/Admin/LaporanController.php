<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\Therapist;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $now = Carbon::now();

        // ── PERIODE ───────────────────────────────────────────────────────
        $mode      = $request->get('mode', 'harian');   // harian | mingguan | bulanan
        $tanggal   = $request->get('tanggal',  $now->toDateString());
        $minggu    = $request->get('minggu',   $now->format('Y-\WW'));
        $bulan     = $request->get('bulan',    $now->format('Y-m'));

        // ── HITUNG RENTANG TANGGAL BERDASARKAN MODE ───────────────────────
        switch ($mode) {
            case 'mingguan':
                [$tahun, $week] = explode('-W', $minggu);
                $start = Carbon::now()->setISODate((int)$tahun, (int)$week)->startOfWeek();
                $end   = $start->copy()->endOfWeek();
                $label = 'Minggu ' . $week . ', ' . $tahun;
                break;

            case 'bulanan':
                [$tahun, $bln] = explode('-', $bulan);
                $start = Carbon::createFromDate((int)$tahun, (int)$bln, 1)->startOfMonth();
                $end   = $start->copy()->endOfMonth();
                $label = $start->translatedFormat('F Y');
                break;

            default: // harian
                $start = Carbon::parse($tanggal)->startOfDay();
                $end   = Carbon::parse($tanggal)->endOfDay();
                $label = Carbon::parse($tanggal)->translatedFormat('d F Y');
                break;
        }

        // ── LAPORAN KEUANGAN ──────────────────────────────────────────────
        $totalPendapatan = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$start, $end])
            ->sum('final_price');

        $totalDiskon = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$start, $end])
            ->sum('discount');

        $totalBruto = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$start, $end])
            ->sum('price');

        $pendapatanQris = Payment::whereHas(
            'booking',
            fn($q) =>
            $q->whereBetween('scheduled_at', [$start, $end])
        )->where('method', 'qris')->sum('amount');

        $pendapatanCash = Payment::whereHas(
            'booking',
            fn($q) =>
            $q->whereBetween('scheduled_at', [$start, $end])
        )->where('method', 'cash')->sum('amount');

        // ── LAPORAN BOOKING ───────────────────────────────────────────────
        $totalBooking    = Booking::whereBetween('scheduled_at', [$start, $end])->count();
        $bookingSelesai  = Booking::where('status', 'completed')->whereBetween('scheduled_at', [$start, $end])->count();
        $bookingBatal    = Booking::where('status', 'cancelled')->whereBetween('scheduled_at', [$start, $end])->count();
        $bookingPending  = Booking::whereIn('status', ['pending', 'scheduled'])->whereBetween('scheduled_at', [$start, $end])->count();

        // Sumber booking
        $sumberBooking = Booking::whereBetween('scheduled_at', [$start, $end])
            ->selectRaw('order_source, COUNT(*) as total')
            ->groupBy('order_source')
            ->pluck('total', 'order_source');

        // ── LAPORAN LAYANAN TERPOPULER ────────────────────────────────────
        $topLayanan = Service::withCount([
            'bookings as total_sesi' => fn($q) =>
            $q->whereBetween('scheduled_at', [$start, $end]),
        ])
            ->withSum([
                'bookings as total_pendapatan' => fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end]),
            ], 'final_price')
            ->having('total_sesi', '>', 0)
            ->orderByDesc('total_sesi')
            ->limit(10)
            ->get();

        // ── LAPORAN TERAPIS ───────────────────────────────────────────────
        $laporanTerapis = Therapist::where('is_active', true)
            ->withCount([
                'bookings as total_sesi' => fn($q) =>
                $q->whereBetween('scheduled_at', [$start, $end]),
                'bookings as sesi_selesai' => fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end]),
            ])
            ->withSum([
                'bookings as total_pendapatan' => fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end]),
            ], 'final_price')
            ->orderByDesc('total_sesi')
            ->get();

        // ── CHART DATA ────────────────────────────────────────────────────
        if ($mode === 'harian') {
            // Per jam
            $chartLabels = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));
            $raw = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->selectRaw('HOUR(scheduled_at) as period, SUM(final_price) as total, COUNT(*) as jumlah')
                ->groupBy('period')
                ->get()->keyBy('period');
            $chartPendapatan = array_map(fn($h) => (int)($raw[$h]->total  ?? 0), range(0, 23));
            $chartBooking    = array_map(fn($h) => (int)($raw[$h]->jumlah ?? 0), range(0, 23));
        } elseif ($mode === 'mingguan') {
            // Per hari (Sen-Min)
            $chartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $dowMap = [2, 3, 4, 5, 6, 7, 1];
            $raw = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->selectRaw('DAYOFWEEK(scheduled_at) as period, SUM(final_price) as total, COUNT(*) as jumlah')
                ->groupBy('period')
                ->get()->keyBy('period');
            $chartPendapatan = array_map(fn($d) => (int)($raw[$d]->total  ?? 0), $dowMap);
            $chartBooking    = array_map(fn($d) => (int)($raw[$d]->jumlah ?? 0), $dowMap);
        } else {
            // Per tanggal dalam bulan
            $daysInMonth = $start->daysInMonth;
            $chartLabels = array_map(fn($d) => (string)$d, range(1, $daysInMonth));
            $raw = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->selectRaw('DAY(scheduled_at) as period, SUM(final_price) as total, COUNT(*) as jumlah')
                ->groupBy('period')
                ->get()->keyBy('period');
            $chartPendapatan = array_map(fn($d) => (int)($raw[$d]->total  ?? 0), range(1, $daysInMonth));
            $chartBooking    = array_map(fn($d) => (int)($raw[$d]->jumlah ?? 0), range(1, $daysInMonth));
        }

        // ── DETAIL TRANSAKSI ──────────────────────────────────────────────
        $detailTransaksi = Booking::with(['customer', 'service', 'therapist', 'payment'])
            ->whereBetween('scheduled_at', [$start, $end])
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        return view('admin.laporan.index', compact(
            'mode',
            'tanggal',
            'minggu',
            'bulan',
            'label',
            'start',
            'end',
            // Keuangan
            'totalPendapatan',
            'totalDiskon',
            'totalBruto',
            'pendapatanQris',
            'pendapatanCash',
            // Booking
            'totalBooking',
            'bookingSelesai',
            'bookingBatal',
            'bookingPending',
            'sumberBooking',
            // Layanan & Terapis
            'topLayanan',
            'laporanTerapis',
            // Chart
            'chartLabels',
            'chartPendapatan',
            'chartBooking',
            // Detail
            'detailTransaksi',
        ));
    }
}
