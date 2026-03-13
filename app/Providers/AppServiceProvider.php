<?php

namespace App\Providers;

use App\Models\Therapist;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Variabel global untuk semua view yang menggunakan layout admin
        View::composer('layouts.admin', function ($view) {
            $view->with([
                'totalTherapists' => Therapist::where('is_active', true)->count(),
                'totalCustomers'  => Customer::count(),
                'therapists'      => Therapist::where('is_active', true)->get(),
                'recentBookings'  => Booking::with(['customer', 'service'])
                    ->latest('scheduled_at')
                    ->limit(8)
                    ->get(),
                'unpaidBookings'  => Booking::with(['customer', 'service'])
                    ->where('status', 'scheduled')
                    ->whereDoesntHave('payment')
                    ->latest('scheduled_at')
                    ->limit(5)
                    ->get(),
                'topServices'     => Service::withCount('bookings')
                    ->orderByDesc('bookings_count')
                    ->limit(5)
                    ->get(),
            ]);
        });
    }
}
