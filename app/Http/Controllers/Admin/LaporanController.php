<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Commission;
use App\Models\Payment;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\TherapistAttendance;
use App\Services\CommissionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // ── Konstanta bisnis ──────────────────────────────────────────────────
    const BONUS_HADIR   = 20000;
    const RATE_STANDARD = 0.25;   // 25%
    const RATE_PROGRAM  = 0.30;   // 30%

    public function index(Request $request)
    {
        $now = Carbon::now('Asia/Jakarta');

        // ── PERIODE ───────────────────────────────────────────────────────
        $mode       = $request->get('mode',       'bulanan');
        $tanggal    = $request->get('tanggal',    $now->toDateString());
        $minggu     = $request->get('minggu',     $now->format('Y-\WW'));
        $bulan      = $request->get('bulan',      $now->format('Y-m'));
        $rangeStart = $request->get('range_start', $now->toDateString());
        $rangeEnd   = $request->get('range_end',   $now->toDateString());

        [$start, $end, $label] = $this->resolveRange($mode, $tanggal, $minggu, $bulan, $rangeStart, $rangeEnd);

        // ══════════════════════════════════════════════════════════════════
        //  KEUANGAN — INCOME "REAL"
        //
        //  Prinsip:
        //  ┌─────────────────────────────────────────────────────────────┐
        //  │ Bruto       = total final_price semua booking COMPLETED      │
        //  │ Kom.Std     = bruto booking standard × 25%                  │
        //  │ Kom.Program = bruto booking program  × 30%                  │
        //  │ Kom.Cancel  = total pembayaran booking cancel+forfeit        │
        //  │ Koichi Real = Bruto − Kom.Std − Kom.Program                 │
        //  │               (Kom.Cancel tidak mengurangi Koichi karena    │
        //  │                uang memang tidak masuk ke Koichi)            │
        //  └─────────────────────────────────────────────────────────────┘
        // ══════════════════════════════════════════════════════════════════

        // Booking completed — pisah standard vs program
        $completedBase = Booking::completed()->inRange($start, $end);

        $brutoStandard = (clone $completedBase)
            ->where('commission_type', 'standard')
            ->sum('final_price');

        $brutoProgram = (clone $completedBase)
            ->where('commission_type', 'program')
            ->sum('final_price');

        $totalBruto        = $brutoStandard + $brutoProgram;
        $totalHargaAsli    = (clone $completedBase)->sum('price');
        $totalDiskon       = (clone $completedBase)->sum('discount');

        // Komisi dari sesi selesai (dicatat di tabel commissions)
        $komisiFromSessions = Commission::where('commission_source', 'normal')
            ->whereHas('booking', fn($q) => $q->inRange($start, $end))
            ->sum('commission_amount');

        // Komisi dari cancel forfeit (uang yg hangus ke terapis)
        $komisiFromCancels = Commission::where('commission_source', 'cancel_forfeit')
            ->whereHas('booking', fn($q) => $q->inRange($start, $end))
            ->sum('commission_amount');

        // Breakdown komisi per rate
        $komisiStandard = round($brutoStandard * self::RATE_STANDARD, 2);
        $komisiProgram  = round($brutoProgram  * self::RATE_PROGRAM,  2);

        // Total komisi terapis dari sesi (25%/30%)
        $totalKomisiPijat = $komisiStandard + $komisiProgram;

        // Bonus hadir
        $totalBonusHadir = TherapistAttendance::whereIn('status', ['present', 'late'])
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->count() * self::BONUS_HADIR;

        $totalKomisiTerapis = $totalKomisiPijat + $totalBonusHadir;

        // Pendapatan REAL Koichi = bruto - komisi sesi (bukan dari cancel forfeit)
        // Cancel forfeit: uang memang tidak masuk ke Koichi, langsung ke terapis
        $totalPendapatan = $totalBruto - $totalKomisiPijat - $totalBonusHadir;

        // Metode pembayaran
        $pendapatanQris = Payment::whereHas('booking', fn($q) => $q->whereBetween('scheduled_at', [$start, $end]))
            ->where('method', 'qris')->sum('amount');
        $pendapatanCash = Payment::whereHas('booking', fn($q) => $q->whereBetween('scheduled_at', [$start, $end]))
            ->where('method', 'cash')->sum('amount');

        // Booking counts
        $totalBooking   = Booking::inRange($start, $end)->count();
        $bookingSelesai = Booking::completed()->inRange($start, $end)->count();
        $bookingBatal   = Booking::cancelled()->inRange($start, $end)->count();
        $bookingPending = Booking::whereIn('status', ['pending', 'scheduled'])->inRange($start, $end)->count();
        $sumberBooking  = Booking::inRange($start, $end)
            ->selectRaw('order_source, COUNT(*) as total')
            ->groupBy('order_source')->pluck('total', 'order_source');

        // Cancel forfeit count & amount
        $cancelForfeitCount  = Booking::cancelled()->inRange($start, $end)->where('is_specific_therapist', true)
            ->whereHas('payment')->count();

        // Rekap harian
        $rekapHarian = $this->buildRekapHarian($start, $end);

        // Laporan terapis
        $laporanTerapis = $this->buildLaporanTerapis($start, $end);

        // Top layanan
        $topLayanan = Service::withCount([
            'bookings as total_sesi' => fn($q) => $q->inRange($start, $end),
        ])->withSum([
            'bookings as total_pendapatan' => fn($q) => $q->completed()->inRange($start, $end),
        ], 'final_price')
            ->having('total_sesi', '>', 0)
            ->orderByDesc('total_sesi')->limit(10)->get();

        // Chart
        [$chartLabels, $chartBruto, $chartPendapatan, $chartBooking] =
            $this->buildChartData($mode, $start, $end);

        // Detail transaksi
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
            'brutoStandard',
            'brutoProgram',
            'komisiStandard',
            'komisiProgram',
            'komisiFromSessions',
            'komisiFromCancels',
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
            'cancelForfeitCount',
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

    // ── EXPORT ────────────────────────────────────────────────────────────
    public function export(Request $request)
    {
        $now  = Carbon::now('Asia/Jakarta');
        $mode = $request->get('mode', 'bulanan');

        [$start, $end, $label] = $this->resolveRange(
            $mode,
            $request->get('tanggal',     $now->toDateString()),
            $request->get('minggu',      $now->format('Y-\WW')),
            $request->get('bulan',       $now->format('Y-m')),
            $request->get('range_start', $now->toDateString()),
            $request->get('range_end',   $now->toDateString()),
        );

        $rekapHarian    = $this->buildRekapHarian($start, $end);
        $laporanTerapis = $this->buildLaporanTerapis($start, $end);
        $absensi        = $this->buildAbsensi($start, $end);
        $transaksi      = Booking::with(['customer', 'service', 'therapist', 'payment'])
            ->inRange($start, $end)->orderBy('scheduled_at')->get();

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
        // Booking completed per hari — pisah standard vs program
        $stdPerHari = Booking::completed()->inRange($start, $end)
            ->where('commission_type', 'standard')
            ->selectRaw('DATE(scheduled_at) as tgl, SUM(final_price) as bruto, COUNT(*) as sesi')
            ->groupBy('tgl')->get()->keyBy('tgl');

        $progPerHari = Booking::completed()->inRange($start, $end)
            ->where('commission_type', 'program')
            ->selectRaw('DATE(scheduled_at) as tgl, SUM(final_price) as bruto, COUNT(*) as sesi')
            ->groupBy('tgl')->get()->keyBy('tgl');

        // Absensi per hari
        $absensiPerHari = TherapistAttendance::whereIn('status', ['present', 'late'])
            ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('attendance_date as tgl, COUNT(*) as hadir')
            ->groupBy('tgl')->get()->keyBy('tgl');

        // Cancel forfeit per hari
        $cancelPerHari = Commission::where('commission_source', 'cancel_forfeit')
            ->whereHas('booking', fn($q) => $q->inRange($start, $end))
            ->selectRaw('DATE(created_at) as tgl, SUM(commission_amount) as total')
            ->groupBy('tgl')->get()->keyBy('tgl');

        $days = collect();
        $date = $start->copy()->startOfDay();

        while ($date <= $end) {
            $tgl = $date->format('Y-m-d');

            $brutoStd  = (float)($stdPerHari[$tgl]->bruto  ?? 0);
            $brutoProg = (float)($progPerHari[$tgl]->bruto ?? 0);
            $bruto     = $brutoStd + $brutoProg;
            $sesi      = (int)($stdPerHari[$tgl]->sesi  ?? 0) + (int)($progPerHari[$tgl]->sesi ?? 0);
            $hadir     = (int)($absensiPerHari[$tgl]->hadir ?? 0);

            $komisiStd  = $brutoStd  * self::RATE_STANDARD;
            $komisiProg = $brutoProg * self::RATE_PROGRAM;
            $komisiPijat = $komisiStd + $komisiProg;
            $bonusHadir  = $hadir * self::BONUS_HADIR;
            $totalKomisi = $komisiPijat + $bonusHadir;
            $bersih      = $bruto - $totalKomisi;

            // Cancel forfeit hari ini (info saja, tidak mengurangi bersih Koichi)
            $cancelForfeit = (float)($cancelPerHari[$tgl]->total ?? 0);

            $days->push([
                'tanggal'       => $date->copy(),
                'sesi'          => $sesi,
                'bruto'         => $bruto,
                'bruto_std'     => $brutoStd,
                'bruto_prog'    => $brutoProg,
                'komisi_std'    => $komisiStd,
                'komisi_prog'   => $komisiProg,
                'komisi_pijat'  => $komisiPijat,
                'bonus_hadir'   => $bonusHadir,
                'total_komisi'  => $totalKomisi,
                'bersih'        => $bersih,
                'hadir'         => $hadir,
                'cancel_forfeit' => $cancelForfeit,
            ]);

            $date->addDay();
        }

        return $days;
    }

    private function buildLaporanTerapis($start, $end): \Illuminate\Support\Collection
    {
        $terapis = Therapist::where('is_active', true)
            ->withCount([
                'bookings as total_sesi'   => fn($q) => $q->inRange($start, $end),
                'bookings as sesi_selesai' => fn($q) => $q->completed()->inRange($start, $end),
            ])
            ->withSum([
                'bookings as bruto_standard' => fn($q) => $q->completed()->inRange($start, $end)
                    ->where('commission_type', 'standard'),
            ], 'final_price')
            ->withSum([
                'bookings as bruto_program' => fn($q) => $q->completed()->inRange($start, $end)
                    ->where('commission_type', 'program'),
            ], 'final_price')
            ->orderByDesc('sesi_selesai')->get();

        $terapis->each(function ($t) use ($start, $end) {
            $brutoStd  = (float)($t->bruto_standard ?? 0);
            $brutoProg = (float)($t->bruto_program  ?? 0);
            $bruto     = $brutoStd + $brutoProg;

            $t->total_bruto_fmt = $bruto;
            $t->komisi_std      = round($brutoStd  * self::RATE_STANDARD, 2);
            $t->komisi_prog     = round($brutoProg * self::RATE_PROGRAM,  2);
            $t->komisi_pijat    = $t->komisi_std + $t->komisi_prog;

            // Bonus hadir
            $hariHadir = TherapistAttendance::where('therapist_id', $t->id)
                ->whereIn('status', ['present', 'late'])
                ->whereBetween('attendance_date', [$start->toDateString(), $end->toDateString()])
                ->count();

            $t->hari_hadir  = $hariHadir;
            $t->bonus_hadir = $hariHadir * self::BONUS_HADIR;
            $t->total_komisi = $t->komisi_pijat + $t->bonus_hadir;
            $t->pendapatan_spa = $bruto - $t->komisi_pijat;

            // Komisi dari cancel forfeit
            $t->cancel_forfeit = Commission::where('therapist_id', $t->id)
                ->where('commission_source', 'cancel_forfeit')
                ->whereHas('booking', fn($q) => $q->inRange($start, $end))
                ->sum('commission_amount');

            // Total yang diterima terapis (sesi + cancel + bonus hadir)
            $t->total_terima = $t->total_komisi + $t->cancel_forfeit;
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
            $pendapatan = array_map(fn($h) => (int)(($raw[$h]->total ?? 0) * (1 - self::RATE_STANDARD)), range(0, 23));
            $booking    = array_map(fn($h) => (int)($raw[$h]->jml   ?? 0), range(0, 23));
        } elseif ($mode === 'mingguan') {
            $labels  = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            $dowMap  = [2, 3, 4, 5, 6, 7, 1];
            $raw     = Booking::completed()->inRange($start, $end)
                ->selectRaw('DAYOFWEEK(scheduled_at) as p, SUM(final_price) as total, COUNT(*) as jml')
                ->groupBy('p')->get()->keyBy('p');
            $bruto      = array_map(fn($d) => (int)($raw[$d]->total ?? 0), $dowMap);
            $pendapatan = array_map(fn($d) => (int)(($raw[$d]->total ?? 0) * (1 - self::RATE_STANDARD)), $dowMap);
            $booking    = array_map(fn($d) => (int)($raw[$d]->jml   ?? 0), $dowMap);
        } else {
            $days   = $start->copy()->daysInMonth;
            $labels = array_map(fn($d) => (string)$d, range(1, $days));
            $raw    = Booking::completed()->inRange($start, $end)
                ->selectRaw('DAY(scheduled_at) as p, SUM(final_price) as total, COUNT(*) as jml')
                ->groupBy('p')->get()->keyBy('p');
            $bruto      = array_map(fn($d) => (int)($raw[$d]->total ?? 0), range(1, $days));
            $pendapatan = array_map(fn($d) => (int)(($raw[$d]->total ?? 0) * (1 - self::RATE_STANDARD)), range(1, $days));
            $booking    = array_map(fn($d) => (int)($raw[$d]->jml   ?? 0), range(1, $days));
        }
        return [$labels, $bruto, $pendapatan, $booking];
    }

    private function generateExcel($label, $rekapHarian, $laporanTerapis, $transaksi, $absensi): void
    {
        if (ob_get_level()) ob_end_clean();

        (new LaporanExportController(
            $label,
            $rekapHarian,
            $laporanTerapis,
            $transaksi,
            $absensi
        ))->generate();
    }
}
