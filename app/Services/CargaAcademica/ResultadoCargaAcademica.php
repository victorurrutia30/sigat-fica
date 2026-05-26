<?php

namespace App\Services\CargaAcademica;

class ResultadoCargaAcademica
{
    private array $contadores = [
        'hojas_procesadas' => 0,
        'filas_leidas' => 0,
        'filas_importadas' => 0,
        'filas_ignoradas' => 0,
        'filas_error' => 0,
        'materias_creadas' => 0,
        'materias_actualizadas' => 0,
        'secciones_creadas' => 0,
        'secciones_actualizadas' => 0,
        'horarios_creados' => 0,
    ];

    private array $errores = [];

    private array $advertencias = [];

    private array $hojas = [];

    public function incrementar(string $contador, int $cantidad = 1): void
    {
        if (! array_key_exists($contador, $this->contadores)) {
            $this->contadores[$contador] = 0;
        }

        $this->contadores[$contador] += $cantidad;
    }

    public function registrarHoja(string $nombreHoja): void
    {
        if (! in_array($nombreHoja, $this->hojas, true)) {
            $this->hojas[] = $nombreHoja;
            $this->incrementar('hojas_procesadas');
        }
    }

    public function registrarError(string $hoja, int|string $fila, string $mensaje, array $datos = []): void
    {
        $this->incrementar('filas_error');

        $this->errores[] = [
            'hoja' => $hoja,
            'fila' => $fila,
            'mensaje' => $mensaje,
            'datos' => $datos,
        ];
    }

    public function registrarAdvertencia(string $hoja, int|string $fila, string $mensaje, array $datos = []): void
    {
        $this->advertencias[] = [
            'hoja' => $hoja,
            'fila' => $fila,
            'mensaje' => $mensaje,
            'datos' => $datos,
        ];
    }

    public function contadores(): array
    {
        return $this->contadores;
    }

    public function errores(): array
    {
        return $this->errores;
    }

    public function advertencias(): array
    {
        return $this->advertencias;
    }

    public function hojas(): array
    {
        return $this->hojas;
    }

    public function estado(): string
    {
        if ($this->contadores['filas_importadas'] === 0 && $this->contadores['filas_error'] > 0) {
            return 'fallido';
        }

        if ($this->contadores['filas_error'] > 0 || count($this->advertencias) > 0) {
            return 'procesado_con_observaciones';
        }

        return 'procesado';
    }

    public function resumen(): array
    {
        return [
            'estado' => $this->estado(),
            'hojas' => $this->hojas,
            'contadores' => $this->contadores,
            'advertencias' => $this->advertencias,
        ];
    }
}
