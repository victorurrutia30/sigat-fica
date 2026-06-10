<?php

namespace App\Services;

use App\Models\Consolidado;
use App\Models\PeriodoEvaluacion;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TableroCumplimientoService
{
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

    public function periodosParaFiltro(): Collection
    {
        return PeriodoEvaluacion::query()
            ->with('ciclo')
            ->orderByDesc('activo')
            ->orderByDesc('fecha_inicio')
            ->get();
    }

    public function registrosParaTablero(array $filtros): LengthAwarePaginator
    {
        $periodoId = $filtros['periodo_id'] ?? null;
        $estado = $filtros['estado'] ?? null;
        $busqueda = $filtros['busqueda'] ?? null;

        $registros = $this->consultaBase()
            ->when($periodoId, function ($query) use ($periodoId) {
                $query->where('periodo_evaluacion_id', $periodoId);
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

        $this->agregarEstadoCumplimiento($registros->getCollection());

        if ($estado) {
            $filtrados = $registros->getCollection()
                ->filter(fn($registro) => $registro->estado_cumplimiento === $estado)
                ->values();

            $registros->setCollection($filtrados);
        }

        return $registros;
    }

    public function metricas(array $filtros): array
    {
        $periodoId = $filtros['periodo_id'] ?? null;
        $busqueda = $filtros['busqueda'] ?? null;

        $registros = $this->consultaBase()
            ->when($periodoId, function ($query) use ($periodoId) {
                $query->where('periodo_evaluacion_id', $periodoId);
            })
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->whereHas('tutor', function ($subquery) use ($busqueda) {
                    $subquery->where('nombre_completo', 'like', "%{$busqueda}%")
                        ->orWhere('codigo_empleado', 'like', "%{$busqueda}%")
                        ->orWhere('correo_institucional', 'like', "%{$busqueda}%");
                });
            })
            ->get();

        $this->agregarEstadoCumplimiento($registros);

        return [
            'total' => $registros->count(),
            'pendientes' => $registros->where('estado_cumplimiento', 'pendiente')->count(),
            'en_progreso' => $registros->where('estado_cumplimiento', 'en_progreso')->count(),
            'entregados' => $registros->where('estado_cumplimiento', 'entregado')->count(),
            'con_observaciones' => $registros->where('estado_cumplimiento', 'con_observaciones')->count(),
            'atrasados' => $registros->where('estado_cumplimiento', 'atrasado')->count(),
        ];
    }

    private function consultaBase(): Builder
    {
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
            ->selectSub(function ($query) {
                $query->from('casos_seguimiento')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('casos_seguimiento.periodo_evaluacion_id', 'consolidados.periodo_evaluacion_id')
                    ->whereColumn('casos_seguimiento.tutor_id', 'consolidados.tutor_id')
                    ->where(function ($subquery) {
                        $subquery->where('cerrado', false)
                            ->orWhereNull('causa_id')
                            ->orWhereNull('detalle_inasistencia')
                            ->orWhere('detalle_inasistencia', '')
                            ->orWhereNull('matricula')
                            ->orWhereNotExists(function ($gestionesQuery) {
                                $gestionesQuery->selectRaw('1')
                                    ->from('gestiones_caso')
                                    ->whereColumn('gestiones_caso.caso_seguimiento_id', 'casos_seguimiento.id');
                            });
                    });
            }, 'casos_incompletos_count')
            ->with([
                'periodoEvaluacion.ciclo',
                'tutor',
            ]);
    }

    private function agregarEstadoCumplimiento(Collection $registros): void
    {
        $hoy = now()->startOfDay();

        $registros->each(function (Consolidado $consolidado) use ($hoy) {
            $periodo = $consolidado->periodoEvaluacion;

            $estaAtrasado = $periodo
                && $periodo->fecha_limite_consolidado
                && $periodo->fecha_limite_consolidado->copy()->startOfDay()->lt($hoy)
                && $consolidado->estado_entrega !== 'entregado';

            $totalCasos = (int) ($consolidado->casos_total_count ?? 0);

            $estado = match (true) {
                $estaAtrasado => 'atrasado',
                $consolidado->estado_entrega === 'entregado' => 'entregado',
                $consolidado->estado_entrega === 'con_observaciones' => 'con_observaciones',
                $totalCasos > 0 => 'en_progreso',
                default => 'pendiente',
            };

            $consolidado->setAttribute('estado_cumplimiento', $estado);
            $consolidado->setAttribute('casos_pendientes_count', max(
                0,
                $totalCasos - (int) ($consolidado->casos_cerrados_count ?? 0)
            ));
        });
    }
}
