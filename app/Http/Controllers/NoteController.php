<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\UserLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, UserLibrary $userLibrary)
    {
        if ($userLibrary->user_id !== $request->user->uid) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $notes = $userLibrary->notes()->orderBy('created_at', 'desc')->get();

        $notes->transform(function ($note) {
            if ($note->image) {
                // Pastikan `APP_URL` di file .env Anda sudah benar.
                $note->image_url = asset('storage/' . $note->image);
            }
            return $note;
        });

        return response()->json($notes);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, UserLibrary $userLibrary)
    {

        // Log saat fungsi dimulai
        Log::info('Memulai fungsi store untuk user_library_id: ' . $userLibrary->id);

        try {
            if ($userLibrary->user_id !== $request->user->uid) {
                Log::warning('Upaya akses tidak sah ke user_library_id: ' . $userLibrary->id . ' oleh user: ' . $request->user->uid);
                return response()->json(['message' => 'Akses ditolak.'], 403);
            }

            $validator = Validator::make($request->all(), [
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                Log::error('Validasi gagal: ', $validator->errors()->toArray());
                return response()->json($validator->errors(), 422);
            }

            $data = $validator->validated();

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('notes_images', 'public');
                $data['image'] = $path;
            }

            $note = $userLibrary->notes()->create($data);

            Log::info('Catatan berhasil dibuat dengan id: ' . $note->id);

            if ($note->image) {
                $note->image_url = asset('storage/' . $note->image);
            }

            return response()->json($note, 201);

        } catch (\Exception $e) {
            Log::error('Terjadi CRITICAL ERROR di fungsi store: ' . $e->getMessage());
            Log::error($e->getTraceAsString()); // Catat jejak lengkap error

            return response()->json([
                'message' => 'Terjadi kesalahan internal pada server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Note $note)
    {
        if ($note->userLibrary->user_id !== $request->user()->uid) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        if ($note->image) {
            $note->image_url = asset(Storage::url($note->image));
        }

        return response()->json($note);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        if ($note->userLibrary->user_id !== $request->user->uid) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        $validator = Validator::make($request->all(), [
            'content' => 'sometimes|required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $data = $validator->validated();
        if ($request->hasFile('image')) {
            if ($note->image) { Storage::disk('public')->delete($note->image); }
            $path = $request->file('image')->store('notes_images', 'public');
            $data['image'] = $path;
        }
        $note->update($data);
        $note->refresh();
        if ($note->image) { $note->image_url = asset('storage/' . $note->image); }
        return response()->json($note);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Note $note)
    {
        if ($note->userLibrary->user_id !== $request->user->uid) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }
        if ($note->image) { Storage::disk('public')->delete($note->image); }
        $note->delete();
        return response()->json(null, 204);
    }
}
