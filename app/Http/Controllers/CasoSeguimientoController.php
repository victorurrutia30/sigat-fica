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
use App\Http\Requests\CasoCierreRequest;
use App\Models\Causa;

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
            $casoService->validarConsolidadoEditable($tutor, $periodo);
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

        $estadoConsolidado = $casoService->estadoConsolidadoParaCaso(
            caso: $casoSeguimiento,
            usuario: $request->user()
        );

        return view('tutor.casos.show', [
            'caso' => $casoSeguimiento,
            'estadoConsolidado' => $estadoConsolidado,
            'puedeModificarCaso' => $estadoConsolidado !== 'entregado',
        ]);
    }

    public function cierre(
        CasoSeguimiento $casoSeguimiento,
        Request $request,
        CasoSeguimientoService $casoService
    ): View|RedirectResponse {
        $casoService->validarAccesoTutor($casoSeguimiento, $request->user());

        $casoSeguimiento->loadMissing('periodoEvaluacion');

        $tutor = $casoService->obtenerTutorDelUsuario($request->user());
        $casoService->validarConsolidadoEditable($tutor, $casoSeguimiento->periodoEvaluacion);

        $casoSeguimiento->load([
            'periodoEvaluacion.ciclo',
            'seccion.materia',
            'estudiante',
            'tutor',
            'causa',
            'gestiones',
        ]);

        if ($casoSeguimiento->cerrado) {
            return redirect()
                ->route('casos.show', $casoSeguimiento)
                ->with('error', 'Este caso ya se encuentra cerrado.');
        }

        if ($casoSeguimiento->gestiones->isEmpty()) {
            return redirect()
                ->route('casos.show', $casoSeguimiento)
                ->with('error', 'Debe registrar al menos una gestión antes de cerrar el caso.');
        }

        $causas = Causa::query()
            ->where('activo', true)
            ->orderBy('nombre')
            ->get();

        if ($causas->isEmpty()) {
            return redirect()
                ->route('casos.show', $casoSeguimiento)
                ->with('error', 'No hay causas activas disponibles. Solicita a Coordinación registrar al menos una causa.');
        }

        return view('tutor.casos.cierre', [
            'caso' => $casoSeguimiento,
            'causas' => $causas,
        ]);
    }

    public function cerrar(
        CasoCierreRequest $request,
        CasoSeguimiento $casoSeguimiento,
        CasoSeguimientoService $casoService
    ): RedirectResponse {
        $casoService->cerrarCaso(
            caso: $casoSeguimiento,
            usuario: $request->user(),
            datos: $request->validated()
        );

        return redirect()
            ->route('casos.show', $casoSeguimiento)
            ->with('success', 'Caso cerrado correctamente.');
    }


    public function reabrir(
        CasoSeguimiento $casoSeguimiento,
        Request $request,
        CasoSeguimientoService $casoService
    ): RedirectResponse {
        try {
            $casoService->reabrirCaso(
                caso: $casoSeguimiento,
                usuario: $request->user()
            );
        } catch (ValidationException $exception) {
            return redirect()
                ->route('casos.show', $casoSeguimiento)
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('casos.show', $casoSeguimiento)
            ->with('success', 'Caso reabierto correctamente. Registra la gestión correctiva y vuelve a cerrar el caso.');
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
