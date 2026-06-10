<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Services\NotificacionService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer('layouts.navigation', function ($view) {
            $usuario = Auth::user();

            $totalNotificacionesNoLeidas = 0;

            if ($usuario) {
                $notificacionService = app(NotificacionService::class);

                $notificacionService->sincronizarCumplimiento();

                $totalNotificacionesNoLeidas = $notificacionService
                    ->cantidadNoLeidas($usuario);
            }

            $view->with('totalNotificacionesNoLeidas', $totalNotificacionesNoLeidas);
        });
    }
}
