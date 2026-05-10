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
            ->orderBy('ciclo_plan')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.materias.index', compact('materias', 'busqueda', 'estado'));
    }

    public function create(): View
    {
        return view('coordinacion.materias.create');
    }

    public function store(MateriaRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

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
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

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
}
