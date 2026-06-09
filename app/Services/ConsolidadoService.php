<?php

namespace App\Services;

use App\Models\CasoSeguimiento;
use App\Models\Consolidado;
use App\Models\PeriodoEvaluacion;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Ciclo;
use Illuminate\Pagination\LengthAwarePaginator;

class ConsolidadoService
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

    public function obtenerOCrearConsolidado(
        PeriodoEvaluacion $periodo,
        Tutor $tutor
    ): Consolidado {
        return Consolidado::firstOrCreate(
            [
                'periodo_evaluacion_id' => $periodo->id,
                'tutor_id' => $tutor->id,
            ],
            [
                'estado_entrega' => 'pendiente',
                'sin_casos' => false,
            ]
        );
    }

    public function casosDelTutorEnPeriodo(
        Tutor $tutor,
        PeriodoEvaluacion $periodo
    ): EloquentCollection {
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
            ->orderBy('seccion_id')
            ->orderByDesc('created_at')
            ->get();
    }

    public function diagnosticarCasos(EloquentCollection $casos): array
    {
        $detalleIncompletos = [];

        foreach ($casos as $caso) {
            $faltantes = [];

            if (! $caso->cerrado) {
                $faltantes[] = 'caso sin cerrar';
            }

            if (! $caso->causa_id) {
                $faltantes[] = 'causa';
            }

            if (! $caso->resultado_final) {
                $faltantes[] = 'resultado final';
            }

            if ($caso->gestiones->isEmpty()) {
                $faltantes[] = 'gestión registrada';
            }

            if (! empty($faltantes)) {
                $detalleIncompletos[] = [
                    'caso' => $caso,
                    'faltantes' => $faltantes,
                ];
            }
        }

        return [
            'total' => $casos->count(),
            'cerrados' => $casos->where('cerrado', true)->count(),
            'abiertos' => $casos->where('cerrado', false)->count(),
            'incompletos' => count($detalleIncompletos),
            'detalle_incompletos' => $detalleIncompletos,
        ];
    }

    public function contextoParaTutor(User $usuario): array
    {
        $periodo = $this->obtenerPeriodoActivo();
        $tutor = $this->obtenerTutorDelUsuario($usuario);
        $consolidado = $this->obtenerOCrearConsolidado($periodo, $tutor);
        $casos = $this->casosDelTutorEnPeriodo($tutor, $periodo);
        $diagnostico = $this->diagnosticarCasos($casos);

        return [
            'periodo' => $periodo,
            'tutor' => $tutor,
            'consolidado' => $consolidado,
            'casos' => $casos,
            'diagnostico' => $diagnostico,
        ];
    }

    public function entregar(User $usuario, bool $confirmarSinCasos): Consolidado
    {
        return DB::transaction(function () use ($usuario, $confirmarSinCasos) {
            $periodo = $this->obtenerPeriodoActivo();
            $tutor = $this->obtenerTutorDelUsuario($usuario);

            $consolidado = $this->obtenerOCrearConsolidado($periodo, $tutor);
            $casos = $this->casosDelTutorEnPeriodo($tutor, $periodo);
            $diagnostico = $this->diagnosticarCasos($casos);

            if ($consolidado->estado_entrega === 'entregado') {
                throw ValidationException::withMessages([
                    'consolidado' => 'Este consolidado ya fue entregado.',
                ]);
            }

            if ($diagnostico['total'] === 0 && ! $confirmarSinCasos) {
                throw ValidationException::withMessages([
                    'confirmar_sin_casos' => 'Debe confirmar explícitamente que no hubo estudiantes no evaluados.',
                ]);
            }

            if ($diagnostico['incompletos'] > 0) {
                throw ValidationException::withMessages([
                    'casos' => 'No se puede entregar el consolidado porque existen casos incompletos.',
                ]);
            }

            $consolidado->update([
                'estado_entrega' => 'entregado',
                'sin_casos' => $diagnostico['total'] === 0,
                'entregado_en' => now(),
                'entregado_por' => $usuario->id,
            ]);

            return $consolidado->fresh([
                'periodoEvaluacion',
                'tutor',
            ]);
        });
    }

    public function periodosParaFiltro()
    {
        return PeriodoEvaluacion::query()
            ->with('ciclo')
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    public function obtenerPeriodoActivoOpcional(): ?PeriodoEvaluacion
    {
        return PeriodoEvaluacion::query()
            ->with('ciclo')
            ->where('activo', true)
            ->whereHas('ciclo', function ($query) {
                $query->where('activo', true);
            })
            ->first();
    }

    public function consolidadosParaCoordinacion(array $filtros): LengthAwarePaginator
    {
        $periodoId = $filtros['periodo_id'] ?? null;
        $estado = $filtros['estado'] ?? null;
        $busqueda = $filtros['busqueda'] ?? null;

        return Consolidado::query()
            ->select('consolidados.*')
            ->selectSub(function ($query) {
                $query->from('casos_seguimiento')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('casos_seguimiento.periodo_evaluacion_id', 'consolidados.periodo_evaluacion_id')
                    ->whereColumn('casos_seguimiento.tutor_id', 'consolidados.tutor_id');
            }, 'casos_total_count')
            ->selectSub(function ($query) {
                $query->from('casos_seguimiento')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('casos_seguimiento.periodo_evaluacion_id', 'consolidados.periodo_evaluacion_id')
                    ->whereColumn('casos_seguimiento.tutor_id', 'consolidados.tutor_id')
                    ->where('cerrado', true);
            }, 'casos_cerrados_count')
            ->selectSub(function ($query) {
                $query->from('casos_seguimiento')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('casos_seguimiento.periodo_evaluacion_id', 'consolidados.periodo_evaluacion_id')
                    ->whereColumn('casos_seguimiento.tutor_id', 'consolidados.tutor_id')
                    ->where('cerrado', false);
            }, 'casos_abiertos_count')
            ->with([
                'periodoEvaluacion.ciclo',
                'tutor',
            ])
            ->when($periodoId, function ($query) use ($periodoId) {
                $query->where('periodo_evaluacion_id', $periodoId);
            })
            ->when($estado, function ($query) use ($estado) {
                $query->where('estado_entrega', $estado);
            })
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->whereHas('tutor', function ($subquery) use ($busqueda) {
                    $subquery->where('nombre_completo', 'like', "%{$busqueda}%")
                        ->orWhere('codigo_empleado', 'like', "%{$busqueda}%")
                        ->orWhere('correo_institucional', 'like', "%{$busqueda}%");
                });
            })
            ->orderByRaw("
            CASE estado_entrega
                WHEN 'pendiente' THEN 1
                WHEN 'con_observaciones' THEN 2
                WHEN 'entregado' THEN 3
                ELSE 4
            END
        ")
            ->orderByDesc('updated_at')
            ->paginate(10)
            ->withQueryString();
    }

    public function metricasParaCoordinacion(?int $periodoId = null): array
    {
        $consolidados = Consolidado::query()
            ->with('periodoEvaluacion')
            ->when($periodoId, function ($query) use ($periodoId) {
                $query->where('periodo_evaluacion_id', $periodoId);
            })
            ->get();

        $hoy = now()->startOfDay();

        return [
            'total' => $consolidados->count(),
            'pendientes' => $consolidados->where('estado_entrega', 'pendiente')->count(),
            'entregados' => $consolidados->where('estado_entrega', 'entregado')->count(),
            'con_observaciones' => $consolidados->where('estado_entrega', 'con_observaciones')->count(),
            'atrasados' => $consolidados
                ->filter(function ($consolidado) use ($hoy) {
                    return $consolidado->periodoEvaluacion
                        && $consolidado->periodoEvaluacion->fecha_limite_consolidado->startOfDay()->lt($hoy)
                        && $consolidado->estado_entrega !== 'entregado';
                })
                ->count(),
        ];
    }

    public function detalleParaCoordinacion(Consolidado $consolidado): array
    {
        $consolidado->load([
            'periodoEvaluacion.ciclo',
            'tutor',
        ]);

        $casos = CasoSeguimiento::query()
            ->with([
                'periodoEvaluacion',
                'seccion.materia',
                'estudiante',
                'tutor',
                'causa',
                'gestiones.registradoPor',
            ])
            ->where('periodo_evaluacion_id', $consolidado->periodo_evaluacion_id)
            ->where('tutor_id', $consolidado->tutor_id)
            ->orderBy('cerrado')
            ->orderByDesc('created_at')
            ->get();

        $diagnostico = $this->diagnosticarCasos($casos);

        return [
            'consolidado' => $consolidado,
            'casos' => $casos,
            'diagnostico' => $diagnostico,
        ];
    }

    public function guardarObservacionCoordinacion(
        Consolidado $consolidado,
        string $observacion
    ): Consolidado {
        $consolidado->forceFill([
            'observaciones_coord' => $observacion,
            'estado_entrega' => 'con_observaciones',
        ])->save();

        return $consolidado->fresh([
            'periodoEvaluacion.ciclo',
            'tutor',
        ]);
    }
}
