<?php

namespace App\Services;

use App\Models\Ciclo;
use App\Models\Consolidado;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CicloService
{
    public function crear(array $datos): Ciclo
    {
        return DB::transaction(function () use ($datos) {
            $datos = $this->normalizarDatos($datos);

            if ($datos['activo']) {
                $this->validarCambioDeCicloActivo();
                Ciclo::query()->update(['activo' => false]);
            }

            return Ciclo::create($datos);
        });
    }

    public function actualizar(Ciclo $ciclo, array $datos): Ciclo
    {
        return DB::transaction(function () use ($ciclo, $datos) {
            $datos = $this->normalizarDatos($datos);

            if ($ciclo->activo && ! $datos['activo']) {
                $this->validarCicloSinTrabajoPendiente(
                    ciclo: $ciclo,
                    mensaje: 'No se puede desactivar este ciclo porque todavía tiene consolidados pendientes o con observaciones.'
                );
            }

            if (! $ciclo->activo && $datos['activo']) {
                $this->validarCambioDeCicloActivo($ciclo->id);

                Ciclo::query()
                    ->whereKeyNot($ciclo->id)
                    ->update(['activo' => false]);
            }

            if ($ciclo->activo && $datos['activo']) {
                Ciclo::query()
                    ->whereKeyNot($ciclo->id)
                    ->update(['activo' => false]);
            }

            $ciclo->update($datos);

            return $ciclo->fresh();
        });
    }

    public function desactivar(Ciclo $ciclo): void
    {
        if (! $ciclo->activo) {
            throw ValidationException::withMessages([
                'activo' => 'El ciclo ya se encuentra inactivo.',
            ]);
        }

        $this->validarCicloSinTrabajoPendiente(
            ciclo: $ciclo,
            mensaje: 'No se puede desactivar este ciclo porque todavía tiene consolidados pendientes o con observaciones.'
        );

        $ciclo->update([
            'activo' => false,
        ]);
    }

    private function normalizarDatos(array $datos): array
    {
        return [
            'nombre' => $datos['nombre'],
            'anio' => (int) $datos['anio'],
            'periodo' => (int) $datos['periodo'],
            'fecha_inicio' => $datos['fecha_inicio'],
            'fecha_fin' => $datos['fecha_fin'],
            'activo' => (bool) ($datos['activo'] ?? false),
        ];
    }

    private function validarCambioDeCicloActivo(?int $cicloIgnoradoId = null): void
    {
        $cicloActivoActual = Ciclo::query()
            ->where('activo', true)
            ->when($cicloIgnoradoId, function ($query) use ($cicloIgnoradoId) {
                $query->whereKeyNot($cicloIgnoradoId);
            })
            ->first();

        if (! $cicloActivoActual) {
            return;
        }

        $this->validarCicloSinTrabajoPendiente(
            ciclo: $cicloActivoActual,
            mensaje: 'No se puede activar otro ciclo porque el ciclo activo actual todavía tiene consolidados pendientes o con observaciones.'
        );
    }

    private function validarCicloSinTrabajoPendiente(Ciclo $ciclo, string $mensaje): void
    {
        $tieneTrabajoPendiente = Consolidado::query()
            ->whereHas('periodoEvaluacion', function ($query) use ($ciclo) {
                $query->where('ciclo_id', $ciclo->id);
            })
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
