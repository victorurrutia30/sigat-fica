<?php

namespace App\Http\Controllers;

use App\Http\Requests\TutorRequest;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function index(): View
    {
        $busqueda = request('busqueda');
        $estado = request('estado');

        $tutores = Tutor::withTrashed()
            ->with('usuario')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('codigo_empleado', 'like', "%{$busqueda}%")
                        ->orWhere('nombre_completo', 'like', "%{$busqueda}%")
                        ->orWhere('correo_institucional', 'like', "%{$busqueda}%")
                        ->orWhere('departamento', 'like', "%{$busqueda}%");
                });
            })
            ->when($estado === 'activos', function ($query) {
                $query->where('activo', true)
                    ->whereNull('deleted_at');
            })
            ->when($estado === 'inactivos', function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('activo', false)
                        ->orWhereNotNull('deleted_at');
                });
            })
            ->orderBy('nombre_completo')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.tutores.index', compact('tutores', 'busqueda', 'estado'));
    }

    public function create(): View
    {
        $usuariosDisponibles = $this->usuariosDisponibles();

        return view('coordinacion.tutores.create', compact('usuariosDisponibles'));
    }

    public function store(TutorRequest $request): RedirectResponse
    {
        $datos = $request->validated();

        $datos['usuario_id'] = $datos['usuario_id'] ?? null;
        $datos['tiempo_completo'] = true;
        $datos['activo'] = $request->boolean('activo');

        Tutor::create($datos);

        return redirect()
            ->route('tutores.index')
            ->with('success', 'Tutor creado correctamente.');
    }

    public function show(Tutor $tutor): View
    {
        $tutor->load('usuario')
            ->loadCount([
                'itemsPropuesta',
                'casosSeguimiento',
                'consolidados',
            ]);

        return view('coordinacion.tutores.show', compact('tutor'));
    }

    public function edit(Tutor $tutor): View
    {
        $usuariosDisponibles = $this->usuariosDisponibles($tutor);

        return view('coordinacion.tutores.edit', compact('tutor', 'usuariosDisponibles'));
    }

    public function update(TutorRequest $request, Tutor $tutor): RedirectResponse
    {
        $datos = $request->validated();

        $datos['usuario_id'] = $datos['usuario_id'] ?? null;
        $datos['tiempo_completo'] = true;
        $datos['activo'] = $request->boolean('activo');

        $tutor->update($datos);

        return redirect()
            ->route('tutores.index')
            ->with('success', 'Tutor actualizado correctamente.');
    }

    public function destroy(Tutor $tutor): RedirectResponse
    {
        if ($tutor->trashed()) {
            return redirect()
                ->route('tutores.index')
                ->with('error', 'El tutor ya se encuentra desactivado.');
        }

        $tutor->update(['activo' => false]);
        $tutor->delete();

        return redirect()
            ->route('tutores.index')
            ->with('success', 'Tutor desactivado correctamente.');
    }

    private function usuariosDisponibles(?Tutor $tutor = null): Collection
    {
        $usuariosOcupados = Tutor::withTrashed()
            ->whereNotNull('usuario_id')
            ->when($tutor, function ($query) use ($tutor) {
                $query->whereKeyNot($tutor->id);
            })
            ->pluck('usuario_id');

        return User::query()
            ->where('rol', 'tutor')
            ->where('activo', true)
            ->whereNotIn('id', $usuariosOcupados)
            ->orderBy('nombre')
            ->get();
    }
}
