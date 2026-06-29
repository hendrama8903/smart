<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMenuAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        try {
            // Cari screen menu yang URL-nya cocok dengan request saat ini
            $currentUrl = rtrim(url()->current(), '/');

            $menu = Menu::where('aktif', true)
                ->where('type', 'screen')
                ->get(['id', 'controller', 'fungsi', 'url', 'roles'])
                ->first(function ($m) use ($currentUrl) {
                    $link = $m->link();
                    return $link !== '#' && rtrim($link, '/') === $currentUrl;
                });

            // Jika menu ditemukan dan user tidak punya akses → 403
            if ($menu && ! $menu->visibleTo($user)) {
                abort(403, 'Anda tidak memiliki hak akses ke halaman ini.');
            }
        } catch (\Throwable) {
            // Jika pengecekan gagal (misal menu table belum ada), biarkan lanjut
        }

        return $next($request);
    }
}
