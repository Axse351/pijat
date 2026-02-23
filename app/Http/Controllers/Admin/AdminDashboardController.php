<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Therapist;
use App\Models\Service;
use App\Models\Payment;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stats
        $todayBookings  = Booking::whereDate('scheduled_at', today())->count();
        $monthRevenue   = Payment::whereMonth('paid_at', now()->month)
                                  ->whereYear('paid_at', now()->year)
                                  ->sum('amount');
        $totalTherapists = Therapist::count();
        $totalCustomers  = Customer::count();

        // Recent bookings (5 terbaru)
        $recentBookings = Booking::with(['customer', 'therapist', 'service'])
                                  ->latest()
                                  ->take(5)
                                  ->get();

        // Therapist list
        $therapists = Therapist::take(5)->get();

        // Booking selesai tapi belum dibayar
        $unpaidBookings = Booking::with(['customer', 'service'])
                                  ->where('status', 'completed')
                                  ->whereDoesntHave('payment')
                                  ->take(5)
                                  ->get();

        // Layanan terpopuler
        $topServices = Service::withCount('bookings')
                               ->orderByDesc('bookings_count')
                               ->take(4)
                               ->get();

        return view('admin.dashboard', compact(
            'todayBookings',
            'monthRevenue',
            'totalTherapists',
            'totalCustomers',
            'recentBookings',
            'therapists',
            'unpaidBookings',
            'topServices'
        ));
    }
}
