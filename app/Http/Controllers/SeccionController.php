<?php

namespace App\Http\Controllers;

use App\Http\Requests\SeccionRequest;
use App\Models\Ciclo;
use App\Models\Materia;
use App\Models\Seccion;
use App\Services\SeccionService;
use Illuminate\Http\RedirectResponse;
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

    public function create(Materia $materia): View|RedirectResponse
    {
        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        if ($ciclos->isEmpty()) {
            return redirect()
                ->route('ciclos.index')
                ->with('error', 'Debe existir al menos un ciclo académico antes de crear secciones.');
        }

        $cicloActivo = $ciclos->firstWhere('activo', true);

        $seccion = new Seccion([
            'materia_id' => $materia->id,
            'ciclo_id' => $cicloActivo?->id,
            'modalidad' => 'presencial',
            'requiere_tutor' => (bool) $materia->gestionada_por_coordinacion,
            'capacidad' => 35,
        ]);

        $seccion->setRelation('materia', $materia);
        $seccion->setRelation('ciclo', $cicloActivo);
        $seccion->setRelation('horarios', collect());
        $seccion->setRelation('itemsPropuesta', collect());

        $seccion->casos_seguimiento_count = 0;
        $seccion->items_propuesta_count = 0;
        $seccion->nominas_seccion_count = 0;

        return view('coordinacion.secciones.create', compact(
            'materia',
            'seccion',
            'ciclos'
        ));
    }

    public function store(
        SeccionRequest $request,
        Materia $materia,
        SeccionService $seccionService
    ): RedirectResponse {
        $seccion = $seccionService->crear(
            materia: $materia,
            datos: $request->validated(),
            usuarioId: $request->user()->id
        );

        return redirect()
            ->route('materias.secciones.index', $seccion->materia)
            ->with('success', 'Sección creada correctamente.');
    }

    public function edit(Seccion $seccion): View
    {
        $seccion->load([
            'materia',
            'ciclo',
            'horarios',
            'itemsPropuesta.tutor',
            'itemsPropuesta.propuestaAsignacion',
        ]);

        $seccion->loadCount([
            'casosSeguimiento',
            'itemsPropuesta',
            'nominasSeccion',
        ]);

        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        return view('coordinacion.secciones.edit', compact('seccion', 'ciclos'));
    }

    public function update(
        SeccionRequest $request,
        Seccion $seccion,
        SeccionService $seccionService
    ): RedirectResponse {
        $seccionActualizada = $seccionService->actualizar(
            seccion: $seccion,
            datos: $request->validated(),
            usuarioId: $request->user()->id
        );

        return redirect()
            ->route('materias.secciones.index', $seccionActualizada->materia_id)
            ->with('success', 'Sección actualizada correctamente.');
    }
}
