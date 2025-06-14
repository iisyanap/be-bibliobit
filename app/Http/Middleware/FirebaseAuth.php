<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Models\LocalUser;
use Illuminate\Support\Facades\Log;

class FirebaseAuth
{
    protected $auth;

    public function __construct()
    {
        $credentialsPath = env('FIREBASE_CREDENTIALS', storage_path('bibliobit-service-account.json'));

        if (!$credentialsPath || !file_exists($credentialsPath)) {
            throw new \Exception("Firebase credentials not found at: {$credentialsPath}");
        }

        try {
            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->auth = $factory->createAuth();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Firebase Auth: ' . $e->getMessage());
            throw $e;
        }
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $token = $request->bearerToken();
            if (!$token) {
                return response()->json(['error' => 'Unauthorized: No token provided'], 401);
            }

            Log::info("Token Firebase diterima dari client: {$token}");

            // Verifikasi token
            $verifiedIdToken = $this->auth->verifyIdToken($token);

            // Ambil uid dan data klaim lainnya
            $uid = $verifiedIdToken->claims()->get('sub');
            $email = $verifiedIdToken->claims()->get('email') ?? null;
            $name = $verifiedIdToken->claims()->get('name') ?? null;

            // Contoh tambahan ambil waktu issued at (iat) dan format agar tidak error
            $issuedAt = $verifiedIdToken->claims()->get('iat');
            if ($issuedAt instanceof \DateTimeImmutable) {
                $issuedAtFormatted = $issuedAt->format('Y-m-d H:i:s');
            } elseif (is_int($issuedAt)) {
                // iat kadang bisa berupa timestamp integer
                $issuedAtFormatted = date('Y-m-d H:i:s', $issuedAt);
            } else {
                $issuedAtFormatted = (string) $issuedAt;
            }

            Log::info("Token issued at: {$issuedAtFormatted}");

            // Sinkronkan pengguna ke database lokal
            $user = LocalUser::firstOrCreate(
                ['uid' => $uid],
                [
                    'email' => $email,
                    'username' => $name ?? 'user_' . substr($uid, 0, 8),
                    'name' => $name ?? 'User_' . substr($uid, 0, 8),
                    'is_synced' => true,
                ]
            );

            // Tambahkan pengguna ke request untuk controller
            $request->merge(['user' => $user]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Firebase Auth Error: ' . $e->getMessage());
            return response()->json(['error' => 'Unauthorized: Invalid token'], 401);
        }
    }
}
