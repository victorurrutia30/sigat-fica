<?php

namespace App\Http\Controllers;

use App\Http\Requests\MateriaRequest;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(): View
    {
        $busqueda = request('busqueda');
        $estado = request('estado');
        $gestion = request('gestion');
        $revision = request('revision');

        $materias = Materia::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('codigo', 'like', "%{$busqueda}%")
                        ->orWhere('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('departamento', 'like', "%{$busqueda}%");
                });
            })
            ->when($estado === 'activas', function ($query) {
                $query->where('activo', true);
            })
            ->when($estado === 'inactivas', function ($query) {
                $query->where('activo', false);
            })
            ->when($gestion === 'gestionadas', function ($query) {
                $query->where('gestionada_por_coordinacion', true);
            })
            ->when($gestion === 'no_gestionadas', function ($query) {
                $query->where('gestionada_por_coordinacion', false);
            })
            ->when($revision === 'pendientes', function ($query) {
                $query->where('requiere_revision', true);
            })
            ->when($revision === 'completas', function ($query) {
                $query->where('requiere_revision', false);
            })
            ->orderByDesc('gestionada_por_coordinacion')
            ->orderByDesc('requiere_revision')
            ->orderByRaw('ciclo_plan IS NULL')
            ->orderBy('ciclo_plan')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.materias.index', compact(
            'materias',
            'busqueda',
            'estado',
            'gestion',
            'revision'
        ));
    }

    public function create(): View
    {
        return view('coordinacion.materias.create');
    }

    public function store(MateriaRequest $request): RedirectResponse
    {
        $datos = $this->datosNormalizados($request);

        Materia::create($datos);

        return redirect()
            ->route('materias.index')
            ->with('success', 'Materia creada correctamente.');
    }

    public function show(Materia $materia): RedirectResponse
    {
        return redirect()->route('materias.edit', $materia);
    }

    public function edit(Materia $materia): View
    {
        return view('coordinacion.materias.edit', compact('materia'));
    }

    public function update(MateriaRequest $request, Materia $materia): RedirectResponse
    {
        $datos = $this->datosNormalizados($request);

        $materia->update($datos);

        return redirect()
            ->route('materias.index')
            ->with('success', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia): RedirectResponse
    {
        if (! $materia->activo) {
            return redirect()
                ->route('materias.index')
                ->with('error', 'La materia ya se encuentra inactiva.');
        }

        $materia->update(['activo' => false]);

        return redirect()
            ->route('materias.index')
            ->with('success', 'Materia desactivada correctamente.');
    }

    private function datosNormalizados(MateriaRequest $request): array
    {
        $datos = $request->validated();

        $datos['creditos'] = $datos['creditos'] ?? 3;
        $datos['ciclo_plan'] = $datos['ciclo_plan'] ?? null;
        $datos['activo'] = $request->boolean('activo');
        $datos['gestionada_por_coordinacion'] = $request->boolean('gestionada_por_coordinacion');

        $datos['requiere_revision'] = $this->determinarRevision(
            gestionadaPorCoordinacion: $datos['gestionada_por_coordinacion'],
            cicloPlan: $datos['ciclo_plan'],
        );

        return $datos;
    }

    private function determinarRevision(bool $gestionadaPorCoordinacion, ?int $cicloPlan): bool
    {
        if (! $gestionadaPorCoordinacion) {
            return false;
        }

        return $cicloPlan === null;
    }
}
