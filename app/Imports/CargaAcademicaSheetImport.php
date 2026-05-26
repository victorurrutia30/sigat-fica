<?php

namespace App\Imports;

use App\Models\Ciclo;
use App\Services\CargaAcademica\ProcesarCargaAcademicaService;
use App\Services\CargaAcademica\ResultadoCargaAcademica;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

class CargaAcademicaSheetImport implements ToCollection, WithCalculatedFormulas
{
    public function __construct(
        private readonly string $nombreHoja,
        private readonly Ciclo $ciclo,
        private readonly ResultadoCargaAcademica $resultado,
        private readonly ProcesarCargaAcademicaService $procesador
    ) {}

    public function collection(Collection $rows): void
    {
        $this->procesador->procesarHoja(
            nombreHoja: $this->nombreHoja,
            filas: $rows,
            ciclo: $this->ciclo,
            resultado: $this->resultado
        );
    }
}
