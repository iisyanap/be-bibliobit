<?php

namespace App\Http\Controllers;

use App\Models\ReadingProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReadingProgressController extends Controller
{
    /**
     * Menampilkan daftar data progres baca.
     */
    public function index(Request $request)
    {
        $user = $request->user;
        $userLibraryId = $request->query('user_library_id');

        $userLibraryIds = $user->userLibraries()->pluck('id');

        $query = ReadingProgress::whereIn('user_library_id', $userLibraryIds);

        if ($userLibraryId) {
            $query->where('user_library_id', $userLibraryId);
        }

        $progress = $query->orderBy('recorded_at', 'desc')->get();

        return response()->json($progress);
    }

    /**
     * Menyimpan data progres baca baru dan mengupdate user_library.
     */
    public function store(Request $request)
    {
        $user = $request->user;

        $validated = $request->validate([
            'user_library_id' => 'required|integer|exists:user_library,id',
            'page_read' => 'required|integer',
            'recorded_at' => 'required|date',
        ]);

        // 1. Validasi keamanan: pastikan user_library_id ini benar-benar milik pengguna.
        $userLibrary = $user->userLibraries()->find($validated['user_library_id']);

        if (!$userLibrary) {
            return response()->json(['error' => 'Unauthorized or invalid library entry.'], 403);
        }

        // 2. Gunakan try-catch untuk memastikan semua operasi berhasil atau tidak sama sekali.
        try {
            // Buat entri ReadingProgress baru.
            $readingProgress = ReadingProgress::create([
                'user_library_id' => $validated['user_library_id'],
                'page_read'       => $validated['page_read'],
                'recorded_at'     => $validated['recorded_at'],
                'user_id'         => $userLibrary->user_id,
            ]);

            // 3. Langsung update user_library dengan halaman terakhir yang dibaca.
            $userLibrary->last_page_read = $validated['page_read'];
            
            // Opsional: Jika Anda ingin status berubah dari PLAN_TO_READ menjadi READING
            if ($userLibrary->status === 'PLAN_TO_READ') {
                $userLibrary->status = 'READING';
            }
            
            $userLibrary->save();

            // Berhasil, kembalikan data progres yang baru dibuat.
            return response()->json($readingProgress, 201);

        } catch (\Exception $e) {
            // Jika terjadi error, catat di log dan kembalikan response error.
            Log::error('Gagal menyimpan progres baca: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan data. Silakan coba lagi.'], 500);
        }
    }
}