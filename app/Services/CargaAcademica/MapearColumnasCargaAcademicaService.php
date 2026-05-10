<?php

namespace App\Services\CargaAcademica;

use Illuminate\Support\Collection;

class MapearColumnasCargaAcademicaService
{
    public function __construct(
        private readonly NormalizarTextoService $normalizador
    ) {}

    public function mapear(Collection|array $filaEncabezados): array
    {
        $aliasNormalizados = $this->aliasNormalizados();
        $mapa = [];

        foreach ($filaEncabezados as $indice => $encabezado) {
            $clave = $this->normalizador->claveConfig((string) $encabezado);

            if ($clave === '') {
                continue;
            }

            if (! isset($aliasNormalizados[$clave])) {
                continue;
            }

            $campo = $aliasNormalizados[$clave];

            if (! isset($mapa[$campo])) {
                $mapa[$campo] = $indice;
            }
        }

        return $mapa;
    }

    public function columnasFaltantes(array $mapa): array
    {
        $obligatorias = config('carga_academica.columnas_obligatorias', []);

        return collect($obligatorias)
            ->reject(fn(string $columna) => array_key_exists($columna, $mapa))
            ->values()
            ->all();
    }

    public function tieneColumnasObligatorias(array $mapa): bool
    {
        return count($this->columnasFaltantes($mapa)) === 0;
    }

    public function extraerDatosFila(Collection|array $fila, array $mapa): array
    {
        $datos = [];

        foreach ($mapa as $campo => $indice) {
            $datos[$campo] = $fila[$indice] ?? null;
        }

        return $datos;
    }

    private function aliasNormalizados(): array
    {
        $aliasColumnas = config('carga_academica.alias_columnas', []);
        $aliasNormalizados = [];

        foreach ($aliasColumnas as $campo => $alias) {
            foreach ($alias as $nombreAlias) {
                $clave = $this->normalizador->claveConfig($nombreAlias);
                $aliasNormalizados[$clave] = $campo;
            }
        }

        return $aliasNormalizados;
    }
}
