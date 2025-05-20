<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        return response()->json(Book::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'genre' => 'nullable|string',
            'year' => 'nullable|integer',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string',
            'pages' => 'required|integer',
            'publisher' => 'nullable|string',
            'cover_photo_path' => 'nullable|string',
        ]);

        $book = Book::create($validated);
        return response()->json($book, 201);
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function update(Request $request, Book $book)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'author' => 'sometimes|string|max:255',
            'genre' => 'nullable|string',
            'year' => 'nullable|integer',
            'description' => 'nullable|string',
            'isbn' => 'nullable|string',
            'pages' => 'sometimes|integer',
            'publisher' => 'nullable|string',
            'cover_photo_path' => 'nullable|string',
        ]);

        $book->update($validated);
        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(null, 204);
    }
}
