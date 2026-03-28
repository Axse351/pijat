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
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $today = Carbon::today();

        $therapists = Therapist::with([
            'attendances' => fn($q) => $q->whereDate('attendance_date', $today),
            'faceData'
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

        // Ambil semua terapis dengan wajah verified
        $therapists = Therapist::with([
            'faceData'    => fn($q) => $q->where('status', 'verified'),
            'attendances' => fn($q) => $q->whereDate('attendance_date', $today),
        ])->get();

        // Build array embeddings untuk dikirim ke JS
        // Format: [{ id, name, embeddings: [float...] }, ...]
        $faceDescriptors = $therapists
            ->filter(fn($t) => $t->faceData && $t->faceData->face_embeddings)
            ->map(function ($t) {
                $embeddings = $t->faceData->face_embeddings;

                // face_embeddings bisa disimpan sebagai JSON string atau array
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

        return view('admin.attendances.check-in-camera', compact('therapists', 'faceDescriptors'));
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

        $faceDescriptors = $therapists
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

        return view('admin.attendances.check-out-camera', compact('therapists', 'faceDescriptors'));
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK-IN via AJAX (dipanggil dari face recognition JS)
    |--------------------------------------------------------------------------
    */
    public function checkInAjax(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'image'        => 'required|image|max:5120',
            'confidence'   => 'nullable|numeric',
        ]);

        $therapist = Therapist::findOrFail($request->therapist_id);
        $today     = Carbon::today('Asia/Jakarta');
        $now       = Carbon::now('Asia/Jakarta');

        // ✅ Cek jadwal hari ini
        $schedule = \App\Models\TherapistSchedule::where('therapist_id', $therapist->id)
            ->whereDate('schedule_date', $today)
            ->first();

        // ✅ Validasi apakah dijadwalkan masuk
        if (!$schedule || $schedule->status !== 'working') {
            return response()->json([
                'success' => false,
                'message' => $therapist->name . ' tidak dijadwalkan masuk hari ini.',
            ]);
        }

        // Cek sudah check-in hari ini
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

        try {
            DB::beginTransaction();

            $imagePath = $request->file('image')->store('faces/checkin', 'public');

            // ✅ Bandingkan dengan start_time dari jadwal, bukan hardcode 09:00
            $scheduledStart = Carbon::parse($schedule->start_time, 'Asia/Jakarta');
            $status         = $now->gt($scheduledStart) ? 'late' : 'present';

            TherapistAttendance::updateOrCreate(
                ['therapist_id' => $therapist->id, 'attendance_date' => $today],
                [
                    'check_in_at'         => $now,
                    'check_in_image'      => $imagePath,
                    'check_in_confidence' => $request->confidence ?? 1.0,
                    'status'              => $status,
                    'check_out_at'        => null,
                ]
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'time'    => $now->format('H:i'),
                'status'  => $status,
                'message' => 'Check-in berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    public function checkOutAjax(Request $request)
    {
        $request->validate([
            'therapist_id' => 'required|exists:therapists,id',
            'image'        => 'required|image|max:5120',
            'confidence'   => 'nullable|numeric',
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

        try {
            DB::beginTransaction();

            $imagePath = $request->file('image')->store('faces/checkout', 'public');

            $attendance->update([
                'check_out_at'         => $now,
                'check_out_image'      => $imagePath,
                'check_out_confidence' => $request->confidence ?? 1.0,
            ]);

            DB::commit();

            // ✅ Hitung durasi pakai timezone WIB
            $checkIn  = Carbon::parse($attendance->check_in_at)->setTimezone('Asia/Jakarta');
            $hours    = $checkIn->diffInHours($now);
            $minutes  = $checkIn->diff($now)->i;
            $duration = $hours . ' jam ' . $minutes . ' menit';

            return response()->json([
                'success'  => true,
                'time'     => $now->format('H:i'),
                'duration' => $duration,
                'message'  => 'Check-out berhasil',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK-OUT via AJAX
    |--------------------------------------------------------------------------
    */


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
