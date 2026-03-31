<?php

namespace App\Http\Controllers\Terapis;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Therapist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Display therapist dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        // Data hari ini
        $todayBookings = Booking::where('therapist_id', $therapist->id)
            ->whereDate('scheduled_at', Carbon::today())
            ->get();

        $todayBookingsCount = $todayBookings->count();
        $todayRevenue = $todayBookings->sum('final_price');

        // Data bulan ini
        $monthBookings = Booking::where('therapist_id', $therapist->id)
            ->whereMonth('scheduled_at', Carbon::now()->month)
            ->whereYear('scheduled_at', Carbon::now()->year)
            ->get();

        $monthRevenue = $monthBookings->sum('final_price');
        $monthCommission = $monthRevenue * ($therapist->commission_percent / 100);

        // Booking bulan ini dengan status
        $statusSummary = [
            'scheduled' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', Carbon::today())
                ->where('status', 'scheduled')
                ->count(),
            'ongoing' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', Carbon::today())
                ->where('status', 'ongoing')
                ->count(),
            'completed' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', Carbon::today())
                ->where('status', 'completed')
                ->count(),
            'cancelled' => Booking::where('therapist_id', $therapist->id)
                ->whereDate('scheduled_at', Carbon::today())
                ->where('status', 'cancelled')
                ->count(),
        ];

        // Jadwal hari ini
        $todaySchedule = $todayBookings->sortBy('scheduled_at');

        // Top services bulan ini
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

        // Data grafik harian
        $chartLabelsHarian = array_map(fn($h) => sprintf('%02d:00', $h), range(0, 23));
        $chartHarian = array_fill(0, 24, 0);

        foreach ($todayBookings as $booking) {
            $hour = Carbon::parse($booking->scheduled_at)->hour;
            $chartHarian[$hour] += $booking->final_price;
        }

        // Data grafik mingguan
        $chartLabelsMingguan = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        $chartMingguan = array_fill(0, 7, 0);

        $weekBookings = Booking::where('therapist_id', $therapist->id)
            ->whereBetween('scheduled_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ])
            ->get();

        foreach ($weekBookings as $booking) {
            $dayOfWeek = Carbon::parse($booking->scheduled_at)->dayOfWeek;
            $chartMingguan[$dayOfWeek] += $booking->final_price;
        }

        // Data grafik bulanan
        $chartLabelsBulanan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $chartBulanan = array_fill(0, 12, 0);

        $yearBookings = Booking::where('therapist_id', $therapist->id)
            ->whereYear('scheduled_at', Carbon::now()->year)
            ->get();

        foreach ($yearBookings as $booking) {
            $month = Carbon::parse($booking->scheduled_at)->month - 1;
            $chartBulanan[$month] += $booking->final_price;
        }

        return view('terapis.dashboard', [
            'therapist'           => $therapist,
            'todayBookingsCount'  => $todayBookingsCount,
            'todayRevenue'        => $todayRevenue,
            'monthRevenue'        => $monthRevenue,
            'monthCommission'     => $monthCommission,
            'statusSummary'       => $statusSummary,
            'todaySchedule'       => $todaySchedule,
            'topServices'         => $topServices,
            'chartLabelsHarian'   => $chartLabelsHarian,
            'chartHarian'         => $chartHarian,
            'chartLabelsMingguan' => $chartLabelsMingguan,
            'chartMingguan'       => $chartMingguan,
            'chartLabelsBulanan'  => $chartLabelsBulanan,
            'chartBulanan'        => $chartBulanan,
        ]);
    }

    /**
     * Display therapist bookings
     */
    public function bookings(Request $request)
    {
        $user = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        $query = Booking::where('therapist_id', $therapist->id)
            ->with('customer', 'service');

        // Filter berdasarkan status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('scheduled_at', $request->date);
        }

        $bookings = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        return view('terapis.bookings.index', compact('bookings'));
    }

    /**
     * Display therapist profile
     */
    public function profile()
    {
        $user = Auth::user();
        $therapist = $user->therapist;

        if (!$therapist) {
            return redirect()->route('login');
        }

        return view('terapis.profile.show', compact('therapist'));
    }

    /**
     * Update therapist profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $therapist = $user->therapist;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'specialty' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // ✅ SEKARANG BISA PAKAI update()
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // ✅ SEKARANG BISA PAKAI update()
        $therapist->update([
            'name' => $validated['name'],
            'specialty' => $validated['specialty'] ?? null,
            'photo' => $photoPath,
        ]);

        return redirect()->route('terapis.profile')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Change therapist password
     */
    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();
        $user->update([
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }
}
