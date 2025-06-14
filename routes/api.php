<?php

use App\Models\LocalUser;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\GoogleBooksController;
use App\Http\Controllers\UserLibraryController;
use App\Http\Controllers\ReadingProgressController;

// RUTE PUBLIK - Tidak perlu login
Route::get('/google-books/search', [GoogleBooksController::class, 'search']);
Route::get('/google-books/isbn/{isbn}', [GoogleBooksController::class, 'findByIsbn']);

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

    // Endpoint notes
    Route::get('/user-library/{userLibrary}/notes', [NoteController::class, 'index']);
    Route::post('/user-library/{userLibrary}/notes', [NoteController::class, 'store']);
    // Route::get('/notes/{note}', [NoteController::class, 'show']);
    Route::post('/notes/{note}', [NoteController::class, 'update']);
    Route::delete('/notes/{note}', [NoteController::class, 'destroy']);

    // Endpoint Statistic
    Route::get('/statistics', [StatisticController::class, 'index']);

    // // Endpoint baru untuk Google Books API
    // Route::get('/google-books/search', [GoogleBooksController::class, 'search']);
    // Route::get('/google-books/isbn/{isbn}', [GoogleBooksController::class, 'findByIsbn']);

});
