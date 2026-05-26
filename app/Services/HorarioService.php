<?php

namespace App\Services;

use App\Models\ItemPropuesta;
use App\Models\Seccion;
use Carbon\Carbon;

class HorarioService
{
    /**
     * Devuelve los choques de horario entre una sección nueva y las secciones
     * ya asignadas al mismo tutor dentro de una propuesta.
     */
    public function obtenerChoques(
        int $tutorId,
        int $seccionId,
        int $propuestaId,
        ?int $itemIgnoradoId = null
    ): array {
        $seccionNueva = Seccion::query()
            ->with(['materia', 'horarios'])
            ->findOrFail($seccionId);

        if ($seccionNueva->horarios->isEmpty()) {
            return [];
        }

        $itemsExistentes = ItemPropuesta::query()
            ->with([
                'seccion.materia',
                'seccion.horarios',
            ])
            ->where('propuesta_asignacion_id', $propuestaId)
            ->where('tutor_id', $tutorId)
            ->when($itemIgnoradoId, function ($query) use ($itemIgnoradoId) {
                $query->whereKeyNot($itemIgnoradoId);
            })
            ->get();

        $choques = [];

        foreach ($itemsExistentes as $itemExistente) {
            $seccionExistente = $itemExistente->seccion;

            if (! $seccionExistente || $seccionExistente->horarios->isEmpty()) {
                continue;
            }

            foreach ($seccionNueva->horarios as $horarioNuevo) {
                foreach ($seccionExistente->horarios as $horarioExistente) {
                    if ((int) $horarioNuevo->dia_semana !== (int) $horarioExistente->dia_semana) {
                        continue;
                    }

                    if (! $this->seTraslapan(
                        $horarioNuevo->hora_inicio,
                        $horarioNuevo->hora_fin,
                        $horarioExistente->hora_inicio,
                        $horarioExistente->hora_fin
                    )) {
                        continue;
                    }

                    $choques[] = [
                        'dia_semana' => (int) $horarioNuevo->dia_semana,
                        'dia_nombre' => $this->nombreDia((int) $horarioNuevo->dia_semana),
                        'seccion_nueva' => [
                            'id' => $seccionNueva->id,
                            'materia' => $seccionNueva->materia?->nombre,
                            'codigo_materia' => $seccionNueva->materia?->codigo,
                            'numero_seccion' => $seccionNueva->numero_seccion,
                            'hora_inicio' => $this->formatearHora($horarioNuevo->hora_inicio),
                            'hora_fin' => $this->formatearHora($horarioNuevo->hora_fin),
                        ],
                        'seccion_existente' => [
                            'id' => $seccionExistente->id,
                            'materia' => $seccionExistente->materia?->nombre,
                            'codigo_materia' => $seccionExistente->materia?->codigo,
                            'numero_seccion' => $seccionExistente->numero_seccion,
                            'hora_inicio' => $this->formatearHora($horarioExistente->hora_inicio),
                            'hora_fin' => $this->formatearHora($horarioExistente->hora_fin),
                        ],
                    ];
                }
            }
        }

        return $choques;
    }

    public function tieneChoque(
        int $tutorId,
        int $seccionId,
        int $propuestaId,
        ?int $itemIgnoradoId = null
    ): bool {
        return count($this->obtenerChoques(
            tutorId: $tutorId,
            seccionId: $seccionId,
            propuestaId: $propuestaId,
            itemIgnoradoId: $itemIgnoradoId
        )) > 0;
    }

    public function mensajeChoque(array $choques): string
    {
        if (empty($choques)) {
            return '';
        }

        $primerChoque = $choques[0];

        $nueva = $primerChoque['seccion_nueva'];
        $existente = $primerChoque['seccion_existente'];

        return sprintf(
            'El tutor ya tiene una sección asignada con choque de horario el día %s. Nueva sección: %s %s, %s-%s. Sección existente: %s %s, %s-%s.',
            $primerChoque['dia_nombre'],
            $nueva['codigo_materia'],
            $nueva['numero_seccion'],
            $nueva['hora_inicio'],
            $nueva['hora_fin'],
            $existente['codigo_materia'],
            $existente['numero_seccion'],
            $existente['hora_inicio'],
            $existente['hora_fin']
        );
    }

    private function seTraslapan(
        string $inicioA,
        string $finA,
        string $inicioB,
        string $finB
    ): bool {
        $inicioA = Carbon::parse($inicioA);
        $finA = Carbon::parse($finA);
        $inicioB = Carbon::parse($inicioB);
        $finB = Carbon::parse($finB);

        return $inicioA->lt($finB) && $inicioB->lt($finA);
    }

    private function formatearHora(string $hora): string
    {
        return Carbon::parse($hora)->format('H:i');
    }

    private function nombreDia(int $diaSemana): string
    {
        return match ($diaSemana) {
            1 => 'lunes',
            2 => 'martes',
            3 => 'miércoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sábado',
            7 => 'domingo',
            default => 'día no definido',
        };
    }
}
