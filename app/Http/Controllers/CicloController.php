<?php

namespace App\Http\Controllers;

use App\Http\Requests\CicloRequest;
use App\Models\Ciclo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CicloController extends Controller
{
    public function index(): View
    {
        $busqueda = request('busqueda');

        $ciclos = Ciclo::query()
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('anio', 'like', "%{$busqueda}%");
            })
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.ciclos.index', compact('ciclos', 'busqueda'));
    }

    public function create(): View
    {
        return view('coordinacion.ciclos.create');
    }

    public function store(CicloRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $datos = $request->validated();
            $datos['activo'] = $request->boolean('activo');

            if ($datos['activo']) {
                Ciclo::query()->update(['activo' => false]);
            }

            Ciclo::create($datos);
        });

        return redirect()
            ->route('ciclos.index')
            ->with('success', 'Ciclo académico creado correctamente.');
    }

    public function show(Ciclo $ciclo): View
    {
        $ciclo->loadCount([
            'secciones',
            'propuestasAsignacion',
            'periodosEvaluacion',
        ]);

        return view('coordinacion.ciclos.show', compact('ciclo'));
    }

    public function edit(Ciclo $ciclo): View
    {
        return view('coordinacion.ciclos.edit', compact('ciclo'));
    }

    public function update(CicloRequest $request, Ciclo $ciclo): RedirectResponse
    {
        DB::transaction(function () use ($request, $ciclo) {
            $datos = $request->validated();
            $datos['activo'] = $request->boolean('activo');

            if ($datos['activo']) {
                Ciclo::query()
                    ->whereKeyNot($ciclo->id)
                    ->update(['activo' => false]);
            }

            $ciclo->update($datos);
        });

        return redirect()
            ->route('ciclos.index')
            ->with('success', 'Ciclo académico actualizado correctamente.');
    }

    public function destroy(Ciclo $ciclo): RedirectResponse
    {
        if (! $ciclo->activo) {
            return redirect()
                ->route('ciclos.index')
                ->with('error', 'El ciclo ya se encuentra inactivo.');
        }

        $ciclo->update(['activo' => false]);

        return redirect()
            ->route('ciclos.index')
            ->with('success', 'Ciclo académico desactivado correctamente.');
    }
}
