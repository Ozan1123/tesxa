<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\GuestController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/guests/store', [GuestController::class, 'store'])->name('guest.store');
Route::get('/admin', [GuestController::class, 'index'])->name('admin.index');
Route::delete('/guests/{id}', [GuestController::class, 'destroy'])->name('guests.destroy');
