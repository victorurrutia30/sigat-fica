<?php

namespace App\Http\Controllers;

use App\Http\Requests\ConsolidadoEntregaRequest;
use App\Services\ConsolidadoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Http\Requests\ConsolidadoObservacionRequest;
use App\Models\Consolidado;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Exports\ConsolidadoPeriodoInstitucionalExport;
use App\Models\PeriodoEvaluacion;


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

    public function coordinacionIndex(
        Request $request,
        ConsolidadoService $consolidadoService
    ): View {
        $periodoActivo = $consolidadoService->obtenerPeriodoActivoOpcional();

        $periodoId = $request->integer('periodo_id') ?: $periodoActivo?->id;
        $estado = $request->string('estado')->toString();
        $busqueda = $request->string('busqueda')->toString();

        $periodos = $consolidadoService->periodosParaFiltro();

        $consolidados = $consolidadoService->consolidadosParaCoordinacion([
            'periodo_id' => $periodoId,
            'estado' => $estado ?: null,
            'busqueda' => $busqueda ?: null,
        ]);

        $metricas = $consolidadoService->metricasParaCoordinacion($periodoId);

        return view('coordinacion.consolidados.index', compact(
            'periodos',
            'periodoId',
            'estado',
            'busqueda',
            'consolidados',
            'metricas'
        ));
    }

    public function coordinacionShow(
        Consolidado $consolidado,
        ConsolidadoService $consolidadoService
    ): View {
        $contexto = $consolidadoService->detalleParaCoordinacion($consolidado);

        return view('coordinacion.consolidados.show', $contexto);
    }

    public function exportarInstitucional(
        Request $request,
        Consolidado $consolidado
    ): BinaryFileResponse {
        if ($request->user()?->rol !== 'coordinacion') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $consolidado->load([
            'periodoEvaluacion.ciclo',
            'tutor',
        ]);

        $ciclo = Str::slug($consolidado->periodoEvaluacion?->ciclo?->nombre ?? 'ciclo');
        $periodo = Str::slug($consolidado->periodoEvaluacion?->nombre ?? 'periodo');
        $tutor = Str::slug($consolidado->tutor?->nombre_completo ?? 'tutor');

        $nombreArchivo = "consolidado-institucional-{$ciclo}-{$periodo}-{$tutor}-"
            . now()->format('Ymd-His')
            . '.xlsx';

        return Excel::download(
            new ConsolidadoInstitucionalExport($consolidado),
            $nombreArchivo
        );
    }

    public function exportarPeriodoInstitucional(
        Request $request,
        PeriodoEvaluacion $periodoEvaluacion
    ): BinaryFileResponse {
        if ($request->user()?->rol !== 'coordinacion') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $periodoEvaluacion->load('ciclo');

        $ciclo = Str::slug($periodoEvaluacion->ciclo?->nombre ?? 'ciclo');
        $periodo = Str::slug($periodoEvaluacion->nombre ?? 'periodo');

        $nombreArchivo = "consolidado-institucional-{$ciclo}-{$periodo}-"
            . now()->format('Ymd-His')
            . '.xlsx';

        return Excel::download(
            new ConsolidadoPeriodoInstitucionalExport($periodoEvaluacion),
            $nombreArchivo
        );
    }

    public function guardarObservacion(
        ConsolidadoObservacionRequest $request,
        Consolidado $consolidado,
        ConsolidadoService $consolidadoService
    ): RedirectResponse {
        $consolidadoService->guardarObservacionCoordinacion(
            consolidado: $consolidado,
            observacion: $request->validated('observaciones_coord')
        );

        return redirect()
            ->route('consolidados.show', $consolidado)
            ->with('success', 'Observación registrada correctamente.');
    }
}
