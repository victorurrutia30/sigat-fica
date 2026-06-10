<?php

namespace App\Services;

use App\Models\Consolidado;
use App\Models\ItemPropuesta;
use App\Models\PeriodoEvaluacion;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeriodoEvaluacionService
{
    public function crear(array $datos): array
    {
        return DB::transaction(function () use ($datos) {
            $datos = $this->normalizarDatos($datos);

            if ($datos['activo']) {
                $this->validarCambioDePeriodoActivo($datos['ciclo_id']);
                $this->desactivarOtrosPeriodosDelCiclo($datos['ciclo_id']);
            }

            $periodo = PeriodoEvaluacion::create($datos);

            $consolidadosCreados = $this->generarConsolidadosParaPeriodo($periodo);

            return [
                'periodo' => $periodo,
                'consolidados_creados' => $consolidadosCreados,
            ];
        });
    }

    public function actualizar(PeriodoEvaluacion $periodo, array $datos): array
    {
        return DB::transaction(function () use ($periodo, $datos) {
            $datos = $this->normalizarDatos($datos);

            if ($periodo->activo && ! $datos['activo']) {
                $this->validarPeriodoSinTrabajoPendiente(
                    periodo: $periodo,
                    mensaje: 'No se puede desactivar este periodo porque todavía tiene consolidados pendientes o con observaciones.'
                );
            }

            if ($datos['activo']) {
                $this->validarCambioDePeriodoActivo(
                    cicloId: $datos['ciclo_id'],
                    periodoIgnoradoId: $periodo->id
                );

                $this->desactivarOtrosPeriodosDelCiclo(
                    cicloId: $datos['ciclo_id'],
                    periodoIgnoradoId: $periodo->id
                );
            }

            $periodo->update($datos);

            $consolidadosCreados = $this->generarConsolidadosParaPeriodo($periodo->fresh());

            return [
                'periodo' => $periodo->fresh(),
                'consolidados_creados' => $consolidadosCreados,
            ];
        });
    }

    public function desactivar(PeriodoEvaluacion $periodo): void
    {
        $this->validarPeriodoSinTrabajoPendiente(
            periodo: $periodo,
            mensaje: 'No se puede desactivar este periodo porque todavía tiene consolidados pendientes o con observaciones.'
        );

        $periodo->update([
            'activo' => false,
        ]);
    }

    public function generarConsolidadosParaPeriodo(PeriodoEvaluacion $periodo): int
    {
        $tutorIds = ItemPropuesta::query()
            ->whereHas('propuestaAsignacion', function ($query) use ($periodo) {
                $query->where('ciclo_id', $periodo->ciclo_id)
                    ->where('publicado', true);
            })
            ->pluck('tutor_id')
            ->unique()
            ->values();

        $creados = 0;

        foreach ($tutorIds as $tutorId) {
            $consolidado = Consolidado::firstOrCreate(
                [
                    'periodo_evaluacion_id' => $periodo->id,
                    'tutor_id' => $tutorId,
                ],
                [
                    'estado_entrega' => 'pendiente',
                    'sin_casos' => false,
                ]
            );

            if ($consolidado->wasRecentlyCreated) {
                $creados++;
            }
        }

        return $creados;
    }

    private function normalizarDatos(array $datos): array
    {
        return [
            'ciclo_id' => (int) $datos['ciclo_id'],
            'nombre' => trim($datos['nombre']),
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin'],
            'fecha_limite_consolidado' => $datos['fecha_limite_consolidado'],
            'activo' => (bool) ($datos['activo'] ?? false),
        ];
    }

    private function desactivarOtrosPeriodosDelCiclo(
        int $cicloId,
        ?int $periodoIgnoradoId = null
    ): void {
        PeriodoEvaluacion::query()
            ->where('ciclo_id', $cicloId)
            ->when($periodoIgnoradoId, function ($query) use ($periodoIgnoradoId) {
                $query->whereKeyNot($periodoIgnoradoId);
            })
            ->update([
                'activo' => false,
            ]);
    }

    private function validarCambioDePeriodoActivo(
        int $cicloId,
        ?int $periodoIgnoradoId = null
    ): void {
        $periodoActivoActual = PeriodoEvaluacion::query()
            ->where('ciclo_id', $cicloId)
            ->where('activo', true)
            ->when($periodoIgnoradoId, function ($query) use ($periodoIgnoradoId) {
                $query->whereKeyNot($periodoIgnoradoId);
            })
            ->first();

        if (! $periodoActivoActual) {
            return;
        }

        $this->validarPeriodoSinTrabajoPendiente(
            periodo: $periodoActivoActual,
            mensaje: 'No se puede activar otro periodo porque el periodo activo actual todavía tiene consolidados pendientes o con observaciones.'
        );
    }

    private function validarPeriodoSinTrabajoPendiente(
        PeriodoEvaluacion $periodo,
        string $mensaje
    ): void {
        $tieneTrabajoPendiente = Consolidado::query()
            ->where('periodo_evaluacion_id', $periodo->id)
            ->whereIn('estado_entrega', [
                'pendiente',
                'con_observaciones',
            ])
            ->exists();

        if ($tieneTrabajoPendiente) {
            throw ValidationException::withMessages([
                'activo' => $mensaje,
            ]);
        }
    }
}
