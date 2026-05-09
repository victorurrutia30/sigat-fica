<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'rol' => \App\Http\Middleware\CheckRol::class,
        ]);

        $middleware->redirectUsersTo(function (Request $request) {
            $usuario = $request->user();

            return match ($usuario?->rol) {
                'coordinacion' => route('dashboard', absolute: false),
                'tutor' => route('mis-asignaciones', absolute: false),
                default => '/',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
