<?php

namespace App\Http\Controllers;

use App\Services\TableroCumplimientoService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(TableroCumplimientoService $tableroService): View
    {
        $periodoActivo = $tableroService->obtenerPeriodoActivoOpcional();

        $metricasCumplimiento = $tableroService->metricas([
            'periodo_id' => $periodoActivo?->id,
            'estado' => null,
            'busqueda' => null,
        ]);

        return view('dashboard', compact(
            'periodoActivo',
            'metricasCumplimiento'
        ));
    }
}
