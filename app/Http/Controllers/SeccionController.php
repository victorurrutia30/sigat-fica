<?php

namespace App\Http\Controllers;

use App\Models\Ciclo;
use App\Models\Materia;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeccionController extends Controller
{
    public function index(Materia $materia, Request $request): View
    {
        $cicloActivo = Ciclo::query()
            ->where('activo', true)
            ->first();

        $cicloId = $request->has('ciclo_id')
            ? ($request->integer('ciclo_id') ?: null)
            : $cicloActivo?->id;

        $modalidad = $request->string('modalidad')->toString();
        $requiereTutor = $request->string('requiere_tutor')->toString();
        $busqueda = $request->string('busqueda')->toString();

        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        $secciones = $materia->secciones()
            ->with([
                'ciclo',
                'horarios',
                'itemsPropuesta.tutor',
                'itemsPropuesta.propuestaAsignacion',
            ])
            ->withCount([
                'casosSeguimiento',
                'itemsPropuesta',
                'nominasSeccion',
            ])
            ->when($cicloId, function ($query) use ($cicloId) {
                $query->where('ciclo_id', $cicloId);
            })
            ->when($modalidad, function ($query) use ($modalidad) {
                $query->where('modalidad', $modalidad);
            })
            ->when($requiereTutor === 'si', function ($query) {
                $query->where('requiere_tutor', true);
            })
            ->when($requiereTutor === 'no', function ($query) {
                $query->where('requiere_tutor', false);
            })
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('numero_seccion', 'like', "%{$busqueda}%")
                        ->orWhere('aula', 'like', "%{$busqueda}%")
                        ->orWhere('nombre_titular', 'like', "%{$busqueda}%")
                        ->orWhere('correo_titular', 'like', "%{$busqueda}%")
                        ->orWhere('codigo_docente_titular', 'like', "%{$busqueda}%");
                });
            })
            ->join('ciclos', 'ciclos.id', '=', 'secciones.ciclo_id')
            ->select('secciones.*')
            ->orderByDesc('ciclos.activo')
            ->orderByDesc('ciclos.anio')
            ->orderByDesc('ciclos.periodo')
            ->orderBy('secciones.numero_seccion')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.secciones.index', compact(
            'materia',
            'secciones',
            'ciclos',
            'cicloId',
            'modalidad',
            'requiereTutor',
            'busqueda'
        ));
    }
}
