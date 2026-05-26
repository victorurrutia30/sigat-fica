<?php

namespace App\Services\CargaAcademica;

use Carbon\Carbon;
use InvalidArgumentException;

class InterpretarHorarioService
{
    public function __construct(
        private readonly NormalizarTextoService $normalizador
    ) {}

    public function interpretarDias(mixed $valor): array
    {
        $texto = $this->normalizador->encabezado((string) $valor);

        if ($texto === '') {
            throw new InvalidArgumentException('Los días del horario están vacíos.');
        }

        $partes = preg_split('/[\-,\/]+/u', $texto) ?: [];
        $diasConfig = $this->diasNormalizados();
        $dias = [];

        foreach ($partes as $parte) {
            $clave = $this->normalizador->dia($parte);

            if ($clave === '') {
                continue;
            }

            if (! isset($diasConfig[$clave])) {
                throw new InvalidArgumentException("El día [{$parte}] no es reconocido.");
            }

            $dias[] = $diasConfig[$clave];
        }

        $dias = array_values(array_unique($dias));
        sort($dias);

        if (empty($dias)) {
            throw new InvalidArgumentException('No se pudo interpretar ningún día del horario.');
        }

        return $dias;
    }

    public function interpretarHoras(mixed $valor): array
    {
        $texto = trim((string) $valor);
        $texto = str_replace(['–', '—'], '-', $texto);
        $texto = preg_replace('/\s+/u', ' ', $texto) ?? '';

        if ($texto === '') {
            throw new InvalidArgumentException('El rango de horas está vacío.');
        }

        if (! preg_match('/(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})/', $texto, $coincidencias)) {
            throw new InvalidArgumentException("El rango de horas [{$texto}] no tiene formato válido. Use HH:MM-HH:MM.");
        }

        $horaInicio = $this->normalizarHora($coincidencias[1]);
        $horaFin = $this->normalizarHora($coincidencias[2]);

        if ($horaFin <= $horaInicio) {
            throw new InvalidArgumentException('La hora de fin debe ser posterior a la hora de inicio.');
        }

        return [
            'hora_inicio' => $horaInicio,
            'hora_fin' => $horaFin,
        ];
    }

    public function construirHorarios(mixed $dias, mixed $horas): array
    {
        $diasInterpretados = $this->interpretarDias($dias);
        $horasInterpretadas = $this->interpretarHoras($horas);

        return collect($diasInterpretados)
            ->map(fn(int $dia) => [
                'dia_semana' => $dia,
                'hora_inicio' => $horasInterpretadas['hora_inicio'],
                'hora_fin' => $horasInterpretadas['hora_fin'],
            ])
            ->values()
            ->all();
    }

    private function normalizarHora(string $hora): string
    {
        try {
            return Carbon::createFromFormat('H:i', $hora)->format('H:i');
        } catch (\Throwable) {
            try {
                return Carbon::parse($hora)->format('H:i');
            } catch (\Throwable) {
                throw new InvalidArgumentException("La hora [{$hora}] no tiene formato válido.");
            }
        }
    }

    private function diasNormalizados(): array
    {
        $dias = config('carga_academica.dias_semana', []);
        $normalizados = [];

        foreach ($dias as $texto => $numero) {
            $normalizados[$this->normalizador->dia($texto)] = $numero;
        }

        return $normalizados;
    }
}
