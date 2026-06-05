<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CasoSeguimiento;
use App\Models\PeriodoEvaluacion;
use App\Models\Seccion;

class CasoSeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $tutor = auth()->user()->tutor;

        $periodoActivo = PeriodoEvaluacion::where('activo', true)->first();

        $casos = CasoSeguimiento::where('tutor_id', $tutor->id)
            ->with(['estudiante', 'seccion.materia', 'causa'])
            ->paginate(10);

        $totalCasos = $casos->total();

        $casosCerrados = CasoSeguimiento::where('tutor_id', $tutor->id)
            ->where('cerrado', true)
            ->count();

        $secciones = Seccion::whereHas('itemsPropuesta', function ($q) use ($tutor) {
            $q->where('tutor_id', $tutor->id);
        })->with('materia')->get();

        return view('tutor.casos.index', compact(
            'casos',
            'periodoActivo',
            'totalCasos',
            'casosCerrados',
            'secciones'
        ));
    }

    public function create()
{
    $tutor = auth()->user()->tutor;

    $secciones = Seccion::whereHas('itemsPropuesta', function ($q) use ($tutor) {
        $q->where('tutor_id', $tutor->id);
    })->with('materia')->get();

    $causas = \App\Models\Causa::where('activo', true)->get();

    return view('tutor.casos.create', compact('secciones', 'causas'));
}

    public function edit(CasoSeguimiento $caso)
{
    $tutor = auth()->user()->tutor;

    $secciones = Seccion::whereHas('itemsPropuesta', function ($q) use ($tutor) {
        $q->where('tutor_id', $tutor->id);
    })->with('materia')->get();

    $causas = \App\Models\Causa::where('activo', true)->get();

    return view('tutor.casos.edit', compact('caso', 'secciones', 'causas'));
}

}