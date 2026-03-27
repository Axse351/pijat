<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
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
        $mode    = $request->get('mode',    'harian');
        $tanggal = $request->get('tanggal', $now->toDateString());
        $minggu  = $request->get('minggu',  $now->format('Y-\WW'));
        $bulan   = $request->get('bulan',   $now->format('Y-m'));

        // ── RENTANG TANGGAL ───────────────────────────────────────────────
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

        // Harga asli sebelum diskon
        $totalHargaAsli = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$start, $end])
            ->sum('price');

        // Total diskon yang diberikan ke pelanggan
        $totalDiskon = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$start, $end])
            ->sum('discount');

        // Total uang masuk dari pelanggan (setelah diskon) = BRUTO
        $totalBruto = Booking::where('status', 'completed')
            ->whereBetween('scheduled_at', [$start, $end])
            ->sum('final_price');

        // Total komisi terapis — ambil dari tabel commissions
        $totalKomisiTerapis = Commission::whereHas('booking', function ($q) use ($start, $end) {
            $q->where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end]);
        })->sum('commission_amount');

        // Fallback: jika tabel commissions belum terisi, hitung manual dari commission_percent
        if ($totalKomisiTerapis == 0 && $totalBruto > 0) {
            $totalKomisiTerapis = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->whereNotNull('therapist_id')
                ->with('therapist:id,commission_percent')
                ->get()
                ->sum(function ($booking) {
                    $pct = $booking->therapist->commission_percent ?? 0;
                    return $booking->final_price * ($pct / 100);
                });
        }

        // Pendapatan BERSIH SPA = Bruto - Komisi Terapis
        $totalPendapatan = $totalBruto - $totalKomisiTerapis;

        // Breakdown metode pembayaran
        $pendapatanQris = Payment::whereHas(
            'booking',
            fn($q) => $q->whereBetween('scheduled_at', [$start, $end])
        )->where('method', 'qris')->sum('amount');

        $pendapatanCash = Payment::whereHas(
            'booking',
            fn($q) => $q->whereBetween('scheduled_at', [$start, $end])
        )->where('method', 'cash')->sum('amount');

        // ── LAPORAN BOOKING ───────────────────────────────────────────────
        $totalBooking   = Booking::whereBetween('scheduled_at', [$start, $end])->count();
        $bookingSelesai = Booking::where('status', 'completed')->whereBetween('scheduled_at', [$start, $end])->count();
        $bookingBatal   = Booking::where('status', 'cancelled')->whereBetween('scheduled_at', [$start, $end])->count();
        $bookingPending = Booking::whereIn('status', ['pending', 'scheduled'])->whereBetween('scheduled_at', [$start, $end])->count();

        $sumberBooking = Booking::whereBetween('scheduled_at', [$start, $end])
            ->selectRaw('order_source, COUNT(*) as total')
            ->groupBy('order_source')
            ->pluck('total', 'order_source');

        // ── LAPORAN LAYANAN ───────────────────────────────────────────────
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
                'bookings as total_pendapatan_bruto' => fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end]),
            ], 'final_price')
            ->orderByDesc('total_sesi')
            ->get();

        // Tambahkan total komisi per terapis
        $laporanTerapis->each(function ($terapis) use ($start, $end) {
            $komisi = Commission::where('therapist_id', $terapis->id)
                ->whereHas(
                    'booking',
                    fn($q) =>
                    $q->whereBetween('scheduled_at', [$start, $end])
                )
                ->sum('commission_amount');

            // Fallback manual
            if ($komisi == 0 && ($terapis->total_pendapatan_bruto ?? 0) > 0) {
                $komisi = $terapis->total_pendapatan_bruto * ($terapis->commission_percent / 100);
            }

            $terapis->total_komisi     = $komisi;
            $terapis->pendapatan_spa   = ($terapis->total_pendapatan_bruto ?? 0) - $komisi;
            // alias agar kolom "Pendapatan" di view tetap menampilkan bruto per terapis
            $terapis->total_pendapatan = $terapis->total_pendapatan_bruto ?? 0;
        });

        // ── CHART DATA ────────────────────────────────────────────────────
        if ($mode === 'harian') {
            $chartLabels = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));

            $raw = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->selectRaw('HOUR(scheduled_at) as period, SUM(final_price) as total, COUNT(*) as jumlah')
                ->groupBy('period')->get()->keyBy('period');

            $rawKomisi = Commission::whereHas(
                'booking',
                fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end])
            )
                ->join('bookings', 'commissions.booking_id', '=', 'bookings.id')
                ->selectRaw('HOUR(bookings.scheduled_at) as period, SUM(commissions.commission_amount) as total_komisi')
                ->groupBy('period')->get()->keyBy('period');

            $chartBruto      = array_map(fn($h) => (int)($raw[$h]->total ?? 0), range(0, 23));
            $chartPendapatan = array_map(fn($h) => (int)(($raw[$h]->total ?? 0) - ($rawKomisi[$h]->total_komisi ?? 0)), range(0, 23));
            $chartBooking    = array_map(fn($h) => (int)($raw[$h]->jumlah ?? 0), range(0, 23));
        } elseif ($mode === 'mingguan') {
            $chartLabels = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $dowMap      = [2, 3, 4, 5, 6, 7, 1];

            $raw = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->selectRaw('DAYOFWEEK(scheduled_at) as period, SUM(final_price) as total, COUNT(*) as jumlah')
                ->groupBy('period')->get()->keyBy('period');

            $rawKomisi = Commission::whereHas(
                'booking',
                fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end])
            )
                ->join('bookings', 'commissions.booking_id', '=', 'bookings.id')
                ->selectRaw('DAYOFWEEK(bookings.scheduled_at) as period, SUM(commissions.commission_amount) as total_komisi')
                ->groupBy('period')->get()->keyBy('period');

            $chartBruto      = array_map(fn($d) => (int)($raw[$d]->total ?? 0), $dowMap);
            $chartPendapatan = array_map(fn($d) => (int)(($raw[$d]->total ?? 0) - ($rawKomisi[$d]->total_komisi ?? 0)), $dowMap);
            $chartBooking    = array_map(fn($d) => (int)($raw[$d]->jumlah ?? 0), $dowMap);
        } else {
            $daysInMonth = $start->daysInMonth;
            $chartLabels = array_map(fn($d) => (string)$d, range(1, $daysInMonth));

            $raw = Booking::where('status', 'completed')
                ->whereBetween('scheduled_at', [$start, $end])
                ->selectRaw('DAY(scheduled_at) as period, SUM(final_price) as total, COUNT(*) as jumlah')
                ->groupBy('period')->get()->keyBy('period');

            $rawKomisi = Commission::whereHas(
                'booking',
                fn($q) =>
                $q->where('status', 'completed')->whereBetween('scheduled_at', [$start, $end])
            )
                ->join('bookings', 'commissions.booking_id', '=', 'bookings.id')
                ->selectRaw('DAY(bookings.scheduled_at) as period, SUM(commissions.commission_amount) as total_komisi')
                ->groupBy('period')->get()->keyBy('period');

            $chartBruto      = array_map(fn($d) => (int)($raw[$d]->total ?? 0), range(1, $daysInMonth));
            $chartPendapatan = array_map(fn($d) => (int)(($raw[$d]->total ?? 0) - ($rawKomisi[$d]->total_komisi ?? 0)), range(1, $daysInMonth));
            $chartBooking    = array_map(fn($d) => (int)($raw[$d]->jumlah ?? 0), range(1, $daysInMonth));
        }

        // ── DETAIL TRANSAKSI ──────────────────────────────────────────────
        $detailTransaksi = Booking::with(['customer', 'service', 'therapist', 'payment', 'commission'])
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
            'totalHargaAsli',
            'totalDiskon',
            'totalBruto',
            'totalKomisiTerapis',
            'totalPendapatan',
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
            'chartBruto',
            'chartBooking',
            // Detail
            'detailTransaksi',
        ));
    }
}
