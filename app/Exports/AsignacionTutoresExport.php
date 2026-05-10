<?php

namespace App\Exports;

use App\Models\ItemPropuesta;
use App\Models\PropuestaAsignacion;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AsignacionTutoresExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    private int $contador = 0;

    public function __construct(
        private readonly PropuestaAsignacion $propuesta
    ) {}

    public function collection(): Collection
    {
        $this->propuesta->loadMissing([
            'ciclo',
            'items.tutor',
            'items.seccion.materia',
            'items.seccion.horarios',
        ]);

        return $this->propuesta->items
            ->sortBy(function (ItemPropuesta $item) {
                return sprintf(
                    '%s-%s-%s',
                    $item->seccion?->materia?->nombre ?? '',
                    $item->seccion?->materia?->codigo ?? '',
                    str_pad((string) ($item->seccion?->numero_seccion ?? ''), 5, '0', STR_PAD_LEFT)
                );
            })
            ->values();
    }

    public function headings(): array
    {
        return [
            [
                'PROPUESTA DE ASIGNACIÓN DE TUTORES - ' . ($this->propuesta->ciclo?->nombre ?? 'CICLO NO DEFINIDO'),
            ],
            [
                'Generado desde SIGAT-FICA',
            ],
            [
                'N°',
                'Asignaturas Tutoradas',
                'Código',
                'Sección',
                'Hora',
                'Días',
                'Aula',
                'Correo',
                'Docente titular',
                'Docente tutor(a)',
                'Modalidad',
            ],
        ];
    }

    public function map($item): array
    {
        /** @var ItemPropuesta $item */
        $this->contador++;

        $seccion = $item->seccion;
        $materia = $seccion?->materia;
        $tutor = $item->tutor;

        return [
            $this->contador,
            $materia?->nombre ?? 'Sin materia',
            $materia?->codigo ?? 'Sin código',
            $seccion?->numero_seccion ?? 'Sin sección',
            $this->formatearHoras($item),
            $this->formatearDias($item),
            $this->formatearAula($item),
            $seccion?->correo_titular ?? '',
            $seccion?->nombre_titular ?? '',
            $tutor?->nombre_completo ?? '',
            $this->formatearModalidad($seccion?->modalidad),
        ];
    }

    public function title(): string
    {
        return 'Asignación tutores';
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
                    'italic' => true,
                    'color' => ['rgb' => '666666'],
                ],
            ],
            3 => [
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

                $ultimaFila = $this->collection()->count() + 3;

                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');

                $sheet->freezePane('A4');
                $sheet->setAutoFilter("A3:K{$ultimaFila}");

                $sheet->getStyle("A1:K{$ultimaFila}")
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                $sheet->getStyle("A3:K{$ultimaFila}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN);

                $sheet->getStyle("A4:K{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                $sheet->getStyle("A4:A{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("D4:D{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle("K4:K{$ultimaFila}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }

    private function formatearHoras(ItemPropuesta $item): string
    {
        $seccion = $item->seccion;

        if (! $seccion) {
            return '';
        }

        if ($seccion->modalidad === 'virtual') {
            return 'Virtual';
        }

        if ($seccion->horarios->isEmpty()) {
            return 'Sin horario';
        }

        return $seccion->horarios
            ->map(fn($horario) => $this->horaCorta($horario->hora_inicio) . '-' . $this->horaCorta($horario->hora_fin))
            ->unique()
            ->implode("\n");
    }

    private function formatearDias(ItemPropuesta $item): string
    {
        $seccion = $item->seccion;

        if (! $seccion) {
            return '';
        }

        if ($seccion->modalidad === 'virtual') {
            return 'Virtual';
        }

        if ($seccion->horarios->isEmpty()) {
            return 'Sin días';
        }

        return $seccion->horarios
            ->map(fn($horario) => $this->nombreDia((int) $horario->dia_semana))
            ->unique()
            ->implode(', ');
    }

    private function formatearAula(ItemPropuesta $item): string
    {
        $seccion = $item->seccion;

        if (! $seccion) {
            return '';
        }

        if ($seccion->modalidad === 'virtual') {
            return 'VIRTUAL';
        }

        if ($seccion->modalidad === 'en_linea') {
            return 'EN LÍNEA';
        }

        return $seccion->aula ?: 'No definida';
    }

    private function formatearModalidad(?string $modalidad): string
    {
        return match ($modalidad) {
            'presencial' => 'PRESENCIAL',
            'en_linea' => 'EN LÍNEA',
            'virtual' => 'VIRTUAL',
            'mixta' => 'MIXTA',
            default => Str::upper((string) $modalidad),
        };
    }

    private function nombreDia(int $diaSemana): string
    {
        return match ($diaSemana) {
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
            7 => 'Domingo',
            default => 'Día no definido',
        };
    }

    private function horaCorta(string $hora): string
    {
        return substr($hora, 0, 5);
    }
}
