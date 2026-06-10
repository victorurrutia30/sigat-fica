<?php

namespace App\Http\Controllers;

use App\Http\Requests\CicloRequest;
use App\Models\Ciclo;
use App\Services\CicloService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
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

    public function store(CicloRequest $request, CicloService $cicloService): RedirectResponse
    {
        try {
            $cicloService->crear($request->validated());
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

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

    public function update(
        CicloRequest $request,
        Ciclo $ciclo,
        CicloService $cicloService
    ): RedirectResponse {
        try {
            $cicloService->actualizar(
                ciclo: $ciclo,
                datos: $request->validated()
            );
        } catch (ValidationException $exception) {
            return back()
                ->withInput()
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('ciclos.index')
            ->with('success', 'Ciclo académico actualizado correctamente.');
    }

    public function destroy(Ciclo $ciclo, CicloService $cicloService): RedirectResponse
    {
        try {
            $cicloService->desactivar($ciclo);
        } catch (ValidationException $exception) {
            return redirect()
                ->route('ciclos.index')
                ->withErrors($exception->errors())
                ->with('error', collect($exception->errors())->flatten()->first());
        }

        return redirect()
            ->route('ciclos.index')
            ->with('success', 'Ciclo académico desactivado correctamente.');
    }
}
