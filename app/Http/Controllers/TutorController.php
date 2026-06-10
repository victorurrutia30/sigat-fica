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
                        ->orWhere('departamento', 'like', "%{$busqueda}%")
                        ->orWhere('categoria_docente', 'like', "%{$busqueda}%");
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
        $datos = $this->datosNormalizados($request);

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
        $datos = $this->datosNormalizados($request);

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

    public function reactivar(int $tutor): RedirectResponse
    {
        $tutor = Tutor::withTrashed()->findOrFail($tutor);

        if (! $tutor->trashed() && $tutor->activo) {
            return redirect()
                ->route('tutores.index')
                ->with('error', 'El tutor ya se encuentra activo.');
        }

        if ($tutor->trashed()) {
            $tutor->restore();
        }

        $tutor->update([
            'activo' => true,
        ]);

        return redirect()
            ->route('tutores.index')
            ->with('success', 'Tutor reactivado correctamente.');
    }

    private function datosNormalizados(TutorRequest $request): array
    {
        $datos = $request->validated();

        $datos['usuario_id'] = $datos['usuario_id'] ?? null;
        $datos['tiempo_completo'] = $request->boolean('tiempo_completo');
        $datos['habilitado_para_tutorias'] = $request->boolean('habilitado_para_tutorias');
        $datos['es_excepcion_tutoria'] = $request->boolean('es_excepcion_tutoria');
        $datos['activo'] = $request->boolean('activo');
        $datos['origen_registro'] = $datos['origen_registro'] ?? 'manual';

        if ($datos['tiempo_completo']) {
            $datos['es_excepcion_tutoria'] = false;
            $datos['motivo_excepcion_tutoria'] = null;
        }

        if (! $datos['es_excepcion_tutoria']) {
            $datos['motivo_excepcion_tutoria'] = null;
        }

        return $datos;
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
