<?php

use App\Livewire\Attendances;
use App\Livewire\Cards;
use App\Livewire\Dashboard;
use App\Livewire\Devices;
use App\Livewire\Employees;
use App\Livewire\Login;
use App\Livewire\Schedules;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/login', Login::class)->name('login')->middleware('guest');

Route::middleware('auth')->group(function () {
    Route::get('/', Dashboard::class)->name('dashboard');
    Route::get('/absensi', Attendances::class)->name('attendances');
    Route::get('/karyawan', Employees::class)->name('employees');
    Route::get('/kartu-rfid', Cards::class)->name('cards');
    Route::get('/jam-kerja', Schedules::class)->name('schedules');
    Route::get('/perangkat', Devices::class)->name('devices');

    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});
