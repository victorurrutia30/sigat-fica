<?php

namespace App\Http\Controllers;

use App\Http\Requests\CasoSeguimientoRequest;
use App\Models\CasoSeguimiento;
use App\Services\CasoSeguimientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CasoSeguimientoController extends Controller
{
    public function index(
        Request $request,
        CasoSeguimientoService $casoService
    ): View {
        $periodo = null;
        $tutor = null;
        $casos = new Collection();
        $mensajeBloqueo = null;

        try {
            $periodo = $casoService->obtenerPeriodoActivo();
            $tutor = $casoService->obtenerTutorDelUsuario($request->user());
            $casos = $casoService->casosDelTutorEnPeriodo($tutor, $periodo);
        } catch (ValidationException $exception) {
            $mensajeBloqueo = collect($exception->errors())->flatten()->first();
        }

        return view('tutor.casos.index', compact(
            'periodo',
            'tutor',
            'casos',
            'mensajeBloqueo'
        ));
    }

    public function create(
        Request $request,
        CasoSeguimientoService $casoService
    ): View|RedirectResponse {
        try {
            $periodo = $casoService->obtenerPeriodoActivo();
            $tutor = $casoService->obtenerTutorDelUsuario($request->user());
            $secciones = $casoService->seccionesAsignadasParaTutor($tutor, $periodo);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('casos.index')
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        if ($secciones->isEmpty()) {
            return redirect()
                ->route('casos.index')
                ->with('warning', 'No tienes secciones asignadas en una propuesta publicada del ciclo activo.');
        }

        return view('tutor.casos.create', compact(
            'periodo',
            'tutor',
            'secciones'
        ));
    }

    public function store(
        CasoSeguimientoRequest $request,
        CasoSeguimientoService $casoService
    ): RedirectResponse {
        $caso = $casoService->crearCasoDesdeFormulario(
            usuario: $request->user(),
            datos: $request->validated()
        );

        return redirect()
            ->route('casos.show', $caso)
            ->with('success', 'Caso de seguimiento creado correctamente.');
    }

    public function show(
        CasoSeguimiento $casoSeguimiento,
        Request $request,
        CasoSeguimientoService $casoService
    ): View {
        $casoService->validarAccesoTutor($casoSeguimiento, $request->user());

        $casoSeguimiento->load([
            'periodoEvaluacion.ciclo',
            'seccion.materia',
            'seccion.horarios',
            'estudiante',
            'tutor',
            'causa',
            'gestiones.registradoPor',
        ]);

        return view('tutor.casos.show', [
            'caso' => $casoSeguimiento,
        ]);
    }

    public function edit(CasoSeguimiento $casoSeguimiento): RedirectResponse
    {
        return redirect()->route('casos.show', $casoSeguimiento);
    }

    public function update(): RedirectResponse
    {
        return redirect()
            ->route('casos.index')
            ->with('error', 'La edición del caso se implementará en el módulo de gestiones.');
    }

    public function destroy(): RedirectResponse
    {
        return redirect()
            ->route('casos.index')
            ->with('error', 'Los casos no se eliminan para conservar trazabilidad.');
    }
}
