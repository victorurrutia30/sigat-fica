<?php

namespace App\Http\Controllers;

use App\Services\ConsolidadoService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(ConsolidadoService $consolidadoService): View
    {
        $periodoActivo = $consolidadoService->obtenerPeriodoActivoOpcional();

        $metricasCumplimiento = $consolidadoService->metricasParaCoordinacion(
            $periodoActivo?->id
        );

        return view('dashboard', compact(
            'periodoActivo',
            'metricasCumplimiento'
        ));
    }
}
