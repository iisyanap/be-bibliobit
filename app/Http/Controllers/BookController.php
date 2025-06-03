<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function sync(Request $request)
    {
        $validator = Validator::make($request->all(), [
            '*.id' => 'required|integer',
            '*.title' => 'required|string',
            '*.author' => 'required|string',
            '*.publisher' => 'required|string',
            '*.genre' => 'nullable|string',
            '*.pages' => 'nullable|integer',
            '*.year' => 'nullable|integer',
            '*.isbn' => 'nullable|string',
            '*.description' => 'nullable|string',
            '*.cover_photo_path' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $syncedBooks = [];
        foreach ($request->all() as $data) {
            $book = Book::updateOrCreate(['id' => $data['id']], $data);
            $syncedBooks[] = $book;
        }

        return response()->json(['data' => $syncedBooks], 200);
    }
}
