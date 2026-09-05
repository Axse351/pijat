<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Therapist;
use App\Models\TherapistAttendance;
use App\Models\TherapistFaceData;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TherapistAttendanceController extends Controller
{
    private $confidenceThreshold = 0.75;

    /*
    |--------------------------------------------------------------------------
    | LOKASI KOICHI (untuk validasi geofence absen)
    |--------------------------------------------------------------------------
    | Diambil dari titik Google Maps KOICHI Family Reflexology Cirebon.
    | Ubah $maxDistanceMeters kalau radius area kerja perlu diperlonggar/diperketat
    | (misal GPS HP kurang akurat di dalam ruangan, radius bisa dinaikkan).
    */
    private float $officeLatitude = -6.7098533;
    private float $officeLongitude = 108.5652088;
    private float $maxDistanceMeters = 150;

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $today = Carbon::today();

        $therapists = Therapist::with([
            'attendances' => fn($q) => $q->whereDate('attendance_date', $today),
            'faceData',
            // ⭐ BARU: eager-load jadwal hari ini supaya bisa ditampilkan shift-nya
            'todaySchedule',
        ])->paginate(15);

        return view('admin.attendances.index', compact('therapists', 'today'));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW CHECK-IN CAMERA PAGE
    | Memuat semua embeddings terapis yang sudah verified untuk face matching
    |--------------------------------------------------------------------------
    */
    public function showCheckInCamera()
    {
        $today = Carbon::today();

        $therapists = Therapist::with([
            'faceData'    => fn($q) => $q->where('status', 'verified'),
            'attendances' => fn($q) => $q->whereDate('attendance_date', $today),
        ])->get();

        $faceDescriptors = $this->buildFaceDescriptors($therapists);

        return view('admin.attendances.check-in-camera', [
            'therapists'        => $therapists,
            'faceDescriptors'   => $faceDescriptors,
            'officeLatitude'    => $this->officeLatitude,
            'officeLongitude'   => $this->officeLongitude,
            'maxDistanceMeters' => $this->maxDistanceMeters,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW CHECK-OUT CAMERA PAGE
    |--------------------------------------------------------------------------
    */
    public function showCheckOutCamera()
    {
        $today = Carbon::today();

        $therapists = Therapist::with([
            'faceData'    => fn($q) => $q->where('status', 'verified'),
            'attendances' => fn($q) => $q->whereDate('attendance_date', $today),
        ])->get();

        $faceDescriptors = $this->buildFaceDescriptors($therapists);

        return view('admin.attendances.check-out-camera', [
            'therapists'        => $therapists,
            'faceDescriptors'   => $faceDescriptors,
            'officeLatitude'    => $this->officeLatitude,
            'officeLongitude'   => $this->officeLongitude,
            'maxDistanceMeters' => $this->maxDistanceMeters,
        ]);
    }

    /**
     * Helper: bangun array embeddings wajah untuk dikirim ke JS.
     */
    private function buildFaceDescriptors($therapists)
    {
        return $therapists
            ->filter(fn($t) => $t->faceData && $t->faceData->face_embeddings)
            ->map(function ($t) {
                $embeddings = $t->faceData->face_embeddings;

                if (is_string($embeddings)) {
                    $embeddings = json_decode($embeddings, true) ?? [];
                }

                return [
                    'id'         => $t->id,
                    'name'       => $t->name,
                    'embeddings' => $embeddings,
                ];
            })
            ->values();
    }

    /**
     * Hitung jarak antara dua koordinat (meter) pakai rumus Haversine.
     */
    private function calculateDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meter

        $latRad1 = deg2rad($lat1);
        $latRad2 = deg2rad($lat2);
        $deltaLat = deg2rad($lat2 - $lat1);
        $deltaLng = deg2rad($lng2 - $lng1);

        $a = sin($deltaLat / 2) ** 2
            + cos($latRad1) * cos($latRad2) * sin($deltaLng / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK-IN via AJAX (foto biasa, tanpa kedip — divalidasi lokasi GPS)
    |--------------------------------------------------------------------------
    */
    public function checkInAjax(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'image'        => 'required|image|max:5120',
            'confidence'   => 'nullable|numeric',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
        ]);

        $therapist = Therapist::findOrFail($request->therapist_id);
        $today     = Carbon::today('Asia/Jakarta');
        $now       = Carbon::now('Asia/Jakarta');

        $schedule = \App\Models\TherapistSchedule::where('therapist_id', $therapist->id)
            ->whereDate('schedule_date', $today)
            ->first();

        if (!$schedule || !in_array($schedule->status, \App\Http\Controllers\Admin\TherapistScheduleController::WORKING_STATUSES)) {
            return response()->json([
                'success' => false,
                'message' => $therapist->name . ' tidak dijadwalkan masuk hari ini.',
            ]);
        }

        $existing = TherapistAttendance::where('therapist_id', $therapist->id)
            ->whereDate('attendance_date', $today)
            ->whereNotNull('check_in_at')
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => $therapist->name . ' sudah check-in pada '
                    . Carbon::parse($existing->check_in_at)
                    ->setTimezone('Asia/Jakarta')
                    ->format('H:i'),
            ]);
        }

        $distance = $this->calculateDistanceMeters(
            $this->officeLatitude,
            $this->officeLongitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($distance > $this->maxDistanceMeters) {
            return response()->json([
                'success' => false,
                'message' => 'Absen ditolak: Anda berada ' . round($distance) . ' meter dari lokasi Koichi '
                    . '(maks. ' . $this->maxDistanceMeters . ' meter). Pastikan Anda berada di area kerja.',
            ]);
        }

        try {
            DB::beginTransaction();

            $imagePath = $request->file('image')->store('faces/checkin', 'public');

            $scheduledStart = Carbon::parse($schedule->start_time, 'Asia/Jakarta');
            $status         = $now->gt($scheduledStart) ? 'late' : 'present';

            TherapistAttendance::updateOrCreate(
                ['therapist_id' => $therapist->id, 'attendance_date' => $today],
                [
                    'check_in_at'               => $now,
                    'check_in_image'            => $imagePath,
                    'check_in_confidence'       => $request->confidence ?? 1.0,
                    'check_in_latitude'         => $request->latitude,
                    'check_in_longitude'        => $request->longitude,
                    'check_in_distance_meters'  => round($distance, 1),
                    'status'                    => $status,
                    'check_out_at'              => null,
                ]
            );

            DB::commit();

            return response()->json([
                'success'  => true,
                'time'     => $now->format('H:i'),
                'status'   => $status,
                'distance' => round($distance),
                'message'  => 'Check-in berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK-OUT via AJAX (foto biasa, tanpa kedip — divalidasi lokasi GPS)
    |--------------------------------------------------------------------------
    */
    public function checkOutAjax(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'image'        => 'required|image|max:5120',
            'confidence'   => 'nullable|numeric',
            'latitude'     => 'required|numeric|between:-90,90',
            'longitude'    => 'required|numeric|between:-180,180',
        ]);

        $therapist = Therapist::findOrFail($request->therapist_id);
        $today     = Carbon::today('Asia/Jakarta');
        $now       = Carbon::now('Asia/Jakarta');

        $attendance = TherapistAttendance::where('therapist_id', $therapist->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in_at) {
            return response()->json([
                'success' => false,
                'message' => $therapist->name . ' belum check-in hari ini.',
            ]);
        }

        if ($attendance->check_out_at) {
            return response()->json([
                'success' => false,
                'message' => $therapist->name . ' sudah check-out pada '
                    . Carbon::parse($attendance->check_out_at)
                    ->setTimezone('Asia/Jakarta')
                    ->format('H:i'),
            ]);
        }

        $distance = $this->calculateDistanceMeters(
            $this->officeLatitude,
            $this->officeLongitude,
            (float) $request->latitude,
            (float) $request->longitude
        );

        if ($distance > $this->maxDistanceMeters) {
            return response()->json([
                'success' => false,
                'message' => 'Absen ditolak: Anda berada ' . round($distance) . ' meter dari lokasi Koichi '
                    . '(maks. ' . $this->maxDistanceMeters . ' meter). Pastikan Anda berada di area kerja.',
            ]);
        }

        try {
            DB::beginTransaction();

            $imagePath = $request->file('image')->store('faces/checkout', 'public');

            $attendance->update([
                'check_out_at'              => $now,
                'check_out_image'           => $imagePath,
                'check_out_confidence'      => $request->confidence ?? 1.0,
                'check_out_latitude'        => $request->latitude,
                'check_out_longitude'       => $request->longitude,
                'check_out_distance_meters' => round($distance, 1),
            ]);

            DB::commit();

            $checkIn  = Carbon::parse($attendance->check_in_at)->setTimezone('Asia/Jakarta');
            $hours    = $checkIn->diffInHours($now);
            $minutes  = $checkIn->diff($now)->i;
            $duration = $hours . ' jam ' . $minutes . ' menit';

            return response()->json([
                'success'  => true,
                'time'     => $now->format('H:i'),
                'duration' => $duration,
                'distance' => round($distance),
                'message'  => 'Check-out berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */
    public function history(Therapist $therapist)
    {
        $attendances = TherapistAttendance::where('therapist_id', $therapist->id)
            ->latest('attendance_date')
            ->paginate(20);

        $stats = [
            'total_hadir'     => TherapistAttendance::where('therapist_id', $therapist->id)->where('status', 'present')->count(),
            'total_terlambat' => TherapistAttendance::where('therapist_id', $therapist->id)->where('status', 'late')->count(),
            'total_absent'    => TherapistAttendance::where('therapist_id', $therapist->id)->where('status', 'absent')->count(),
        ];

        return view('admin.attendances.history', compact('therapist', 'attendances', 'stats'));
    }

    /*
    |--------------------------------------------------------------------------
    | DEPRECATED - kept for backward compat
    |--------------------------------------------------------------------------
    */
    public function checkIn(Request $request, Therapist $therapist)
    {
        return redirect()->route('admin.attendance.check-in-camera');
    }

    public function checkOut(Request $request, Therapist $therapist)
    {
        return redirect()->route('admin.attendance.check-out-camera');
    }
}
