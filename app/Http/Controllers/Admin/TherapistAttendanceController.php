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
    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */

    // Threshold minimal agar dianggap wajah cocok
    private $confidenceThreshold = 0.75;


    /*
    |--------------------------------------------------------------------------
    | INDEX - LIST ALL THERAPISTS WITH ATTENDANCE STATUS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $today = Carbon::today();

        // Ambil semua therapist dengan relasi attendance hari ini dan face data
        $therapists = Therapist::with([
            'attendances' => function ($query) use ($today) {
                $query->whereDate('attendance_date', $today);
            },
            'faceData'
        ])->paginate(15);

        return view('admin.attendances.index', compact('therapists', 'today'));
    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER FACE (Admin) - DEPRECATED
    |--------------------------------------------------------------------------
    */

    // Method ini sekarang ada di TherapistFaceController
    // Kept here untuk backward compatibility jika diperlukan

    public function registerFace(Request $request, Therapist $therapist)
    {
        $request->validate([
            'image' => 'required|image',
            'embeddings' => 'required|array'
        ]);

        DB::beginTransaction();

        try {
            // Simpan foto
            $path = $request->file('image')->store('faces/reference', 'public');

            TherapistFaceData::updateOrCreate(
                ['therapist_id' => $therapist->id],
                [
                    'face_embeddings' => $request->embeddings,
                    'reference_image' => $path,
                    'samples_count' => count($request->embeddings),
                    'status' => 'verified'
                ]
            );

            DB::commit();

            return back()->with('success', 'Wajah berhasil diregistrasi');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK IN
    |--------------------------------------------------------------------------
    */

    public function checkIn(Request $request, Therapist $therapist)
    {
        $request->validate([
            'image' => 'required|image',
            'confidence' => 'required|numeric'
        ]);

        // Cek apakah therapist memiliki data wajah yang verified
        $faceData = $therapist->faceData;

        if (!$faceData || !$faceData->isVerified()) {
            return back()->with('error', 'Wajah belum diverifikasi. Silakan daftar wajah terlebih dahulu.');
        }

        // Cek confidence level
        if ($request->confidence < $this->confidenceThreshold) {
            return back()->with('error', 'Wajah tidak cocok (confidence: ' . round($request->confidence, 2) . '%). Minimum: ' . $this->confidenceThreshold);
        }

        $today = Carbon::today();

        // Cek apakah sudah check-in hari ini
        $existingAttendance = TherapistAttendance::where('therapist_id', $therapist->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($existingAttendance && $existingAttendance->check_in_at) {
            return back()->with('error', 'Anda sudah check-in pada ' . $existingAttendance->check_in_at->format('H:i'));
        }

        try {
            DB::beginTransaction();

            // Simpan image check-in
            $imagePath = $request->file('image')->store('faces/checkin', 'public');

            // Tentukan status: late jika setelah jam 09:00
            $status = now()->format('H:i') > '09:00' ? 'late' : 'present';

            // Create atau update attendance untuk hari ini
            $attendance = TherapistAttendance::updateOrCreate(
                [
                    'therapist_id' => $therapist->id,
                    'attendance_date' => $today
                ],
                [
                    'check_in_at' => now(),
                    'check_in_image' => $imagePath,
                    'check_in_confidence' => $request->confidence,
                    'status' => $status,
                    'check_out_at' => null,
                ]
            );

            DB::commit();

            $message = $status === 'late'
                ? 'Check-in berhasil (Status: TERLAMBAT)'
                : 'Check-in berhasil (Status: HADIR)';

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal check-in: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHECK OUT
    |--------------------------------------------------------------------------
    */

    public function checkOut(Request $request, Therapist $therapist)
    {
        $request->validate([
            'image' => 'required|image',
            'confidence' => 'required|numeric'
        ]);

        // Cek apakah therapist memiliki data wajah yang verified
        $faceData = $therapist->faceData;

        if (!$faceData || !$faceData->isVerified()) {
            return back()->with('error', 'Wajah belum diverifikasi.');
        }

        // Cek confidence level
        if ($request->confidence < $this->confidenceThreshold) {
            return back()->with('error', 'Wajah tidak cocok. Confidence terlalu rendah.');
        }

        $today = Carbon::today();

        // Ambil attendance hari ini
        $attendance = TherapistAttendance::where('therapist_id', $therapist->id)
            ->whereDate('attendance_date', $today)
            ->first();

        // Validasi kondisi check-out
        if (!$attendance) {
            return back()->with('error', 'Data kehadiran hari ini tidak ditemukan. Silakan check-in terlebih dahulu.');
        }

        if (!$attendance->check_in_at) {
            return back()->with('error', 'Anda belum check-in hari ini.');
        }

        if ($attendance->check_out_at) {
            return back()->with('error', 'Anda sudah check-out pada ' . $attendance->check_out_at->format('H:i'));
        }

        try {
            DB::beginTransaction();

            // Simpan image check-out
            $imagePath = $request->file('image')->store('faces/checkout', 'public');

            // Update attendance dengan check-out
            $attendance->update([
                'check_out_at' => now(),
                'check_out_image' => $imagePath,
                'check_out_confidence' => $request->confidence,
            ]);

            DB::commit();

            return back()->with('success', 'Check-out berhasil pada ' . now()->format('H:i'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal check-out: ' . $e->getMessage());
        }
    }


    /*
    |--------------------------------------------------------------------------
    | HISTORY - SHOW ATTENDANCE HISTORY
    |--------------------------------------------------------------------------
    */

    public function history(Therapist $therapist)
    {
        // Ambil history kehadiran dengan pagination
        $attendances = TherapistAttendance::where('therapist_id', $therapist->id)
            ->latest('attendance_date')
            ->paginate(20);

        // Hitung statistik
        $stats = [
            'total_hadir' => TherapistAttendance::where('therapist_id', $therapist->id)
                ->where('status', 'present')
                ->count(),
            'total_terlambat' => TherapistAttendance::where('therapist_id', $therapist->id)
                ->where('status', 'late')
                ->count(),
            'total_absent' => TherapistAttendance::where('therapist_id', $therapist->id)
                ->where('status', 'absent')
                ->count(),
        ];

        return view('admin.attendances.history', compact('therapist', 'attendances', 'stats'));
    }


    /*
    |--------------------------------------------------------------------------
    | HELPER METHODS (Optional)
    |--------------------------------------------------------------------------
    */

    /**
     * Ambil attendance terakhir therapist
     */
    private function getLatestAttendance(Therapist $therapist)
    {
        return TherapistAttendance::where('therapist_id', $therapist->id)
            ->latest('created_at')
            ->first();
    }

    /**
     * Hitung durasi kerja
     */
    public function calculateWorkDuration(TherapistAttendance $attendance)
    {
        if (!$attendance->check_in_at || !$attendance->check_out_at) {
            return null;
        }

        $duration = $attendance->check_out_at->diffInHours($attendance->check_in_at);
        return $duration;
    }
}
