<?php

namespace App\Imports;

use App\Models\Ciclo;
use App\Services\CargaAcademica\ProcesarCargaAcademicaService;
use App\Services\CargaAcademica\ResultadoCargaAcademica;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CargaAcademicaImport implements WithMultipleSheets, SkipsUnknownSheets
{
    private array $hojasNoProcesadas = [];

    public function __construct(
        private readonly Ciclo $ciclo,
        private readonly ResultadoCargaAcademica $resultado,
        private readonly ProcesarCargaAcademicaService $procesador
    ) {}

    public function sheets(): array
    {
        $sheets = [];

        foreach (config('carga_academica.hojas_permitidas', []) as $nombreHoja) {
            $sheets[$nombreHoja] = new CargaAcademicaSheetImport(
                nombreHoja: $nombreHoja,
                ciclo: $this->ciclo,
                resultado: $this->resultado,
                procesador: $this->procesador
            );
        }

        return $sheets;
    }

    public function onUnknownSheet($sheetName): void
    {
        $this->hojasNoProcesadas[] = (string) $sheetName;
    }

    public function resultado(): ResultadoCargaAcademica
    {
        return $this->resultado;
    }

    public function hojasNoProcesadas(): array
    {
        return $this->hojasNoProcesadas;
    }
}
