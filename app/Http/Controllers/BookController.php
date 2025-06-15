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

    // public function index(Request $request)
    // {
    // $query = $request->query('q');

    // if ($query) {
    //     $books = Book::where('title', 'like', "%{$query}%")
    //                  ->orWhere('author', 'like', "%{$query}%")
    //                  ->take(5) // Batasi hasil pencarian lokal
    //                  ->get();
    // } else {
    //     // Jika tidak mencari, jangan kembalikan semua buku. Itu tidak efisien.
    //     // Kembalikan array kosong atau buku terbaru saja.
    //     $books = Book::latest()->take(10)->get();
    // }

    //     return response()->json($books);
    // }

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
            'isbn' => 'nullable|string|unique:books,isbn,' . ($request->id ?? 'NULL') . ',id', // ISBN harus unik
            'pages' => 'sometimes|integer',
            'publisher' => 'nullable|string',
            'cover_photo_path' => 'nullable|string',
        ]);

        if (!empty($validated['isbn'])) {
        $book = Book::updateOrCreate(
            ['isbn' => $validated['isbn']], // Kunci untuk mencari
            $validated                      // Data untuk di-update atau di-create
        );
        } else {
            $book = Book::create($validated);
        }

        $book->update($validated);
        return response()->json($book, $book->wasRecentlyCreated ? 201 : 200);
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

    public function findOrCreate(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:20',
            // Tambahkan validasi untuk field lain yang dikirim dari GoogleBook
            'publisher' => 'nullable|string',
            'year' => 'nullable|integer',
            'pages' => 'required|integer',
            'description' => 'nullable|string',
            'cover_photo_path' => 'nullable|string',
            'genre' => 'nullable|string',
        ]);

        $book = null;

        if (!empty($data['isbn'])) {
            $book = Book::where('isbn', $data['isbn'])->first();
        }

        if (!$book) {
            $book = Book::where('title', $data['title'])
                        ->where('author', $data['author'])
                        ->first();
        }

        if (!$book) {
            $book = Book::create($data);
            return response()->json($book, 201);
        }

        return response()->json($book, 200);
    }
}
