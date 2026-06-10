<?php

namespace App\Http\Controllers;

use App\Services\TableroCumplimientoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TableroCumplimientoController extends Controller
{
    public function index(
        Request $request,
        TableroCumplimientoService $tableroService
    ): View {
        $periodoActivo = $tableroService->obtenerPeriodoActivoOpcional();

        $periodoId = $request->integer('periodo_id') ?: $periodoActivo?->id;
        $estado = $request->string('estado')->toString();
        $busqueda = $request->string('busqueda')->toString();

        $periodos = $tableroService->periodosParaFiltro();

        $filtros = [
            'periodo_id' => $periodoId,
            'estado' => $estado ?: null,
            'busqueda' => $busqueda ?: null,
        ];

        $registros = $tableroService->registrosParaTablero($filtros);
        $metricas = $tableroService->metricas($filtros);

        return view('coordinacion.tablero.index', compact(
            'periodos',
            'periodoId',
            'estado',
            'busqueda',
            'registros',
            'metricas'
        ));
    }
}
