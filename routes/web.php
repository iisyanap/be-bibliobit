<?php

use Illuminate\Support\Facades\Route;
// Pastikan semua controller yang dibutuhkan sudah di-import
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ChartController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan rute web untuk aplikasi Anda. Rute-rute ini
| dimuat oleh RouteServiceProvider dan semuanya akan ditugaskan ke
| grup middleware "web".
|
*/

// Rute Publik (bisa diakses siapa saja)
Route::get('/', function () {
    return view('welcome');
});

// Rute Autentikasi Admin (hanya login dan logout yang diaktifkan)
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// =================== PENGAMANAN RUTE ADMIN ===================
// Semua rute di dalam grup ini hanya bisa diakses setelah login.
Route::middleware('auth')->prefix('admin')->group(function () {

    // Rute untuk dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Rute untuk charts
    Route::get('/charts', [ChartController::class, 'index'])->name('admin.charts');

});
// =============================================================

