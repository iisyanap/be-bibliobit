<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // Tambahkan rute API
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Daftarkan middleware FirebaseAuth dengan alias 'firebase'
        $middleware->alias([
            'firebase' => \App\Http\Middleware\FirebaseAuth::class,
        ]);

        // (Opsional) Tambahkan middleware ke grup 'api' jika ingin diterapkan otomatis
        $middleware->api(prepend: [
            \App\Http\Middleware\FirebaseAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
