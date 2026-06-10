<?php

namespace App\Exports\Sheets;

use App\Models\CasoSeguimiento;
use App\Models\ItemPropuesta;
use App\Models\PeriodoEvaluacion;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConsolidadoSeccionSheet implements FromCollection, WithHeadings, WithTitle, WithColumnWidths, WithStyles, WithEvents, WithDrawings
{
    private ?Collection $filas = null;

    public function __construct(
        private readonly PeriodoEvaluacion $periodo,
        private readonly ?ItemPropuesta $item,
        private readonly int $indice = 1,
        private readonly bool $sinAsignaciones = false
    ) {}

    public static function sinAsignaciones(PeriodoEvaluacion $periodo): self
    {
        return new self(
            periodo: $periodo,
            item: null,
            indice: 1,
            sinAsignaciones: true
        );
    }

    public function collection(): Collection
    {
        if ($this->filas !== null) {
            return $this->filas;
        }

        if ($this->sinAsignaciones || ! $this->item) {
            $this->filas = collect([
                $this->fila([
                    'No hay secciones asignadas para este periodo.',
                ]),
            ]);

            return $this->filas;
        }

        $casos = CasoSeguimiento::query()
            ->with([
                'estudiante',
                'causa',
                'gestiones',
            ])
            ->where('periodo_evaluacion_id', $this->periodo->id)
            ->where('seccion_id', $this->item->seccion_id)
            ->where('tutor_id', $this->item->tutor_id)
            ->orderBy('created_at')
            ->get();

        if ($casos->isEmpty()) {
            $this->filas = collect([
                $this->fila([
                    'Todos han hecho examen parcial',
                ]),
            ]);

            return $this->filas;
        }

        $this->filas = $casos->values()->map(function (CasoSeguimiento $caso) {
            return [
                $caso->estudiante?->carne ?? '',
                $caso->estudiante?->nombre_completo ?? '',
                '',
                $caso->detalle_inasistencia ?: ($caso->causa?->nombre ?? ''),
                $caso->resultado_consolidado === 'rc' ? 'X' : '',
                $caso->resultado_consolidado === 'rm' ? 'X' : '',
                $caso->resultado_consolidado === 'abm' ? 'X' : '',
                $caso->resultado_consolidado === 'abc' ? 'X' : '',
                $caso->matricula ? 'X' : '',
                $caso->cuota_cancelada ?? '',
            ];
        });

        return $this->filas;
    }

    public function headings(): array
    {
        $this->periodo->loadMissing('ciclo');

        $seccion = $this->item?->seccion;
        $materia = $seccion?->materia;
        $tutor = $this->item?->tutor;

        return [
            $this->fila([' Universidad Tecnologica de El Salvador ']),
            $this->fila([' Programa de Tutores ']),
            $this->fila([' Decanato de Estudiantes ']),
            $this->fila([]),
            $this->fila([' Programa de Tutores ']),
            $this->fila(['Facultad de: Informatica y Ciencias Aplicadas', null, null, null, 'Formulario']),
            $this->fila(['Ciclo  ' . ($this->periodo->ciclo?->nombre ?? 'No definido'), null, null, null, ' Inasistencia']),
            $this->fila(['Asignatura: ' . ($materia?->nombre ?? 'No definida')]),
            $this->fila(['Sección: ', $seccion?->numero_seccion ?? '', null, null, ' Simbologia ']),
            $this->fila(['Docente: ' . ($seccion?->nombre_titular ?? ''), null, null, null, 'R/C']),
            $this->fila(['Tutor(a) : ' . ($tutor?->nombre_completo ?? ''), null, null, null, 'Retiro de ciclo ']),
            $this->fila([null, null, null, null, 'R/M']),
            $this->fila(['Inscritos en General : ' . ($seccion?->capacidad ?? ''), null, null, null, 'Retiro de materia ']),
            $this->fila(['Incritos  de nuevo ingreso: ', null, null, null, 'AB/M']),
            $this->fila([null, null, null, null, 'Abandono de materia']),
            $this->fila([null, null, null, null, 'AB/C']),
            $this->fila([null, null, null, null, 'Abandono del Ciclo']),
            $this->fila([]),
            $this->fila([]),
            $this->fila([]),
            $this->fila(['Nómina de alumnos que no realizaron primera evaluación']),
            [
                'Carnet',
                'Nombre del Alumno',
                'Apellidos del Alumno ',
                ' Detalle de Inasistencia a primera evaluación',
                'R/C',
                'R/M',
                'AB/M',
                'AB/C',
                'Matricula',
                'Nº/cuota cancelada',
            ],
        ];
    }

    public function title(): string
    {
        if ($this->sinAsignaciones || ! $this->item) {
            return 'Sin asignaciones';
        }

        $seccion = $this->item->seccion;
        $materia = $seccion?->materia;

        $nombre = trim(sprintf(
            '%s %s',
            $materia?->nombre ?? 'Materia',
            $seccion?->numero_seccion ?? ''
        ));

        $nombre = preg_replace('/[\\\\\\/\\?\\*\\[\\]:]/', ' ', $nombre) ?? $nombre;
        $nombre = preg_replace('/\s+/', ' ', $nombre) ?? $nombre;

        return Str::limit($this->indice . ' ' . $nombre, 31, '');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 28,
            'C' => 28,
            'D' => 55,
            'E' => 10,
            'F' => 10,
            'G' => 10,
            'H' => 10,
            'I' => 14,
            'J' => 18,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
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
            21 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'D9D9D9'],
                ],
            ],
            22 => [
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

                $cantidadFilas = max($this->collection()->count(), 1);
                $ultimaFila = 22 + $cantidadFilas;

                foreach ([1, 2, 3, 5, 21] as $fila) {
                    $sheet->mergeCells("A{$fila}:J{$fila}");
                }

                $sheet->freezePane('A23');
                $sheet->setAutoFilter("A22:J{$ultimaFila}");

                $sheet->getStyle("A1:J{$ultimaFila}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle('A1:J3')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('A21:J21')
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("A22:J{$ultimaFila}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A23:C{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("E23:J{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("D23:D{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $primeraFila = $this->collection()->first();

                if (
                    is_array($primeraFila)
                    && in_array($primeraFila[0] ?? '', [
                        'Todos han hecho examen parcial',
                        'No hay secciones asignadas para este periodo.',
                    ], true)
                ) {
                    $sheet->mergeCells('A23:J23');

                    $sheet->getStyle('A23:J23')
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle('A23:J23')
                        ->getFont()
                        ->setBold(true);
                }

                $sheet->getRowDimension(21)->setRowHeight(22);
                $sheet->getRowDimension(22)->setRowHeight(30);
            },
        ];
    }

    public function drawings(): array
    {
        $logoPath = public_path('images/logo-utec.png');

        if (! file_exists($logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo UTEC');
        $drawing->setDescription('Logo UTEC');
        $drawing->setPath($logoPath);
        $drawing->setHeight(90);
        $drawing->setCoordinates('I8');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);

        return [$drawing];
    }

    private function fila(array $valores): array
    {
        return array_pad($valores, 10, null);
    }
}
