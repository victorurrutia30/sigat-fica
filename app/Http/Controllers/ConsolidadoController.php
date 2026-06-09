<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidadoEntregaRequest;
use App\Services\ConsolidadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsolidadoController extends Controller
{
    public function index(
        Request $request,
        ConsolidadoService $consolidadoService
    ): View {
        $mensajeBloqueo = null;
        $contexto = [
            'periodo' => null,
            'tutor' => null,
            'consolidado' => null,
            'casos' => collect(),
            'diagnostico' => [
                'total' => 0,
                'cerrados' => 0,
                'abiertos' => 0,
                'incompletos' => 0,
                'detalle_incompletos' => [],
            ],
        ];

        try {
            $contexto = $consolidadoService->contextoParaTutor($request->user());
        } catch (ValidationException $exception) {
            $mensajeBloqueo = collect($exception->errors())->flatten()->first();
        }

        return view('tutor.consolidado.index', array_merge($contexto, [
            'mensajeBloqueo' => $mensajeBloqueo,
        ]));
    }

    public function entregar(
        ConsolidadoEntregaRequest $request,
        ConsolidadoService $consolidadoService
    ): RedirectResponse {
        try {
            $consolidadoService->entregar(
                usuario: $request->user(),
                confirmarSinCasos: $request->boolean('confirmar_sin_casos')
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route('consolidado.index')
                ->withErrors($exception->errors())
                ->withInput()
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('consolidado.index')
            ->with('success', 'Consolidado entregado correctamente.');
    }
}
