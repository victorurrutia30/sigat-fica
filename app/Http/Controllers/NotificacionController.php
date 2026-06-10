<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Models\User;
use App\Services\NotificacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificacionController extends Controller
{
    public function index(Request $request, NotificacionService $notificacionService): View
    {
        /** @var User $usuario */
        $usuario = $request->user();

        $notificacionService->sincronizarCumplimiento();

        $notificaciones = Notificacion::query()
            ->where('usuario_id', $usuario->id)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $totalNoLeidas = $notificacionService->cantidadNoLeidas($usuario);

        return view('notificaciones.index', compact(
            'notificaciones',
            'totalNoLeidas'
        ));
    }

    public function marcarLeida(
        Notificacion $notificacion,
        Request $request,
        NotificacionService $notificacionService
    ): RedirectResponse {
        /** @var User $usuario */
        $usuario = $request->user();

        $notificacionService->marcarComoLeida($notificacion, $usuario);

        return redirect()
            ->back()
            ->with('success', 'Notificación marcada como leída.');
    }

    public function marcarTodasLeidas(Request $request): RedirectResponse
    {
        /** @var User $usuario */
        $usuario = $request->user();

        Notificacion::query()
            ->where('usuario_id', $usuario->id)
            ->where('leido', false)
            ->update([
                'leido' => true,
                'leido_en' => now(),
                'updated_at' => now(),
            ]);

        return redirect()
            ->back()
            ->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }
}
