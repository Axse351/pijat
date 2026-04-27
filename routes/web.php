<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\ContentController;
use App\Http\Controllers\Admin\TherapistFaceController;
use App\Http\Controllers\Admin\TherapistAttendanceController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\TherapistScheduleController;
use App\Http\Controllers\Admin\TherapistLeaveController;
use App\Http\Controllers\Admin\WaMessageTemplateController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AtkCategoryController;
use Illuminate\Support\Facades\Route;

// ============================================================================
// ROOT
// ============================================================================

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// ============================================================================
// PUBLIC BOOKING
// ============================================================================

Route::post('/booking', [PublicBookingController::class, 'store'])->name('public.booking.store');

// ============================================================================
// DASHBOARD
// ============================================================================

Route::middleware('auth')->get('/dashboard', function () {
    return match (auth()->user()->role) {
        'admin'     => redirect()->route('admin.dashboard'),
        'kasir'     => redirect()->route('kasir.dashboard'),
        'therapist' => redirect()->route('terapis.dashboard'),
        default     => redirect()->route('user.dashboard'),
    };
})->name('dashboard');

Route::middleware('auth')->get('/debug-role', function () {
    $user = auth()->user();
    dd([
        'id'         => $user->id,
        'name'       => $user->name,
        'email'      => $user->email,
        'role'       => $user->role,
        'therapist'  => $user->therapist,
        'role_check' => [
            "=== 'therapist'"  => $user->role === 'therapist',
            "=== 'terapis'"    => $user->role === 'terapis',
            "=== 'Therapist'"  => $user->role === 'Therapist',
        ],
    ]);
})->name('debug.role');

// ============================================================================
// ADMIN & KASIR ROUTES
// ============================================================================

Route::middleware(['auth', 'role:admin,kasir'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('dashboard', function () {
            return auth()->user()->role === 'admin'
                ? app(\App\Http\Controllers\Admin\AdminDashboardController::class)->index()
                : app(\App\Http\Controllers\Kasir\KasirDashboardController::class)->index();
        })->name('dashboard');

        Route::get('/laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])->name('laporan.index');

        Route::get('bookings/calendar',      [BookingController::class, 'calendar'])->name('bookings.calendar');
        Route::get('bookings/calendar-data', [BookingController::class, 'calendarData'])->name('bookings.calendar-data');
        Route::resource('bookings', \App\Http\Controllers\Admin\BookingController::class);

        Route::resource('payments', \App\Http\Controllers\Admin\PaymentController::class);

        Route::get('/attendances', [TherapistAttendanceController::class, 'index'])->name('attendances.index');
        Route::get('/therapists/{therapist}/attendance/history', [TherapistAttendanceController::class, 'history'])->name('attendance.history');
        Route::get('/attendance/check-in', [TherapistAttendanceController::class, 'showCheckInCamera'])->name('attendance.check-in-camera');
        Route::get('/attendance/check-out', [TherapistAttendanceController::class, 'showCheckOutCamera'])->name('attendance.check-out-camera');
        Route::post('/attendance/check-in-ajax', [TherapistAttendanceController::class, 'checkInAjax'])->name('attendance.check-in-ajax');
        Route::post('/attendance/check-out-ajax', [TherapistAttendanceController::class, 'checkOutAjax'])->name('attendance.check-out-ajax');

        // ── Leave Requests Terapis (admin & kasir bisa lihat, admin bisa approve/reject) ──
        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/',                           [TherapistLeaveController::class, 'index'])->name('index');
            Route::get('/{leaveRequest}',             [TherapistLeaveController::class, 'show'])->name('show');
            Route::patch('/{leaveRequest}/approve',   [TherapistLeaveController::class, 'approve'])->name('approve');
            Route::patch('/{leaveRequest}/reject',    [TherapistLeaveController::class, 'reject'])->name('reject');
            Route::delete('/{leaveRequest}',          [TherapistLeaveController::class, 'destroy'])->name('destroy');
        });

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
            Route::post('/therapists/{therapist}/face', [TherapistFaceController::class, 'store'])->name('therapist-face.store');
            Route::post('/therapists/{therapist}/face/verify', [TherapistFaceController::class, 'verify'])->name('therapist-face.verify');
            Route::delete('/therapists/{therapist}/face', [TherapistFaceController::class, 'destroy'])->name('therapist-face.destroy');

            Route::prefix('customers/{customer}/memberships')
                ->name('customers.membership.')
                ->group(function () {
                    Route::get('/', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'index'])->name('index');
                    Route::get('/create', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'create'])->name('create');
                    Route::post('/', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'store'])->name('store');
                    Route::get('/{customerMembership}/edit', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'edit'])->name('edit');
                    Route::put('/{customerMembership}', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'update'])->name('update');
                    Route::delete('/{customerMembership}', [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'destroy'])->name('destroy');
                });

            Route::resource('atk-items', \App\Http\Controllers\AtkController::class);
            Route::post('atk-items/{atk}/adjust-stock', [\App\Http\Controllers\AtkController::class, 'adjustStock'])
                ->name('atk-items.adjust-stock');
            Route::get('/laporan/export', [\App\Http\Controllers\Admin\LaporanController::class, 'export'])
                ->name('laporan.export');

            // ── Content Routes ────────────────────────────────────────────────
            Route::get('content',    [ContentController::class, 'index'])->name('content.index');
            Route::put('content',    [ContentController::class, 'update'])->name('content.update');
            Route::delete('content', [ContentController::class, 'reset'])->name('content.reset');

            // ── Template Pesan WhatsApp ───────────────────────────────────────
            Route::prefix('wa-templates')->name('wa-templates.')->group(function () {
                Route::get('/',                       [WaMessageTemplateController::class, 'index'])->name('index');
                Route::get('/{waTemplate}/edit',      [WaMessageTemplateController::class, 'edit'])->name('edit');
                Route::put('/{waTemplate}',           [WaMessageTemplateController::class, 'update'])->name('update');
                Route::put('/{waTemplate}/reset',     [WaMessageTemplateController::class, 'reset'])->name('reset');
                Route::get('/{waTemplate}/preview',   [WaMessageTemplateController::class, 'preview'])->name('preview');
            });
            Route::post('bookings/{booking}/complete', [BookingController::class, 'complete'])
                ->name('bookings.complete');
        });

        Route::resource('atk-purchases', \App\Http\Controllers\AtkPurchaseController::class);
        Route::post('atk-purchases/{purchase}/confirm', [\App\Http\Controllers\AtkPurchaseController::class, 'confirm'])
            ->name('atk-purchases.confirm');
        Route::post('atk-purchases/{purchase}/cancel', [\App\Http\Controllers\AtkPurchaseController::class, 'cancel'])
            ->name('atk-purchases.cancel');
        Route::get('/api/atk-by-category/{category}', [\App\Http\Controllers\AtkPurchaseController::class, 'getAtkByCategory']);
        Route::get('/api/atk-detail/{atk}', [\App\Http\Controllers\AtkPurchaseController::class, 'getAtkDetail']);
        Route::resource('atk-categories', AtkCategoryController::class);

        Route::get('schedules/all', [TherapistScheduleController::class, 'allSchedules'])->name('schedules.all');
        Route::post('schedules/generate-month', [TherapistScheduleController::class, 'generateMonthSchedule'])->name('schedules.generate');
        Route::resource('schedules', TherapistScheduleController::class);

        Route::get('/commissions', [\App\Http\Controllers\Admin\CommissionController::class, 'index'])->name('commissions.index');
        Route::patch('/commissions/{commission}/mark-paid', [\App\Http\Controllers\Admin\CommissionController::class, 'markPaid'])->name('commissions.mark-paid');
        Route::post('/commissions/bulk-paid', [\App\Http\Controllers\Admin\CommissionController::class, 'markBulkPaid'])->name('commissions.bulk-paid');
        Route::get('/commissions/therapist/{therapist}', [\App\Http\Controllers\Admin\CommissionController::class, 'therapistSummary'])->name('commissions.therapist');
        Route::get('bookings/{booking}/receipt', [BookingController::class, 'receipt'])->name('bookings.receipt');
    });

