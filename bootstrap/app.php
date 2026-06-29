<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Alias middleware role (RBAC)
        $middleware->alias([
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'menu.access' => \App\Http\Middleware\CheckMenuAccess::class,
        ]);

        // Berlaku untuk semua halaman web: cegah cache halaman setelah logout (anti tombol Back)
        $middleware->web(append: [
            \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
