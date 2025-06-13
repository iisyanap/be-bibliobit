<?php

use App\Http\Controllers\BookController;
use App\Http\Controllers\UserLibraryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReadingProgressController;
use App\Models\LocalUser;

Route::middleware('firebase')->group(function () {
    // Endpoint untuk Books
    Route::get('/books', [BookController::class, 'index']);
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{book}', [BookController::class, 'show']);
    Route::put('/books/{book}', [BookController::class, 'update']);
    Route::delete('/books/{book}', [BookController::class, 'destroy']);
    Route::post('/books/sync', [BookController::class, 'sync']);

    // Endpoint untuk User Library
    Route::get('/user-library', [UserLibraryController::class, 'index']); // Mendapatkan daftar user library
    Route::post('/user-library', [UserLibraryController::class, 'updateOrCreate']); // Membuat entri user library baru
    Route::get('/user-library/{userLibrary}', [UserLibraryController::class, 'show']); // Mendapatkan detail entri user library
    Route::put('/user-library/{userLibrary}', [UserLibraryController::class, 'update']); // Memperbarui entri user library
    Route::delete('/user-library/{userLibrary}', [UserLibraryController::class, 'destroy']); // Menghapus entri user library
    Route::get('/user-library/reading', [UserLibraryController::class, 'getReadingBooks']); // Mendapatkan buku dengan status READING

    // Endpoint untuk Profile
    Route::get('/profile', [App\Http\Controllers\LocalUserController::class, 'getProfile']);
    Route::put('/profile', [App\Http\Controllers\LocalUserController::class, 'updateProfile']);
    // Route::post('/upload-image', [LocalUser::class, 'upload']);

    Route::get('/reading-progress', [ReadingProgressController::class, 'index']);
    Route::post('/reading-progress', [ReadingProgressController::class, 'store']);
});
