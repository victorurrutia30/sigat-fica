<?php

namespace App\Services;

use App\Models\CasoSeguimiento;
use App\Models\Causa;
use App\Models\Estudiante;
use App\Models\ItemPropuesta;
use App\Models\NominaSeccion;
use App\Models\PeriodoEvaluacion;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class CasoSeguimientoService
{
    public function obtenerPeriodoActivo(): PeriodoEvaluacion
    {
        $periodo = PeriodoEvaluacion::query()
            ->with('ciclo')
            ->where('activo', true)
            ->whereHas('ciclo', function ($query) {
                $query->where('activo', true);
            })
            ->first();

        if (! $periodo) {
            throw ValidationException::withMessages([
                'periodo' => 'No hay un periodo de evaluación activo para el ciclo académico activo.',
            ]);
        }

        return $periodo;
    }

    public function obtenerTutorDelUsuario(User $usuario): Tutor
    {
        $tutor = Tutor::query()
            ->where('usuario_id', $usuario->id)
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->first();

        if (! $tutor) {
            throw ValidationException::withMessages([
                'tutor' => 'El usuario autenticado no está vinculado a un tutor activo.',
            ]);
        }

        return $tutor;
    }

    public function seccionesAsignadasParaTutor(
        Tutor $tutor,
        PeriodoEvaluacion $periodo
    ): Collection {
        return ItemPropuesta::query()
            ->with([
                'seccion.materia',
                'seccion.horarios',
            ])
            ->where('tutor_id', $tutor->id)
            ->whereHas('propuestaAsignacion', function ($query) use ($periodo) {
                $query->where('ciclo_id', $periodo->ciclo_id)
                    ->where('publicado', true);
            })
            ->get()
            ->pluck('seccion')
            ->filter()
            ->sortBy(function ($seccion) {
                return sprintf(
                    '%s-%s',
                    $seccion->materia?->nombre ?? '',
                    str_pad((string) $seccion->numero_seccion, 5, '0', STR_PAD_LEFT)
                );
            })
            ->values();
    }

    public function casosDelTutorEnPeriodo(
        Tutor $tutor,
        PeriodoEvaluacion $periodo
    ): Collection {
        return CasoSeguimiento::query()
            ->with([
                'periodoEvaluacion',
                'seccion.materia',
                'estudiante',
                'tutor',
                'causa',
                'gestiones',
            ])
            ->where('tutor_id', $tutor->id)
            ->where('periodo_evaluacion_id', $periodo->id)
            ->orderBy('cerrado')
            ->orderByDesc('created_at')
            ->get();
    }

    public function crearCasoDesdeFormulario(User $usuario, array $datos): CasoSeguimiento
    {
        return DB::transaction(function () use ($usuario, $datos) {
            $periodo = $this->obtenerPeriodoActivo();
            $tutor = $this->obtenerTutorDelUsuario($usuario);

            $seccionesAsignadas = $this->seccionesAsignadasParaTutor($tutor, $periodo);
            $seccionId = (int) $datos['seccion_id'];

            if (! $seccionesAsignadas->pluck('id')->contains($seccionId)) {
                throw ValidationException::withMessages([
                    'seccion_id' => 'La sección seleccionada no está asignada al tutor en una propuesta publicada del ciclo activo.',
                ]);
            }

            $estudiante = Estudiante::updateOrCreate(
                [
                    'carne' => strtoupper(trim($datos['carne'])),
                ],
                [
                    'nombre_completo' => trim($datos['nombre_completo']),
                    'correo' => $datos['correo'] ?? null,
                    'carrera' => $datos['carrera'] ?? null,
                ]
            );

            NominaSeccion::firstOrCreate(
                [
                    'seccion_id' => $seccionId,
                    'estudiante_id' => $estudiante->id,
                ],
                [
                    'fecha_registro' => now()->toDateString(),
                ]
            );

            $casoExistente = CasoSeguimiento::query()
                ->where('periodo_evaluacion_id', $periodo->id)
                ->where('seccion_id', $seccionId)
                ->where('estudiante_id', $estudiante->id)
                ->first();

            if ($casoExistente) {
                throw ValidationException::withMessages([
                    'carne' => 'Ya existe un caso para este estudiante en la sección y periodo seleccionados.',
                ]);
            }

            return CasoSeguimiento::create([
                'periodo_evaluacion_id' => $periodo->id,
                'seccion_id' => $seccionId,
                'estudiante_id' => $estudiante->id,
                'tutor_id' => $tutor->id,
                'causa_id' => null,
                'resultado_final' => null,
                'cerrado' => false,
                'cerrado_en' => null,
                'registrado_por' => $usuario->id,
            ]);
        });
    }

    public function cerrarCaso(
        CasoSeguimiento $caso,
        User $usuario,
        array $datos
    ): CasoSeguimiento {
        return DB::transaction(function () use ($caso, $usuario, $datos) {
            $this->validarAccesoTutor($caso, $usuario);

            if ($caso->cerrado) {
                throw ValidationException::withMessages([
                    'caso' => 'Este caso ya se encuentra cerrado.',
                ]);
            }

            $gestionesRegistradas = $caso->gestiones()->count();

            if ($gestionesRegistradas === 0) {
                throw ValidationException::withMessages([
                    'gestiones' => 'Debe registrar al menos una gestión antes de cerrar el caso.',
                ]);
            }

            $causaActiva = Causa::query()
                ->whereKey($datos['causa_id'])
                ->where('activo', true)
                ->exists();

            if (! $causaActiva) {
                throw ValidationException::withMessages([
                    'causa_id' => 'La causa seleccionada no existe o está inactiva.',
                ]);
            }

            $resultadoFinal = match ($datos['resultado_consolidado']) {
                'rc', 'rm' => 'retiro',
                'abm', 'abc' => 'abandono',
            };

            $caso->update([
                'causa_id' => $datos['causa_id'],
                'resultado_final' => $resultadoFinal,
                'detalle_inasistencia' => $datos['detalle_inasistencia'],
                'resultado_consolidado' => $datos['resultado_consolidado'],
                'matricula' => (bool) $datos['matricula'],
                'cuota_cancelada' => $datos['cuota_cancelada'] ?? null,
                'cerrado' => true,
                'cerrado_en' => now(),
            ]);

            return $caso->fresh([
                'periodoEvaluacion.ciclo',
                'seccion.materia',
                'estudiante',
                'tutor',
                'causa',
                'gestiones',
            ]);
        });
    }

    public function validarAccesoTutor(CasoSeguimiento $caso, User $usuario): void
    {
        $tutor = $this->obtenerTutorDelUsuario($usuario);

        if ((int) $caso->tutor_id !== (int) $tutor->id) {
            abort(403, 'No tienes permiso para acceder a este caso.');
        }
    }
}
