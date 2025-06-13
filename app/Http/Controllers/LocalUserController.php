<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocalUserController extends Controller
{
    /**
     * Mengambil profil pengguna yang sedang login.
     */
    public function getProfile(Request $request)
    {
        // $request->user ditambahkan oleh middleware FirebaseAuth kita
        return response()->json($request->user);
    }

    /**
     * Memperbarui profil pengguna yang sedang login.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user;

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'username' => 'sometimes|string|max:255|unique:local_users,username,' . $user->uid . ',uid',
            'profile_image' => 'nullable|string'
        ]);

        $user->update($validated);

        return response()->json($user);
    }

    public function uploadImage()
    {
        
    }
}
