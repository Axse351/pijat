<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Therapist;
use Carbon\Carbon;

class WelcomeController extends Controller
{
    public function index()
    {
        // ── Layanan & Terapis aktif ─────────────────────────────────────
        $services   = Service::all();
        $therapists = Therapist::where('is_active', 1)->get();

        // ── Jadwal terapis untuk tampilan publik ────────────────────────
        //
        // Kita ambil jadwal bulan ini + bulan depan sekaligus agar
        // pelanggan bisa navigasi ke depan tanpa AJAX.
        //
        // Format $therapistSchedules yang dihasilkan:
        // [
        //   "1" => [
        //     "name"       => "Sari Dewi",
        //     "spec"       => "Refleksiologi",
        //     "start_time" => "09:00",
        //     "end_time"   => "17:00",
        //     "schedules"  => [
        //       "2026-04-01" => "working",
        //       "2026-04-02" => "off",
        //       ...
        //     ],
        //   ],
        //   ...
        // ]
        //
        $therapistSchedules = $this->buildTherapistSchedules($therapists);

        return view('welcome', compact('services', 'therapists', 'therapistSchedules'));
    }

    // ────────────────────────────────────────────────────────────────────
    //  Helper: bangun data jadwal terapis untuk 3 bulan (lalu–sekarang–depan)
    //  agar navigasi kalender di frontend tetap responsif tanpa request baru.
    // ────────────────────────────────────────────────────────────────────
    private function buildTherapistSchedules($therapists): array
    {
        $result = [];

        // Rentang tanggal: bulan lalu s/d 2 bulan ke depan
        $from = Carbon::now()->startOfMonth()->subMonth();
        $to   = Carbon::now()->endOfMonth()->addMonths(2);

        // Load semua jadwal dalam rentang tersebut, eager-load therapist
        // SESUAIKAN nama model & kolom dengan struktur tabel kamu.
        //
        // Asumsi model: App\Models\TherapistSchedule
        // Asumsi kolom: therapist_id, date (DATE), status (enum/string),
        //               start_time (TIME), end_time (TIME)
        //
        // Jika nama model / kolom berbeda, ubah bagian ini saja.
        //
        $allSchedules = \App\Models\TherapistSchedule::query()
            ->whereBetween('schedule_date', [$from->toDateString(), $to->toDateString()])
            ->get()
            ->groupBy('therapist_id');

        foreach ($therapists as $t) {
            // Ambil baris jadwal pertama yang punya start_time/end_time
            // sebagai jam kerja default ditampilkan di summary.
            $firstWorking = $allSchedules->get($t->id)?->firstWhere('status', 'working');

            $schedMap = [];
            foreach ($allSchedules->get($t->id, collect()) as $row) {
                // Normalisasi tanggal ke string 'Y-m-d'
                $dateKey = Carbon::parse($row->schedule_date)->toDateString();
                $schedMap[$dateKey] = $row->status; // 'working' | 'off' | 'sick' | 'vacation' | 'cuti_bersama'
            }

            $result[(string) $t->id] = [
                'name'       => $t->name,
                'spec'       => $t->specialization ?? 'Terapis Profesional',
                'start_time' => $firstWorking?->start_time
                    ? Carbon::parse($firstWorking->start_time)->format('H:i')
                    : '09:00',
                'end_time'   => $firstWorking?->end_time
                    ? Carbon::parse($firstWorking->end_time)->format('H:i')
                    : '17:00',
                'schedules'  => $schedMap,
            ];
        }

        return $result;
    }
}
