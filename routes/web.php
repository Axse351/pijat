<?php

use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;


// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard utama Breeze — redirect sesuai role
Route::middleware('auth')->get('/dashboard', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('user.dashboard');
})->name('dashboard');


// ================= ADMIN =================
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('bookings/calendar', [BookingController::class, 'calendar'])
            ->name('bookings.calendar');

        Route::get('bookings/calendar-data', [BookingController::class, 'calendarData'])
            ->name('bookings.calendar-data');

        Route::resource('customers',  \App\Http\Controllers\Admin\CustomerController::class);
        Route::resource('therapists', \App\Http\Controllers\Admin\TherapistController::class);
        Route::resource('services',   \App\Http\Controllers\Admin\ServiceController::class);
        Route::resource('bookings',   \App\Http\Controllers\Admin\BookingController::class);
        Route::resource('payments',   \App\Http\Controllers\Admin\PaymentController::class);
        Route::resource('memberships', \App\Http\Controllers\Admin\MembershipController::class);
        Route::resource('promos', \App\Http\Controllers\Admin\PromoController::class);
        Route::resource('barang', \App\Http\Controllers\BarangController::class);

        // ================= CUSTOMER MEMBERSHIP (NESTED) =================
        Route::prefix('customers/{customer}/memberships')
            ->name('customers.membership.')
            ->group(function () {
                Route::get('/',                              [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'index'])->name('index');
                Route::get('/create',                        [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'create'])->name('create');
                Route::post('/',                             [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'store'])->name('store');
                Route::get('/{customerMembership}/edit',    [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'edit'])->name('edit');
                Route::put('/{customerMembership}',         [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'update'])->name('update');
                Route::delete('/{customerMembership}',      [\App\Http\Controllers\Admin\CustomerMembershipController::class, 'destroy'])->name('destroy');
            });
    });


// ================= USER =================
Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/user/dashboard', function () {
        return view('user.dashboard');
    })->name('user.dashboard');
});


// ================= PROFILE =================
Route::middleware('auth')->group(function () {
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
