<?php

use Illuminate\Support\Facades\Route;
// Pastikan kedua controller sudah di-import
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ChartController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Rute Autentikasi
Route::get('/login', function () {
    return view('auth.login');
});

Route::get('/register', function () {
    return view('auth.register');
});

// Rute Admin
Route::prefix('admin')->group(function () {
    // Rute untuk dashboard sudah benar
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // UBAH BAGIAN INI: Arahkan ke ChartController
    Route::get('/charts', [ChartController::class, 'index']);
});