// ============================================================================
// KASIR DASHBOARD
// ============================================================================

Route::middleware(['auth', 'role:kasir'])
    ->prefix('kasir')
    ->name('kasir.')
    ->group(function () {
        Route::get('dashboard', [\App\Http\Controllers\Kasir\KasirDashboardController::class, 'index'])
            ->name('dashboard');
    });

// ============================================================================
// THERAPIST ROUTES
// ============================================================================

Route::middleware(['auth', 'role:therapist'])
    ->prefix('terapis')
    ->name('terapis.')
    ->group(function () {

        Route::get('/', [App\Http\Controllers\Terapis\DashboardController::class, 'index'])
            ->name('dashboard');

        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Terapis\DashboardController::class, 'bookings'])
                ->name('index');
        });

        Route::prefix('schedules')->name('schedules.')->group(function () {
            Route::get('/', [App\Http\Controllers\Terapis\DashboardController::class, 'schedules'])
                ->name('index');
        });

        Route::prefix('leaves')->name('leaves.')->group(function () {
            Route::get('/',               [App\Http\Controllers\Terapis\DashboardController::class, 'leaveRequests'])
                ->name('index');
            Route::get('/create',         [App\Http\Controllers\Terapis\DashboardController::class, 'createLeaveRequest'])
                ->name('create');
            Route::post('/',              [App\Http\Controllers\Terapis\DashboardController::class, 'storeLeaveRequest'])
                ->name('store');
            Route::get('/{leaveRequest}', [App\Http\Controllers\Terapis\DashboardController::class, 'showLeaveRequest'])
                ->name('show');
            Route::delete('/{leaveRequest}', [App\Http\Controllers\Terapis\DashboardController::class, 'cancelLeaveRequest'])
                ->name('destroy');
        });

        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/',                 [App\Http\Controllers\Terapis\DashboardController::class, 'profile'])->name('show');
            Route::post('/',                [App\Http\Controllers\Terapis\DashboardController::class, 'updateProfile'])->name('update');
            Route::post('/change-password', [App\Http\Controllers\Terapis\DashboardController::class, 'changePassword'])->name('change-password');
        });
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

Route::get('/api/booked-slots',     [WelcomeController::class, 'bookedSlots']);
Route::get('/api/bookings-by-date', [WelcomeController::class, 'bookingsByDate']);

// ============================================================================
// AUTH ROUTES
// ============================================================================

require __DIR__ . '/auth.php';
