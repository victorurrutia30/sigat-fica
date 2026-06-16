<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodoEvaluacionRequest;
use App\Models\Ciclo;
use App\Models\PeriodoEvaluacion;
use App\Services\PeriodoEvaluacionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class PeriodoEvaluacionController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->string('busqueda')->toString();
        $cicloId = $request->integer('ciclo_id') ?: null;
        $estado = $request->string('estado')->toString();

        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        $periodos = PeriodoEvaluacion::query()
            ->with('ciclo')
            ->withCount(['casosSeguimiento', 'consolidados'])
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%");
            })
            ->when($cicloId, function ($query) use ($cicloId) {
                $query->where('ciclo_id', $cicloId);
            })
            ->when($estado === 'activos', function ($query) {
                $query->where('activo', true);
            })
            ->when($estado === 'inactivos', function ($query) {
                $query->where('activo', false);
            })
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.periodos.index', compact(
            'periodos',
            'ciclos',
            'busqueda',
            'cicloId',
            'estado'
        ));
    }

    public function create(): View
    {
        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        $nombresPeriodo = PeriodoEvaluacion::nombresPermitidos();

        return view('coordinacion.periodos.create', compact(
            'ciclos',
            'nombresPeriodo'
        ));
    }

    public function store(
        PeriodoEvaluacionRequest $request,
        PeriodoEvaluacionService $periodoService
    ): RedirectResponse {
        try {
            $resultado = $periodoService->crear($request->validated());
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        $redirect = redirect()
            ->route('periodos.index')
            ->with('success', 'Periodo de evaluación creado correctamente.');

        if ($resultado['consolidados_creados'] === 0) {
            $redirect->with(
                'warning',
                'El periodo fue creado, pero no se generaron consolidados porque no hay tutores con asignaciones publicadas para ese ciclo.'
            );
        } else {
            $redirect->with(
                'info',
                "Consolidados generados: {$resultado['consolidados_creados']}."
            );
        }

        return $redirect;
    }

    public function show(PeriodoEvaluacion $periodoEvaluacion): RedirectResponse
    {
        return redirect()->route('periodos.edit', $periodoEvaluacion);
    }

    public function edit(PeriodoEvaluacion $periodoEvaluacion): View
    {
        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        $periodoEvaluacion->loadCount(['casosSeguimiento', 'consolidados']);

        return view('coordinacion.periodos.edit', [
            'periodo' => $periodoEvaluacion,
            'ciclos' => $ciclos,
            'nombresPeriodo' => PeriodoEvaluacion::nombresPermitidos(),
        ]);
    }

    public function update(
        PeriodoEvaluacionRequest $request,
        PeriodoEvaluacion $periodoEvaluacion,
        PeriodoEvaluacionService $periodoService
    ): RedirectResponse {
        try {
            $resultado = $periodoService->actualizar(
                periodo: $periodoEvaluacion,
                datos: $request->validated()
            );
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        $redirect = redirect()
            ->route('periodos.index')
            ->with('success', 'Periodo de evaluación actualizado correctamente.');

        if ($resultado['consolidados_creados'] > 0) {
            $redirect->with(
                'info',
                "Consolidados adicionales generados: {$resultado['consolidados_creados']}."
            );
        }

        return $redirect;
    }

    public function destroy(
        PeriodoEvaluacion $periodoEvaluacion,
        PeriodoEvaluacionService $periodoService
    ): RedirectResponse {
        if (! $periodoEvaluacion->activo) {
            return redirect()
                ->route('periodos.index')
                ->with('error', 'El periodo ya se encuentra inactivo.');
        }

        try {
            $periodoService->desactivar($periodoEvaluacion);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('periodos.index')
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('periodos.index')
            ->with('success', 'Periodo de evaluación desactivado correctamente.');
    }
}
