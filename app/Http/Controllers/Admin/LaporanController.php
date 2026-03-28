<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Payment;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\TherapistAttendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // Konstanta bisnis — ubah di sini jika berubah
    const BONUS_HADIR   = 20000;   // Rp 20.000 per hari hadir
    const KOMISI_PIJAT  = 0.25;    // 25% dari final_price

    public function index(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');

        // ── PERIODE ───────────────────────────────────────────────────────
        $mode       = $request->get('mode',    'bulanan');
        $tanggal    = $request->get('tanggal', $now->toDateString());
        $minggu     = $request->get('minggu',  $now->format('Y-\WW'));
        $bulan      = $request->get('bulan',   $now->format('Y-m'));
        $rangeStart = $request->get('range_start', $now->toDateString());
        $rangeEnd   = $request->get('range_end',   $now->toDateString());

        [$start, $end, $label] = $this->resolveRange($mode, $tanggal, $minggu, $bulan, $rangeStart, $rangeEnd);

        // ── KEUANGAN ──────────────────────────────────────────────────────
        $totalHargaAsli = Booking::completed()->inRange($start, $end)->sum('price');
        $totalDiskon    = Booking::completed()->inRange($start, $end)->sum('discount');
        $totalBruto     = Booking::completed()->inRange($start, $end)->sum('final_price');

        $totalKomisiPijat = Booking::completed()->inRange($start, $end)
            ->sum(DB::raw('final_price * ' . self::KOMISI_PIJAT));

        // Bonus hadir = jumlah hari unik terapis hadir × 20.000
        $totalBonusHadir = TherapistAttendance::whereIn('status', ['present', 'late'])
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->count() * self::BONUS_HADIR;

        $totalKomisiTerapis = $totalKomisiPijat + $totalBonusHadir;
        $totalPendapatan    = $totalBruto - $totalKomisiTerapis;

        $pendapatanQris = Payment::whereHas('booking', fn($q) => $q->whereBetween('scheduled_at', [$start, $end]))
            ->where('method', 'qris')->sum('amount');
        $pendapatanCash = Payment::whereHas('booking', fn($q) => $q->whereBetween('scheduled_at', [$start, $end]))
            ->where('method', 'cash')->sum('amount');

        // ── BOOKING ───────────────────────────────────────────────────────
        $totalBooking   = Booking::inRange($start, $end)->count();
        $bookingSelesai = Booking::completed()->inRange($start, $end)->count();
        $bookingBatal   = Booking::where('status', 'cancelled')->inRange($start, $end)->count();
        $bookingPending = Booking::whereIn('status', ['pending', 'scheduled'])->inRange($start, $end)->count();
        $sumberBooking  = Booking::inRange($start, $end)
            ->selectRaw('order_source, COUNT(*) as total')
            ->groupBy('order_source')->pluck('total', 'order_source');

        // ── REKAP HARIAN (untuk tabel per-hari di dalam periode) ──────────
        $rekapHarian = $this->buildRekapHarian($start, $end);

        // ── LAPORAN TERAPIS ───────────────────────────────────────────────
        $laporanTerapis = $this->buildLaporanTerapis($start, $end);

        // ── TOP LAYANAN ───────────────────────────────────────────────────
        $topLayanan = Service::withCount([
            'bookings as total_sesi' => fn($q) => $q->inRange($start, $end),
        ])->withSum([
            'bookings as total_pendapatan' => fn($q) => $q->completed()->inRange($start, $end),
        ], 'final_price')
            ->having('total_sesi', '>', 0)
            ->orderByDesc('total_sesi')->limit(10)->get();

        // ── CHART DATA ────────────────────────────────────────────────────
        [$chartLabels, $chartBruto, $chartPendapatan, $chartBooking] =
            $this->buildChartData($mode, $start, $end);

        // ── DETAIL TRANSAKSI ──────────────────────────────────────────────
        $detailTransaksi = Booking::with(['customer', 'service', 'therapist', 'payment', 'commission'])
            ->inRange($start, $end)
            ->orderBy('scheduled_at', 'desc')
            ->paginate(15);

        return view('admin.laporan.index', compact(
            'mode',
            'tanggal',
            'minggu',
            'bulan',
            'rangeStart',
            'rangeEnd',
            'label',
            'start',
            'end',
            'totalHargaAsli',
            'totalDiskon',
            'totalBruto',
            'totalKomisiPijat',
            'totalBonusHadir',
            'totalKomisiTerapis',
            'totalPendapatan',
            'pendapatanQris',
            'pendapatanCash',
            'totalBooking',
            'bookingSelesai',
            'bookingBatal',
            'bookingPending',
            'sumberBooking',
            'rekapHarian',
            'laporanTerapis',
            'topLayanan',
            'chartLabels',
            'chartBruto',
            'chartPendapatan',
            'chartBooking',
            'detailTransaksi',
        ));
    }

    // ── EXPORT EXCEL ──────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $now  = Carbon::now('Asia/Jakarta');
        $mode = $request->get('mode', 'bulanan');

        [$start, $end, $label] = $this->resolveRange(
            $mode,
            $request->get('tanggal', $now->toDateString()),
            $request->get('minggu',  $now->format('Y-\WW')),
            $request->get('bulan',   $now->format('Y-m')),
            $request->get('range_start', $now->toDateString()),
            $request->get('range_end',   $now->toDateString()),
        );

        $rekapHarian    = $this->buildRekapHarian($start, $end);
        $laporanTerapis = $this->buildLaporanTerapis($start, $end);
        $absensi        = $this->buildAbsensi($start, $end);
        $transaksi      = Booking::with(['customer', 'service', 'therapist', 'payment'])
            ->inRange($start, $end)
            ->orderBy('scheduled_at')->get();

        $filename = 'laporan_spa_' . str_replace([' ', ',', '/'], '_', $label) . '.xlsx';

        return response()->streamDownload(
            fn() => $this->generateExcel($label, $rekapHarian, $laporanTerapis, $transaksi, $absensi),
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    // ── HELPERS ───────────────────────────────────────────────────────────

    private function resolveRange($mode, $tanggal, $minggu, $bulan, $rangeStart, $rangeEnd): array
    {
        switch ($mode) {
            case 'rentang':
                $start = Carbon::parse($rangeStart, 'Asia/Jakarta')->startOfDay();
                $end   = Carbon::parse($rangeEnd,   'Asia/Jakarta')->endOfDay();
                $label = Carbon::parse($rangeStart)->translatedFormat('d F Y') . ' – ' .
                    Carbon::parse($rangeEnd)->translatedFormat('d F Y');
                break;
            case 'mingguan':
                [$tahun, $week] = explode('-W', $minggu);
                $start = Carbon::now('Asia/Jakarta')->setISODate((int)$tahun, (int)$week)->startOfWeek();
                $end   = $start->copy()->endOfWeek();
                $label = 'Minggu ' . $week . ', ' . $tahun;
                break;
            case 'harian':
                $start = Carbon::parse($tanggal, 'Asia/Jakarta')->startOfDay();
                $end   = Carbon::parse($tanggal, 'Asia/Jakarta')->endOfDay();
                $label = Carbon::parse($tanggal)->translatedFormat('d F Y');
                break;
            default: // bulanan
                [$tahun, $bln] = explode('-', $bulan);
                $start = Carbon::createFromDate((int)$tahun, (int)$bln, 1, 'Asia/Jakarta')->startOfMonth();
                $end   = $start->copy()->endOfMonth();
                $label = $start->translatedFormat('F Y');
                break;
        }
        return [$start, $end, $label];
    }

    private function buildRekapHarian($start, $end): \Illuminate\Support\Collection
    {
        // Ambil data booking per hari
        $bookingPerHari = Booking::completed()->inRange($start, $end)
            ->selectRaw('DATE(scheduled_at) as tgl, SUM(final_price) as bruto, COUNT(*) as sesi')
            ->groupBy('tgl')->get()->keyBy('tgl');

        // Absensi per hari (jumlah terapis hadir)
        $absensiPerHari = TherapistAttendance::whereIn('status', ['present', 'late'])
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('attendance_date as tgl, COUNT(*) as hadir')
            ->groupBy('tgl')->get()->keyBy('tgl');

        $days = collect();
        $date = $start->copy()->startOfDay();
        while ($date <= $end) {
            $tgl     = $date->format('Y-m-d');
            $bruto   = (float) ($bookingPerHari[$tgl]->bruto ?? 0);
            $sesi    = (int)   ($bookingPerHari[$tgl]->sesi  ?? 0);
            $hadir   = (int)   ($absensiPerHari[$tgl]->hadir ?? 0);

            $komisiPijat = $bruto * self::KOMISI_PIJAT;
            $bonusHadir  = $hadir * self::BONUS_HADIR;
            $totalKomisi = $komisiPijat + $bonusHadir;
            $bersih      = $bruto - $totalKomisi;

            $days->push([
                'tanggal'      => $date->copy(),
                'sesi'         => $sesi,
                'bruto'        => $bruto,
                'komisi_pijat' => $komisiPijat,
                'bonus_hadir'  => $bonusHadir,
                'total_komisi' => $totalKomisi,
                'bersih'       => $bersih,
                'hadir'        => $hadir,
            ]);

            $date->addDay();
        }
        return $days;
    }

    private function buildLaporanTerapis($start, $end): \Illuminate\Support\Collection
    {
        $terapis = Therapist::where('is_active', true)
            ->withCount([
                'bookings as total_sesi'    => fn($q) => $q->inRange($start, $end),
                'bookings as sesi_selesai'  => fn($q) => $q->completed()->inRange($start, $end),
            ])
            ->withSum([
                'bookings as total_bruto' => fn($q) => $q->completed()->inRange($start, $end),
            ], 'final_price')
            ->orderByDesc('sesi_selesai')->get();

        $terapis->each(function ($t) use ($start, $end) {
            $bruto = (float) ($t->total_bruto ?? 0);

            // Komisi pijat 25%
            $t->komisi_pijat = $bruto * self::KOMISI_PIJAT;

            // Bonus hadir: hitung hari unik hadir dalam rentang
            $hariHadir = TherapistAttendance::where('therapist_id', $t->id)
                ->whereIn('status', ['present', 'late'])
                ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
                ->count();

            $t->hari_hadir  = $hariHadir;
            $t->bonus_hadir = $hariHadir * self::BONUS_HADIR;
            $t->total_komisi = $t->komisi_pijat + $t->bonus_hadir;
            $t->pendapatan_spa = $bruto - $t->komisi_pijat; // bonus hadir adalah cost spa terpisah
            $t->total_bruto_fmt = $bruto;
        });

        return $terapis;
    }

    private function buildAbsensi($start, $end): \Illuminate\Support\Collection
    {
        return TherapistAttendance::with('therapist')
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->orderBy('attendance_date')
            ->orderBy('therapist_id')
            ->get();
    }

    private function buildChartData($mode, $start, $end): array
    {
        if ($mode === 'harian') {
            $labels = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));
            $raw    = Booking::completed()->inRange($start, $end)
                ->selectRaw('HOUR(scheduled_at) as p, SUM(final_price) as total, COUNT(*) as jml')
                ->groupBy('p')->get()->keyBy('p');
            $bruto      = array_map(fn($h) => (int)($raw[$h]->total ?? 0), range(0, 23));
            $pendapatan = array_map(fn($h) => (int)(($raw[$h]->total ?? 0) * (1 - self::KOMISI_PIJAT)), range(0, 23));
            $booking    = array_map(fn($h) => (int)($raw[$h]->jml   ?? 0), range(0, 23));
        } elseif ($mode === 'mingguan') {
            $labels  = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $dowMap  = [2, 3, 4, 5, 6, 7, 1];
            $raw     = Booking::completed()->inRange($start, $end)
                ->selectRaw('DAYOFWEEK(scheduled_at) as p, SUM(final_price) as total, COUNT(*) as jml')
                ->groupBy('p')->get()->keyBy('p');
            $bruto      = array_map(fn($d) => (int)($raw[$d]->total ?? 0), $dowMap);
            $pendapatan = array_map(fn($d) => (int)(($raw[$d]->total ?? 0) * (1 - self::KOMISI_PIJAT)), $dowMap);
            $booking    = array_map(fn($d) => (int)($raw[$d]->jml   ?? 0), $dowMap);
        } else {
            $days   = $start->daysInMonth ?? $end->diffInDays($start) + 1;
            $labels = array_map(fn($d) => (string)$d, range(1, $days));
            $raw    = Booking::completed()->inRange($start, $end)
                ->selectRaw('DAY(scheduled_at) as p, SUM(final_price) as total, COUNT(*) as jml')
                ->groupBy('p')->get()->keyBy('p');
            $bruto      = array_map(fn($d) => (int)($raw[$d]->total ?? 0), range(1, $days));
            $pendapatan = array_map(fn($d) => (int)(($raw[$d]->total ?? 0) * (1 - self::KOMISI_PIJAT)), range(1, $days));
            $booking    = array_map(fn($d) => (int)($raw[$d]->jml   ?? 0), range(1, $days));
        }
        return [$labels, $bruto, $pendapatan, $booking];
    }

    private function generateExcel($label, $rekapHarian, $laporanTerapis, $transaksi, $absensi): void
    {
        // Bersihkan buffer apapun yang sudah ada
        if (ob_get_level()) {
            ob_end_clean();
        }

        (new \App\Http\Controllers\Admin\LaporanExportController(
            $label,
            $rekapHarian,
            $laporanTerapis,
            $transaksi,
            $absensi
        ))->generate();
    }
}
