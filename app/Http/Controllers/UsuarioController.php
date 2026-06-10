<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;


class UsuarioController extends Controller
{
    public function index(): View
    {
        $busqueda = request('busqueda');
        $rol = request('rol');
        $estado = request('estado');

        $usuarios = User::query()
            ->with('tutor')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('nombre', 'like', "%{$busqueda}%")
                        ->orWhere('correo', 'like', "%{$busqueda}%");
                });
            })
            ->when($rol, function ($query) use ($rol) {
                $query->where('rol', $rol);
            })
            ->when($estado === 'activos', function ($query) {
                $query->where('activo', true);
            })
            ->when($estado === 'inactivos', function ($query) {
                $query->where('activo', false);
            })
            ->orderBy('rol')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('coordinacion.usuarios.index', compact(
            'usuarios',
            'busqueda',
            'rol',
            'estado'
        ));
    }

    public function create(): View
    {
        $tutoresDisponibles = $this->tutoresDisponibles();

        return view('coordinacion.usuarios.create', compact('tutoresDisponibles'));
    }

    public function store(UsuarioRequest $request): RedirectResponse
    {
        $datos = $this->datosNormalizados($request);
        $tutorId = $datos['tutor_id'];

        unset($datos['tutor_id']);

        DB::transaction(function () use ($datos, $tutorId) {
            $usuario = User::create($datos);

            $this->sincronizarTutor($usuario, $tutorId);
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario): View
    {
        $usuario->load('tutor');

        $tutoresDisponibles = $this->tutoresDisponibles($usuario);

        return view('coordinacion.usuarios.edit', compact('usuario', 'tutoresDisponibles'));
    }

    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        $datos = $this->datosNormalizados($request, esEdicion: true);
        $tutorId = $datos['tutor_id'];

        unset($datos['tutor_id']);

        DB::transaction(function () use ($usuario, $datos, $tutorId) {
            $usuario->update($datos);

            $this->sincronizarTutor($usuario, $tutorId);
        });

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $usuario): RedirectResponse
    {
        if ((int) Auth::id() === (int) $usuario->id) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        if (! $usuario->activo) {
            return redirect()
                ->route('usuarios.index')
                ->with('error', 'El usuario ya se encuentra inactivo.');
        }

        if ($usuario->rol === 'coordinacion') {
            $coordinacionesActivas = User::query()
                ->where('rol', 'coordinacion')
                ->where('activo', true)
                ->count();

            if ($coordinacionesActivas <= 1) {
                return redirect()
                    ->route('usuarios.index')
                    ->with('error', 'Debe existir al menos una cuenta activa de Coordinación.');
            }
        }

        $usuario->update([
            'activo' => false,
        ]);

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }

    private function datosNormalizados(UsuarioRequest $request, bool $esEdicion = false): array
    {
        $datos = $request->validated();

        $datos['activo'] = $request->boolean('activo');
        $datos['tutor_id'] = $datos['tutor_id'] ?? null;

        if ($esEdicion && blank($datos['password'] ?? null)) {
            unset($datos['password']);
        }

        return $datos;
    }

    private function tutoresDisponibles(?User $usuario = null)
    {
        return Tutor::query()
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($usuario) {
                $query->whereNull('usuario_id');

                if ($usuario) {
                    $query->orWhere('usuario_id', $usuario->id);
                }
            })
            ->orderBy('nombre_completo')
            ->get();
    }

    private function sincronizarTutor(User $usuario, ?int $tutorId): void
    {
        Tutor::query()
            ->where('usuario_id', $usuario->id)
            ->update([
                'usuario_id' => null,
            ]);

        if ($usuario->rol !== 'tutor' || ! $tutorId) {
            return;
        }

        Tutor::query()
            ->whereKey($tutorId)
            ->update([
                'usuario_id' => $usuario->id,
            ]);
    }
}
