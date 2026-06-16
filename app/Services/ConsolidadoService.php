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
use App\Models\ConfirmacionSeccionConsolidado;
use App\Models\ItemPropuesta;
use Illuminate\Support\Collection;

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

            if (! $caso->detalle_inasistencia) {
                $faltantes[] = 'detalle de inasistencia';
            }

            if (is_null($caso->matricula)) {
                $faltantes[] = 'matrícula';
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

    public function coberturaSecciones(
        Collection $secciones,
        EloquentCollection $casos,
        Consolidado $consolidado
    ): array {
        $confirmaciones = ConfirmacionSeccionConsolidado::query()
            ->with('confirmadoPor')
            ->where('consolidado_id', $consolidado->id)
            ->get()
            ->keyBy('seccion_id');

        $filas = $secciones->map(function ($seccion) use ($casos, $confirmaciones) {
            $casosSeccion = $casos->where('seccion_id', $seccion->id);
            $confirmacion = $confirmaciones->get($seccion->id);

            return [
                'seccion' => $seccion,
                'casos_total' => $casosSeccion->count(),
                'casos_cerrados' => $casosSeccion->where('cerrado', true)->count(),
                'confirmada_sin_casos' => (bool) $confirmacion,
                'confirmacion' => $confirmacion,
                'requiere_confirmacion' => $casosSeccion->count() === 0 && ! $confirmacion,
            ];
        })->values();

        return [
            'total_secciones' => $filas->count(),
            'con_casos' => $filas->where('casos_total', '>', 0)->count(),
            'sin_casos_confirmadas' => $filas
                ->where('casos_total', 0)
                ->where('confirmada_sin_casos', true)
                ->count(),
            'pendientes_confirmacion' => $filas
                ->where('requiere_confirmacion', true)
                ->count(),
            'detalle' => $filas,
        ];
    }

    private function registrarConfirmacionesSinCasos(
        Consolidado $consolidado,
        User $usuario,
        Collection $secciones,
        EloquentCollection $casos,
        array $seccionesSinCasos
    ): void {
        $seccionIdsAsignadas = $secciones->pluck('id')->map(fn($id) => (int) $id);
        $seccionIdsConCasos = $casos->pluck('seccion_id')->unique()->map(fn($id) => (int) $id);

        $seccionesSinCasos = collect($seccionesSinCasos)
            ->map(fn($id) => (int) $id)
            ->unique()
            ->values();

        $seccionesInvalidas = $seccionesSinCasos
            ->reject(fn($id) => $seccionIdsAsignadas->contains($id));

        if ($seccionesInvalidas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'secciones_sin_casos' => 'Una de las secciones confirmadas no pertenece a tus asignaciones publicadas.',
            ]);
        }

        $seccionesConCasosSeleccionadas = $seccionesSinCasos
            ->filter(fn($id) => $seccionIdsConCasos->contains($id));

        if ($seccionesConCasosSeleccionadas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'secciones_sin_casos' => 'No debes confirmar sin casos una sección que ya tiene casos registrados.',
            ]);
        }

        foreach ($seccionesSinCasos as $seccionId) {
            ConfirmacionSeccionConsolidado::updateOrCreate(
                [
                    'consolidado_id' => $consolidado->id,
                    'seccion_id' => $seccionId,
                ],
                [
                    'confirmado_por' => $usuario->id,
                    'confirmado_en' => now(),
                ]
            );
        }
    }

    public function contextoParaTutor(User $usuario): array
    {
        $periodo = $this->obtenerPeriodoActivo();
        $tutor = $this->obtenerTutorDelUsuario($usuario);
        $consolidado = $this->obtenerOCrearConsolidado($periodo, $tutor);
        $casos = $this->casosDelTutorEnPeriodo($tutor, $periodo);
        $diagnostico = $this->diagnosticarCasos($casos);
        $secciones = $this->seccionesAsignadasParaTutor($tutor, $periodo);
        $coberturaSecciones = $this->coberturaSecciones($secciones, $casos, $consolidado);

        return [
            'periodo' => $periodo,
            'tutor' => $tutor,
            'consolidado' => $consolidado,
            'casos' => $casos,
            'diagnostico' => $diagnostico,
            'secciones' => $secciones,
            'coberturaSecciones' => $coberturaSecciones,
        ];
    }

    public function entregar(
        User $usuario,
        bool $confirmarSinCasos,
        array $seccionesSinCasos = []
    ): Consolidado {
        return DB::transaction(function () use ($usuario, $confirmarSinCasos, $seccionesSinCasos) {
            $periodo = $this->obtenerPeriodoActivo();
            $tutor = $this->obtenerTutorDelUsuario($usuario);

            $consolidado = $this->obtenerOCrearConsolidado($periodo, $tutor);
            $casos = $this->casosDelTutorEnPeriodo($tutor, $periodo);
            $diagnostico = $this->diagnosticarCasos($casos);
            $secciones = $this->seccionesAsignadasParaTutor($tutor, $periodo);

            if ($consolidado->estado_entrega === 'entregado') {
                throw ValidationException::withMessages([
                    'consolidado' => 'Este consolidado ya fue entregado.',
                ]);
            }

            if ($secciones->isEmpty()) {
                throw ValidationException::withMessages([
                    'secciones' => 'No tienes secciones asignadas en una propuesta publicada del ciclo activo.',
                ]);
            }



            if ($diagnostico['incompletos'] > 0) {
                throw ValidationException::withMessages([
                    'casos' => 'No se puede entregar el consolidado porque existen casos incompletos.',
                ]);
            }

            $this->registrarConfirmacionesSinCasos(
                consolidado: $consolidado,
                usuario: $usuario,
                secciones: $secciones,
                casos: $casos,
                seccionesSinCasos: $seccionesSinCasos
            );

            $coberturaSecciones = $this->coberturaSecciones($secciones, $casos, $consolidado);

            if ($coberturaSecciones['pendientes_confirmacion'] > 0) {
                throw ValidationException::withMessages([
                    'secciones_sin_casos' => 'Debes confirmar las secciones sin casos antes de entregar el consolidado.',
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
        $atraso = (bool) ($filtros['atraso'] ?? false);

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
            ->when($atraso, function ($query) {
                $hoy = now()->toDateString();

                $query->where('estado_entrega', '!=', 'entregado')
                    ->whereHas('periodoEvaluacion', function ($periodoQuery) use ($hoy) {
                        $periodoQuery->whereDate('fecha_limite_consolidado', '<', $hoy);
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

        $secciones = $this->seccionesAsignadasParaTutor(
            tutor: $consolidado->tutor,
            periodo: $consolidado->periodoEvaluacion
        );

        $coberturaSecciones = $this->coberturaSecciones(
            secciones: $secciones,
            casos: $casos,
            consolidado: $consolidado
        );

        return [
            'consolidado' => $consolidado,
            'casos' => $casos,
            'diagnostico' => $diagnostico,
            'secciones' => $secciones,
            'coberturaSecciones' => $coberturaSecciones,
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
