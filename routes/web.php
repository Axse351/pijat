<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\TherapistFaceController;
use App\Http\Controllers\Admin\TherapistAttendanceController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// ROOT — Welcome/Landing Page (tanpa auth)
// ============================================================================

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// ============================================================================
// PUBLIC BOOKING (tanpa auth)
// ============================================================================

Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');

// ============================================================================
// DASHBOARD (redirect setelah login)
// ============================================================================

Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin'  => redirect()->route('admin.dashboard'),
        'kasir'  => redirect()->route('admin.dashboard'), // kasir pakai dashboard yang sama via admin.dashboard
        default  => redirect()->route('user.dashboard'),
    };
})->name('dashboard');

// ============================================================================
// ADMIN & KASIR ROUTES — prefix /admin, semua pakai nama admin.*
// ============================================================================

Route::middleware(['auth', 'role:admin,kasir'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // ── Dashboard ────────────────────────────────────────────────────
        // Admin → AdminDashboardController, Kasir → KasirDashboardController
        Route::get('dashboard', function () {
            return auth()->user()->role === 'admin'
                ? app(\App\Http\Controllers\Admin\AdminDashboardController::class)->index()
                : app(\App\Http\Controllers\Kasir\KasirDashboardController::class)->index();
        })->name('dashboard');

        // ── Laporan (admin + kasir) ───────────────────────────────────────
        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');

        // ── Booking (admin + kasir) ───────────────────────────────────────
        Route::get('bookings/calendar',      [BookingController::class, 'calendar'])->name('bookings.calendar');
        Route::get('bookings/calendar-data', [BookingController::class, 'calendarData'])->name('bookings.calendar-data');
        Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);

        // ── Pembayaran (admin + kasir) ────────────────────────────────────
        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);

        // ── Kehadiran (admin + kasir) ─────────────────────────────────────
        Route::get('/attendances',                               [TherapistAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/therapists/{therapist}/attendance/history', [TherapistAttendanceController::class, 'history'])->name('attendance.history');
        Route::get('/attendance/check-in',                       [TherapistAttendanceController::class, 'showCheckInCamera'])->name('attendance.check-in-camera');
        Route::get('/attendance/check-out',                      [TherapistAttendanceController::class, 'showCheckOutCamera'])->name('attendance.check-out-camera');
        Route::post('/attendance/check-in-ajax',                 [TherapistAttendanceController::class, 'checkInAjax'])->name('attendance.check-in-ajax');
        Route::post('/attendance/check-out-ajax',                [TherapistAttendanceController::class, 'checkOutAjax'])->name('attendance.check-out-ajax');

        // ── Master Data (admin only) ──────────────────────────────────────
        Route::middleware('role:admin')->group(function () {

            Route::resource('customers',   \App\Http\Controllers\Admin\CustomerController::class);
            Route::resource('therapists',  \App\Http\Controllers\Admin\TherapistController::class);
            Route::resource('services',    \App\Http\Controllers\Admin\ServiceController::class);
            Route::resource('memberships', \App\Http\Controllers\Admin\MembershipController::class);
            Route::resource('promos',      \App\Http\Controllers\Admin\PromoController::class);
            Route::resource('programs',    \App\Http\Controllers\Admin\ProgramController::class);
            Route::resource('barang',      \App\Http\Controllers\BarangController::class);

            Route::patch('/programs/{program}/toggle-active', [ProgramController::class, 'toggleActive'])
                ->name('programs.toggle-active');

            Route::get('/therapists/{therapist}/face/register', [TherapistFaceController::class, 'create'])->name('therapist-face.register');
            Route::post('/therapists/{therapist}/face',          [TherapistFaceController::class, 'store'])->name('therapist-face.store');
            Route::post('/therapists/{therapist}/face/verify',   [TherapistFaceController::class, 'verify'])->name('therapist-face.verify');
            Route::delete('/therapists/{therapist}/face',        [TherapistFaceController::class, 'destroy'])->name('therapist-face.destroy');

            Route::prefix('customers/{customer}/memberships')
                ->name('customers.membership.')
                ->group(function () {
                    Route::get('/',                          [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'index'])->name('index');
                    Route::get('/create',                    [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'create'])->name('create');
                    Route::post('/',                          [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'store'])->name('store');
                    Route::get('/{customerMembership}/edit', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'edit'])->name('edit');
                    Route::put('/{customerMembership}',      [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'update'])->name('update');
                    Route::delete('/{customerMembership}',   [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'destroy'])->name('destroy');
                });
        });
        Route::resource('schedules', \App\Http\Controllers\Admin\TherapistScheduleController::class)
            ->names('schedules');

        // Generate jadwal otomatis
        Route::post(
            'schedules/generate-month',
            [\App\Http\Controllers\Admin\TherapistScheduleController::class, 'generateMonthSchedule']
        )
            ->name('schedules.generate');

        Route::get('/commissions',                  [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
        Route::patch('/commissions/{commission}/mark-paid', [\App\Http\Controllers\Admin\CommissionController::class, 'markPaid'])->name('commissions.mark-paid');
        Route::post('/commissions/bulk-paid',       [\App\Http\Controllers\Admin\CommissionController::class, 'markBulkPaid'])->name('commissions.bulk-paid');
        Route::get('/commissions/therapist/{therapist}', [\App\Http\Controllers\Admin\CommissionController::class, 'therapistSummary'])->name('commissions.therapist');
    });

// ============================================================================
// KASIR DASHBOARD — redirect ke admin.dashboard
// ============================================================================

Route::middleware(['auth', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Kasir\KasirDashboardController::class, 'index'])
            ->name('dashboard');
    });

// ============================================================================
// USER ROUTES
// ============================================================================

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', fn() => view('user.dashboard'))->name('user.dashboard');
});

// ============================================================================
// PROFILE ROUTES
// ============================================================================

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ============================================================================
// AUTH ROUTES
// ============================================================================

require __DIR__ . '/auth.php';
