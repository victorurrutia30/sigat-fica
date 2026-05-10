<?php

namespace App\Services\CargaAcademica;

use Illuminate\Support\Collection;

class DetectarEncabezadoService
{
    public function __construct(
        private readonly MapearColumnasCargaAcademicaService $mapeador
    ) {}

    public function detectar(Collection $filas): array
    {
        $maxFilas = (int) config('carga_academica.lectura.max_filas_busqueda_encabezado', 25);
        $mejorCandidato = null;

        foreach ($filas->take($maxFilas) as $indice => $fila) {
            $mapa = $this->mapeador->mapear($fila);
            $faltantes = $this->mapeador->columnasFaltantes($mapa);

            $candidato = [
                'encontrado' => count($faltantes) === 0,
                'indice_fila' => $indice,
                'numero_fila_excel' => $indice + 1,
                'mapa' => $mapa,
                'faltantes' => $faltantes,
                'cantidad_columnas_detectadas' => count($mapa),
            ];

            if ($candidato['encontrado']) {
                return $candidato;
            }

            if (
                $mejorCandidato === null ||
                $candidato['cantidad_columnas_detectadas'] > $mejorCandidato['cantidad_columnas_detectadas']
            ) {
                $mejorCandidato = $candidato;
            }
        }

        return $mejorCandidato ?? [
            'encontrado' => false,
            'indice_fila' => null,
            'numero_fila_excel' => null,
            'mapa' => [],
            'faltantes' => config('carga_academica.columnas_obligatorias', []),
            'cantidad_columnas_detectadas' => 0,
        ];
    }
}
