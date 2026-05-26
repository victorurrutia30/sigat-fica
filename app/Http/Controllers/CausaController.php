<?php

namespace App\Http\Controllers;

use App\Http\Requests\CausaRequest;
use App\Models\Causa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CausaController extends Controller
{
    public function index(Request $request): View
    {
        $busqueda = $request->string('busqueda')->toString();
        $estado = $request->string('estado')->toString();

        $causas = Causa::query()
            ->withCount('casosSeguimiento')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            })
            ->when($estado === 'activas', function ($query) {
                $query->where('activo', true);
            })
            ->when($estado === 'inactivas', function ($query) {
                $query->where('activo', false);
            })
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.causas.index', compact(
            'causas',
            'busqueda',
            'estado'
        ));
    }

    public function create(): View
    {
        return view('coordinacion.causas.create');
    }

    public function store(CausaRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

        Causa::create($datos);

        return redirect()
            ->route('causas.index')
            ->with('success', 'Causa creada correctamente.');
    }

    public function show(Causa $causa): RedirectResponse
    {
        return redirect()->route('causas.edit', $causa);
    }

    public function edit(Causa $causa): View
    {
        $causa->loadCount('casosSeguimiento');

        return view('coordinacion.causas.edit', compact('causa'));
    }

    public function update(CausaRequest $request, Causa $causa): RedirectResponse
    {
        $datos = $request->validated();
        $datos['activo'] = $request->boolean('activo');

        $causa->update($datos);

        return redirect()
            ->route('causas.index')
            ->with('success', 'Causa actualizada correctamente.');
    }

    public function destroy(Causa $causa): RedirectResponse
    {
        if (! $causa->activo) {
            return redirect()
                ->route('causas.index')
                ->with('error', 'La causa ya se encuentra inactiva.');
        }

        $causa->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('causas.index')
            ->with('success', 'Causa desactivada correctamente. El historial asociado se conserva.');
    }
}
