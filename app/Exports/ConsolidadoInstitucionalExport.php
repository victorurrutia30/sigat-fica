<?php

namespace App\Exports;

use App\Models\CasoSeguimiento;
use App\Models\Consolidado;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsolidadoInstitucionalExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithColumnWidths, WithStyles, WithEvents
{
    public function __construct(
        private readonly Consolidado $consolidado
    ) {}

    public function collection(): Collection
    {
        $this->consolidado->loadMissing([
            'periodoEvaluacion.ciclo',
            'tutor',
        ]);

        $casos = CasoSeguimiento::query()
            ->with([
                'seccion.materia',
                'estudiante',
                'causa',
                'gestiones',
            ])
            ->where('periodo_evaluacion_id', $this->consolidado->periodo_evaluacion_id)
            ->where('tutor_id', $this->consolidado->tutor_id)
            ->orderBy('seccion_id')
            ->orderBy('created_at')
            ->get();

        if ($casos->isEmpty()) {
            return collect([
                [
                    '',
                    '',
                    'Todos han hecho examen parcial',
                    '',
                    '',
                    '',
                    '',
                    'Sin estudiantes no evaluados registrados para este consolidado.',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                    '',
                ],
            ]);
        }

        return $casos->values()->map(function (CasoSeguimiento $caso, int $indice) {
            return [
                $indice + 1,
                $caso->estudiante?->carne ?? '',
                $caso->estudiante?->nombre_completo ?? '',
                '',
                $caso->seccion?->materia?->nombre ?? '',
                $caso->seccion?->numero_seccion ?? '',
                $caso->seccion?->nombre_titular ?? '',
                $caso->detalle_inasistencia ?: ($caso->causa?->nombre ?? ''),
                '',
                $caso->resultado_consolidado === 'rc' ? 'X' : '',
                $caso->resultado_consolidado === 'rm' ? 'X' : '',
                $caso->resultado_consolidado === 'abm' ? 'X' : '',
                $caso->resultado_consolidado === 'abc' ? 'X' : '',
                $this->formatearMatricula($caso->matricula),
                $caso->cuota_cancelada ?? '',
                $caso->gestiones->count(),
            ];
        });
    }

    public function headings(): array
    {
        $this->consolidado->loadMissing([
            'periodoEvaluacion.ciclo',
            'tutor',
        ]);

        return [
            [
                'PROGRAMA DE TUTORES',
            ],
            [
                'DECANATO DE ESTUDIANTES',
            ],
            [
                'FACULTAD DE INFORMÁTICA Y CIENCIAS APLICADAS',
            ],
            [
                'Ciclo: ' . ($this->consolidado->periodoEvaluacion?->ciclo?->nombre ?? 'No definido'),
            ],
            [
                'Periodo: ' . ($this->consolidado->periodoEvaluacion?->nombre ?? 'No definido'),
            ],
            [
                'Tutor(a): ' . ($this->consolidado->tutor?->nombre_completo ?? 'No definido'),
            ],
            [
                'Generado desde SIGAT-FICA: ' . now()->format('d/m/Y H:i'),
            ],
            [
                '',
            ],
            [
                'Nº',
                'Carnet',
                'Nombre del Alumno',
                'Apellidos del Alumno',
                'Asignatura',
                'Sección',
                'Docente',
                'Causa o detalle de inasistencia a evaluación',
                'Realizará Diferido',
                'R/C',
                'R/M',
                'AB/M',
                'AB/C',
                'Matricula',
                'Nº/cuota cancelada',
                'Gestiones',
            ],
        ];
    }

    public function title(): string
    {
        return 'Consolidado';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 18,
            'C' => 30,
            'D' => 30,
            'E' => 35,
            'F' => 12,
            'G' => 30,
            'H' => 55,
            'I' => 18,
            'J' => 10,
            'K' => 10,
            'L' => 10,
            'M' => 10,
            'N' => 14,
            'O' => 18,
            'P' => 12,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 14,
                    'color' => ['rgb' => '5A1533'],
                ],
            ],
            2 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],
            3 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
            ],
            9 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5A1533'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $ultimaFila = $this->collection()->count() + 9;

                foreach (range(1, 8) as $fila) {
                    $sheet->mergeCells("A{$fila}:P{$fila}");
                }

                $sheet->freezePane('A10');
                $sheet->setAutoFilter("A9:P{$ultimaFila}");

                $sheet->getStyle("A1:P{$ultimaFila}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle("A1:P8")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("A9:P{$ultimaFila}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A10:A{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("F10:F{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("I10:P{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    private function formatearMatricula(mixed $matricula): string
    {
        if (is_null($matricula)) {
            return '';
        }

        return (bool) $matricula ? 'Sí' : 'No';
    }
}
