<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserLibraryController;
use Illuminate\Support\Facades\Route;

Route::middleware('firebase')->group(function () {
    // Endpoint untuk Books
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    // Route::post('/books/sync', [BookController::class, 'sync']); masih gabisa ini error

    // Endpoint untuk User Library
    Route::get('/user-library', [UserLibraryController::class, 'index']); // Mendapatkan daftar user library
    Route::post('/user-library', [UserLibraryController::class, 'store']); // Membuat entri user library baru
    Route::get('/user-library/{userLibrary}', [UserLibraryController::class, 'show']); // Mendapatkan detail entri user library
    Route::put('/user-library/{userLibrary}', [UserLibraryController::class, 'update']); // Memperbarui entri user library
    Route::delete('/user-library/{userLibrary}', [UserLibraryController::class, 'destroy']); // Menghapus entri user library
    Route::get('/user-library/reading', [UserLibraryController::class, 'getReadingBooks']); // Mendapatkan buku dengan status READING
});
