<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventBackHistory
{
    /**
     * Cegah halaman ter-cache muncul lagi via tombol Back setelah logout.
     * Memaksa browser meminta ulang halaman -> kena middleware auth -> diarahkan ke login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        return $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }
}