<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserLibraryController;
use Illuminate\Support\Facades\Route;

Route::middleware('firebase')->group(function () {
    Route::apiResource('books', BookController::class);
    Route::apiResource('user-library', UserLibraryController::class);

    // ✅ Route baru untuk mengambil buku dengan status READING
    Route::get('user-library/reading', [UserLibraryController::class, 'getReadingBooks']);
});
