<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\TherapistFaceController;
use App\Http\Controllers\Admin\TherapistAttendanceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// ============================================================================
// ROOT & DASHBOARD
// ============================================================================

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('auth')->get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->name('dashboard');


// ============================================================================
// ADMIN ROUTES
// ============================================================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ---- Dashboard ----
        Route::get('dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->name('dashboard');

        // ---- Booking ----
        Route::get('bookings/calendar', [BookingController::class, 'calendar'])
            ->name('bookings.calendar');
        Route::get('bookings/calendar-data', [BookingController::class, 'calendarData'])
            ->name('bookings.calendar-data');

        // ---- Resource Routes ----
        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class);
        Route::resource('therapists', \App\Http\Controllers\Admin\TherapistController::class);
        Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
        Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);
        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);
        Route::resource('memberships', \App\Http\Controllers\Admin\MembershipController::class);
        Route::resource('promos', \App\Http\Controllers\Admin\PromoController::class);
        Route::resource('barang', \App\Http\Controllers\BarangController::class);

        // ---- Therapist Face Registration ----
        Route::get('/therapists/{therapist}/face/register', [TherapistFaceController::class, 'create'])
            ->name('therapist-face.register');
        Route::post('/therapists/{therapist}/face', [TherapistFaceController::class, 'store'])
            ->name('therapist-face.store');
        Route::post('/therapists/{therapist}/face/verify', [TherapistFaceController::class, 'verify'])
            ->name('therapist-face.verify');
        Route::delete('/therapists/{therapist}/face', [TherapistFaceController::class, 'destroy'])
            ->name('therapist-face.destroy');

        // ---- Therapist Attendance ----
        Route::get('/attendances', [TherapistAttendanceController::class, 'index'])
            ->name('attendances.index');
        Route::post('/therapists/{therapist}/check-in', [TherapistAttendanceController::class, 'checkIn'])
            ->name('attendance.check-in');
        Route::post('/therapists/{therapist}/check-out', [TherapistAttendanceController::class, 'checkOut'])
            ->name('attendance.check-out');
        Route::get('/therapists/{therapist}/attendance/history', [TherapistAttendanceController::class, 'history'])
            ->name('attendance.history');
Route::get('/therapists/{therapist}/check-in',
    [TherapistAttendanceController::class, 'showCheckInCamera'])
    ->name('attendance.check-in-camera');

Route::get('/therapists/{therapist}/check-out',
    [TherapistAttendanceController::class, 'showCheckOutCamera'])
    ->name('attendance.check-out-camera');
        // ---- Customer Membership (Nested) ----
        Route::prefix('customers/{customer}/memberships')
            ->name('customers.membership.')
            ->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'index'])
                    ->name('index');
                Route::get('/create', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'create'])
                    ->name('create');
                Route::post('/', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'store'])
                    ->name('store');
                Route::get('/{customerMembership}/edit', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'edit'])
                    ->name('edit');
                Route::put('/{customerMembership}', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'update'])
                    ->name('update');
                Route::delete('/{customerMembership}', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'destroy'])
                    ->name('destroy');
            });

    });


// ============================================================================
// USER ROUTES
// ============================================================================

Route::middleware(['auth', 'role:user'])
    ->group(function () {
        Route::get('/user/dashboard', function () {
            return view('user.dashboard');
        })->name('user.dashboard');
    });


// ============================================================================
// PROFILE ROUTES
// ============================================================================

Route::middleware('auth')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])
            ->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])
            ->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])
            ->name('profile.destroy');
    });


// ============================================================================
// AUTH ROUTES
// ============================================================================

require __DIR__ . '/auth.php';
