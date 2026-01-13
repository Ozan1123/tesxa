<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GuestController;

Route::get('/', function () {
    return view('main');
});

Route::post('/guests/store', [GuestController::class, 'store'])->name('guest.store');
Route::get('/admin', [GuestController::class, 'index'])->name('admin.index');
Route::delete('/guests/{id}', [GuestController::class, 'destroy'])->name('guests.destroy');
Route::get('/registered-guests', function () {
    return view('registered_guests');
})->name('registered.guests');

Route::get('/api/guests', [GuestController::class, 'apiGuests'])->name('api.guests');

// Admin Routes
Route::prefix('admin')->group(function () {
    Route::get('/vip', [GuestController::class, 'vip'])->name('admin.vip');
    Route::post('/vip/store', [GuestController::class, 'storeVip'])->name('guests.storeVip');

    Route::get('/monitoring', [GuestController::class, 'monitoring'])->name('admin.monitoring');
    Route::post('/visits/{id}/checkout', [GuestController::class, 'forceCheckout'])->name('visits.forceCheckout'); // Updated logic

    Route::get('/reports', [GuestController::class, 'reports'])->name('admin.reports');
});

// Hybrid Workflow API Routes (Frontend Scanner)
Route::get('/api/guests/descriptors', [GuestController::class, 'getDescriptors'])->name('api.descriptors');
Route::post('/api/visits/check-status', [GuestController::class, 'checkStatus'])->name('visits.checkStatus');
Route::post('/api/visits/check-in', [GuestController::class, 'checkin'])->name('visits.checkin');
Route::post('/api/visits/check-out', [GuestController::class, 'checkOut'])->name('visits.checkOut');
