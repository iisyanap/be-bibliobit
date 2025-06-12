<?php

namespace App\Http\Controllers;

use App\Models\ReadingProgress;
use Illuminate\Http\Request;

class ReadingProgressController extends Controller
{
    /**
     * Menampilkan daftar data progres baca.
     * Bisa difilter berdasarkan user_library_id jika parameter itu diberikan.
     */
    public function index(Request $request)
    {
        $user = $request->user;
        $userLibraryId = $request->query('user_library_id');

        // Ambil semua ID library yang SAH milik pengguna yang sedang login
        $userLibraryIds = $user->userLibraries()->pluck('id');

        // Mulai query dasar: ambil semua progress yang terkait dengan library milik user
        $query = ReadingProgress::whereIn('user_library_id', $userLibraryIds);

        // Jika ada parameter user_library_id di URL, tambahkan filter
        if ($userLibraryId) {
            // Filter lebih lanjut untuk hanya mengambil progres dari ID spesifik yang diminta
            $query->where('user_library_id', $userLibraryId);
        }

        // Ambil data dan urutkan berdasarkan tanggal terbaru
        $progress = $query->orderBy('recorded_at', 'desc')->get();

        return response()->json($progress);
    }

    /**
     * Menyimpan data progres baca baru.
     */
    public function store(Request $request)
    {
        $user = $request->user;

        $validated = $request->validate([
            'user_library_id' => 'required|integer',
            'page_read' => 'required|integer',
            'recorded_at' => 'required|date',
        ]);

        // Validasi keamanan: pastikan user_library_id ini benar-benar milik pengguna yang sedang login
        $userLibrary = $user->userLibraries()->find($validated['user_library_id']);

        if (!$userLibrary) {
            // Jika tidak, tolak permintaan untuk mencegah pengguna menyimpan progres di buku orang lain
            return response()->json(['error' => 'Unauthorized or invalid library entry.'], 403);
        }

        $readingProgress = ReadingProgress::create($validated);

        return response()->json($readingProgress, 201);
    }
}