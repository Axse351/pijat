<?php

namespace App\Http\Controllers\Terapis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Therapist;
use App\Models\TherapistSchedule;
use App\Models\TherapistLeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        $today = Carbon::today();

        // ── Antisipasi tabel belum ada ───────────────────────────────────
        $todaySchedule = null;
        $activeLeave   = null;
        $pendingLeaves = collect();

        if (Schema::hasTable('therapist_schedules')) {
            $todaySchedule = TherapistSchedule::where('therapist_id', $therapist->id)
                ->whereDate('schedule_date', $today)
                ->first();
        }

        if (Schema::hasTable('therapist_leave_requests')) {
            $activeLeave = TherapistLeaveRequest::where('therapist_id', $therapist->id)
                ->where('status', 'approved')
                ->where('start_date', '<=', $today)
                ->where('end_date', '>=', $today)
                ->first();

            $pendingLeaves = TherapistLeaveRequest::where('therapist_id', $therapist->id)
                ->where('status', 'pending')
                ->get();
        }

        // ── Booking hari ini ─────────────────────────────────────────────
        $todayBookings      = Booking::where('therapist_id', $therapist->id)
            ->whereDate('scheduled_at', $today)
            ->get();

        $todayBookingsCount = $todayBookings->count();
        $todayRevenue       = $todayBookings->sum('final_price');

        // ── Booking bulan ini ────────────────────────────────────────────
        $monthBookings = Booking::where('therapist_id', $therapist->id)
            ->whereMonth('scheduled_at', Carbon::now()->month)
            ->whereYear('scheduled_at', Carbon::now()->year)
            ->get();

        $monthRevenue    = $monthBookings->sum('final_price');
        $monthCommission = $monthRevenue * ($therapist->commission_percent / 100);

        // ── Status summary ───────────────────────────────────────────────
        $statusSummary = [
            'scheduled' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', $today)
                ->where('status', 'scheduled')->count(),
            'ongoing'   => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', $today)
                ->where('status', 'ongoing')->count(),
            'completed' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', $today)
                ->where('status', 'completed')->count(),
            'cancelled' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', $today)
                ->where('status', 'cancelled')->count(),
        ];

        // ── Booking mendatang ────────────────────────────────────────────
        $upcomingBookings = Booking::where('therapist_id', $therapist->id)
            ->where('scheduled_at', '>=', now())
            ->with('customer', 'service')
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        // ── Top services bulan ini ───────────────────────────────────────
        $topServices = Booking::where('therapist_id', $therapist->id)
            ->whereMonth('scheduled_at', Carbon::now()->month)
            ->whereYear('scheduled_at', Carbon::now()->year)
            ->with('service')
            ->get()
            ->groupBy('service_id')
            ->map(fn($group) => [
                'service_name' => $group->first()->service->name ?? '-',
                'count'        => $group->count(),
                'total'        => $group->sum('final_price'),
            ])
            ->sortByDesc('count')
            ->take(5);

        // ── Grafik harian ────────────────────────────────────────────────
        $chartLabelsHarian = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));
        $chartHarian       = array_fill(0, 24, 0);

        foreach ($todayBookings as $booking) {
            $hour = Carbon::parse($booking->scheduled_at)->hour;
            $chartHarian[$hour] += $booking->final_price;
        }

        // ── Grafik mingguan ──────────────────────────────────────────────
        $chartLabelsMingguan = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $chartMingguan       = array_fill(0, 7, 0);

        $weekBookings = Booking::where('therapist_id', $therapist->id)
            ->whereBetween('scheduled_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])->get();

        foreach ($weekBookings as $booking) {
            $dayOfWeek = Carbon::parse($booking->scheduled_at)->dayOfWeek;
            $chartMingguan[$dayOfWeek] += $booking->final_price;
        }

        // ── Grafik bulanan ───────────────────────────────────────────────
        $chartLabelsBulanan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBulanan       = array_fill(0, 12, 0);

        $yearBookings = Booking::where('therapist_id', $therapist->id)
            ->whereYear('scheduled_at', Carbon::now()->year)
            ->get();

        foreach ($yearBookings as $booking) {
            $month = Carbon::parse($booking->scheduled_at)->month - 1;
            $chartBulanan[$month] += $booking->final_price;
        }

        return view('terapis.dashboard', [
            'therapist'           => $therapist,
            'todaySchedule'       => $todaySchedule,
            'activeLeave'         => $activeLeave,
            'pendingLeaves'       => $pendingLeaves,
            'todayBookingsCount'  => $todayBookingsCount,
            'todayRevenue'        => $todayRevenue,
            'monthRevenue'        => $monthRevenue,
            'monthCommission'     => $monthCommission,
            'statusSummary'       => $statusSummary,
            'upcomingBookings'    => $upcomingBookings,
            'topServices'         => $topServices,
            'chartLabelsHarian'   => $chartLabelsHarian,
            'chartHarian'         => $chartHarian,
            'chartLabelsMingguan' => $chartLabelsMingguan,
            'chartMingguan'       => $chartMingguan,
            'chartLabelsBulanan'  => $chartLabelsBulanan,
            'chartBulanan'        => $chartBulanan,
        ]);
    }

    public function bookings(Request $request)
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        $query = Booking::where('therapist_id', $therapist->id)
            ->with('customer', 'service');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('scheduled_at', $request->date);
        }

        $bookings = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        return view('terapis.bookings.index', compact('bookings'));
    }

    public function schedules(Request $request)
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        $schedules = collect();

        if (Schema::hasTable('therapist_schedules')) {
            $schedules = TherapistSchedule::where('therapist_id', $therapist->id)
                ->orderBy('schedule_date')
                ->get();
        }

        return view('terapis.schedules.index', compact('therapist', 'schedules'));
    }

    /**
     * Menampilkan daftar semua leave requests
     */
    public function leaveRequests(Request $request)
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        $query = TherapistLeaveRequest::where('therapist_id', $therapist->id);

        // Filter by status jika ada
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->get();

        return view('terapis.leaves.index', compact('therapist', 'leaveRequests'));
    }

    /**
     * Form untuk membuat leave request baru
     */
    public function createLeaveRequest()
    {
        return view('terapis.leaves.create');
    }

    /**
     * Menyimpan leave request baru
     */
    public function storeLeaveRequest(Request $request)
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        // Validasi input
        $validated = $request->validate([
            'type'       => 'required|string|in:sakit,pribadi,cuti,izin_khusus',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string|min:10|max:1000',
        ], [
            'type.required'         => 'Jenis izin harus dipilih.',
            'start_date.required'   => 'Tanggal mulai harus diisi.',
            'start_date.after_or_equal' => 'Tanggal mulai tidak boleh di masa lalu.',
            'end_date.required'     => 'Tanggal selesai harus diisi.',
            'end_date.after_or_equal' => 'Tanggal selesai harus sama atau setelah tanggal mulai.',
            'reason.required'       => 'Alasan harus diisi.',
            'reason.min'            => 'Alasan minimal 10 karakter.',
            'reason.max'            => 'Alasan maksimal 1000 karakter.',
        ]);

        // Cek apakah ada izin yang tumpang tindih
        $overlapping = TherapistLeaveRequest::where('therapist_id', $therapist->id)
            ->where('status', '!=', 'rejected')
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhereBetween('end_date', [$validated['start_date'], $validated['end_date']])
                    ->orWhere(function ($q2) use ($validated) {
                        $q2->where('start_date', '<=', $validated['start_date'])
                            ->where('end_date', '>=', $validated['end_date']);
                    });
            })->exists();

        if ($overlapping) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['overlap' => 'Anda sudah memiliki pengajuan izin pada tanggal yang sama.']);
        }

        // Buat leave request baru
        TherapistLeaveRequest::create([
            'therapist_id' => $therapist->id,
            'type'         => $validated['type'],
            'start_date'   => $validated['start_date'],
            'end_date'     => $validated['end_date'],
            'reason'       => $validated['reason'],
            'status'       => 'pending',
        ]);

        return redirect()->route('terapis.leaves.index')
            ->with('success', 'Pengajuan izin berhasil dibuat. Admin akan segera memprosesnya.');
    }

    /**
     * Menampilkan detail leave request
     */
    public function showLeaveRequest($leaveRequest)
    {
        $leaveRequest = TherapistLeaveRequest::findOrFail($leaveRequest);

        // Pastikan user hanya bisa melihat leave request miliknya sendiri
        if ($leaveRequest->therapist_id !== Auth::user()->therapist->id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan izin ini.');
        }

        return view('terapis.leaves.show', compact('leaveRequest'));
    }

    /**
     * Membatalkan leave request
     */
    public function cancelLeaveRequest($leaveRequest)
    {
        $leaveRequest = TherapistLeaveRequest::findOrFail($leaveRequest);

        // Pastikan user hanya bisa membatalkan leave request miliknya sendiri
        if ($leaveRequest->therapist_id !== Auth::user()->therapist->id) {
            abort(403, 'Anda tidak memiliki akses ke pengajuan izin ini.');
        }

        // Hanya izin dengan status pending yang bisa dibatalkan
        if ($leaveRequest->status !== 'pending') {
            return redirect()->back()
                ->withErrors(['error' => 'Hanya pengajuan izin dengan status pending yang bisa dibatalkan.']);
        }

        $leaveRequest->delete();

        return redirect()->route('terapis.leaves.index')
            ->with('success', 'Pengajuan izin berhasil dibatalkan.');
    }

    public function profile()
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        return view('terapis.profile.show', compact('therapist', 'user'));
    }

    public function updateProfile(Request $request)
    {
        $user      = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'specialty' => 'nullable|string|max:255',
            'photo'     => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        $photoPath = $therapist->photo;

        if ($request->hasFile('photo')) {
            if ($therapist->photo && Storage::disk('public')->exists($therapist->photo)) {
                Storage::disk('public')->delete($therapist->photo);
            }
            $photoPath = $request->file('photo')->store('therapists/photos', 'public');
        }

        $therapist->name      = $validated['name'];
        $therapist->specialty = $validated['specialty'] ?? null;
        $therapist->photo     = $photoPath;
        $therapist->save();

        return redirect()->route('terapis.profile.show')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user           = Auth::user();
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diubah.');
    }
}
