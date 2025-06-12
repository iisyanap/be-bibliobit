<?php

namespace App\Http\Controllers;

use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class UserLibraryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user->uid;
        $status = $request->query('status');
        $query = $request->query('query');

        $userLibrary = UserLibrary::where('user_id', $userId);

        if ($status) {
            $userLibrary->where('status', $status);
        }

        if ($query) {
            $userLibrary->whereHas('book', function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%");
            });
        }

        return response()->json($userLibrary->with('book')->get());
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'book_id' => 'required|exists:books,id',
        'status' => 'required|in:PLAN_TO_READ,READING,FINISH',
        'last_page_read' => 'nullable|integer',
        'rating' => 'nullable|numeric|min:0|max:5',
    ]);

    if ($validated['status'] !== 'FINISH' && isset($validated['rating'])) {
        return response()->json(['error' => 'Rating only allowed for FINISH status'], 422);
    }

    $userLibrary = UserLibrary::create([
        'user_id' => $request->user->uid,
        'book_id' => $validated['book_id'],
        'status' => $validated['status'],
        'last_page_read' => $validated['last_page_read'] ?? null,
        'rating' => $validated['rating'] ?? null,
    ]);

    return response()->json($userLibrary->load('book'), 201);
}

    public function show(UserLibrary $userLibrary)
    {
        if ($userLibrary->user_id !== request()->user->uid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json($userLibrary->load('book'));
    }

    public function update(Request $request, UserLibrary $userLibrary)
    {
        if ($userLibrary->user_id !== $request->user->uid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status' => 'sometimes|in:PLAN_TO_READ,READING,FINISH',
            'last_page_read' => 'nullable|integer',
            'rating' => 'nullable|numeric|min:0|max:5',
        ]);

        if (isset($validated['status']) && $validated['status'] !== 'FINISH' && isset($validated['rating'])) {
            return response()->json(['error' => 'Rating only allowed for FINISH status'], 422);
        }

        $userLibrary->update(array_merge($validated, ['updated_at' => now()]));

        return response()->json($userLibrary->load('book'));
    }

    public function destroy(UserLibrary $userLibrary)
    {
        if ($userLibrary->user_id !== request()->user->uid) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $userLibrary->delete();
        return response()->json(null, 204);
    }

    public function getReadingBooks(Request $request)
    {
        $userId = $request->user->uid;

        $readingBooks = UserLibrary::with('book')
            ->where('user_id', $userId)
            ->where('status', 'READING')
            ->get()
            ->map(function ($item) {
                return [
                    'bookId' => $item->book->id,
                    'bookTitle' => $item->book->title,
                    'coverPhotoPath' => $item->book->cover_photo_path,
                    'lastPageRead' => $item->last_page_read,
                    'totalPages' => $item->book->pages,
                ];
            });

        return response()->json($readingBooks);
    }

    public function updateOrCreate(Request $request)
{
    Log::info("Received request: " . $request->getContent());
    $validated = $request->validate([
        'book_id' => 'required|exists:books,id',
        'status' => 'required|in:PLAN_TO_READ,READING,FINISH',
        'last_page_read' => 'nullable|integer',
        'rating' => 'nullable|numeric|min:0|max:5',
    ]);

    if ($validated['status'] !== 'FINISH' && isset($validated['rating'])) {
        return response()->json(['error' => 'Rating only allowed for FINISH status'], 422);
    }

    $userId = $request->user->uid;
    $bookId = $validated['book_id'];

    $userLibrary = UserLibrary::updateOrCreate(
        [
            'user_id' => $userId,
            'book_id' => $bookId,
        ],
        [
            'status' => $validated['status'],
            'last_page_read' => $validated['last_page_read'] ?? null,
            'rating' => $validated['rating'] ?? null,
            'updated_at' => now(),
        ]
    );

    Log::info("UserLibrary updated/created: " . json_encode($userLibrary));
    return response()->json($userLibrary->load('book'), 200);
}
}

