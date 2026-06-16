<?php

namespace App\Exports\Sheets;

use App\Models\CasoSeguimiento;
use App\Models\ConfirmacionSeccionConsolidado;
use App\Models\Consolidado;
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
    // ── Paleta de marca UTEC ────────────────────────────────────────────────
    private const MAROON_900  = '3D0D22';  // más oscuro — fila universidad
    private const MAROON_700  = '5A1533';  // maroon primario
    private const MAROON_500  = '7B2448';  // medio — banner, cabecera simbología
    private const MAROON_100  = 'EDD9E3';  // muy claro — celdas label, spacers
    private const MAROON_050  = 'F7F0F4';  // casi blanco — filas vacías
    private const ROSE_DIVIDER = 'C9A0B4'; // borde acento entre label y valor
    private const RULE_INNER  = 'DCC8D0';  // líneas internas delgadas
    private const ROW_ALT     = 'F5ECF0';  // tint alternado en filas de datos
    private const TEXT_BODY   = '2B2B2B';  // texto cuerpo
    private const WHITE       = 'FFFFFF';

    // ── Mapa de filas heading (datos desde row 23) ──────────────────────────
    private const ROW_UNIV    = 1;
    private const ROW_FACULTY = 2;
    private const ROW_PROGRAM = 3;
    private const ROW_GAP     = 4;   // separador delgado maroon
    private const ROW_BANNER  = 5;
    // Rows  6–14  = sección info (label | valor | separador | código | descripción)
    // Rows 15–20  = spacers
    private const ROW_TITLE   = 21;
    private const ROW_HEADERS = 22;
    // Row  23+    = datos

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
            sinAsignaciones: true,
        );
    }

    // ────────────────────────────────────────────────────────────────────────
    // DATOS
    // ────────────────────────────────────────────────────────────────────────

    public function collection(): Collection
    {
        if ($this->filas !== null) {
            return $this->filas;
        }

        if ($this->sinAsignaciones || ! $this->item) {
            return $this->filas = collect([
                $this->fila(['No hay secciones asignadas para este periodo.']),
            ]);
        }

        $casos = CasoSeguimiento::query()
            ->with(['estudiante', 'causa', 'gestiones'])
            ->where('periodo_evaluacion_id', $this->periodo->id)
            ->where('seccion_id', $this->item->seccion_id)
            ->where('tutor_id', $this->item->tutor_id)
            ->orderBy('created_at')
            ->get();

        if ($casos->isEmpty()) {
            return $this->filas = collect([
                $this->fila([$this->mensajeSeccionSinCasos()]),
            ]);
        }

        return $this->filas = $casos->values()->map(fn(CasoSeguimiento $caso) => [
            $caso->estudiante?->carne ?? '',
            $caso->estudiante?->nombres ?: ($caso->estudiante?->nombre_completo ?? ''),
            $caso->estudiante?->apellidos ?? '',
            $caso->detalle_inasistencia ?: ($caso->causa?->nombre ?? ''),
            $caso->resultado_consolidado === 'rc'  ? 'X' : '',
            $caso->resultado_consolidado === 'rm'  ? 'X' : '',
            $caso->resultado_consolidado === 'abm' ? 'X' : '',
            $caso->resultado_consolidado === 'abc' ? 'X' : '',
            $caso->matricula ? 'X' : '',
            $caso->cuota_cancelada ?? '',
        ]);
    }

    // ────────────────────────────────────────────────────────────────────────
    // HEADINGS (22 filas fijas)
    //
    // Col:  A                    B               C    D    E    F               G:J (merged)
    // ---   -------------------  ----------      ---  ---  ---  --------------- -------------------
    // r6    Facultad             FICA            ·    ·    ·    Formulario      Inasistencia a…
    // r7    Ciclo                value           ·    ·    ·    —               —
    // r8    Asignatura           value           ·    ·    ·    —               —
    // r9    Sección              value           ·    ·    ·    SIMBOLOGÍA      (merged F9:J9)
    // r10   Docente              value           ·    ·    ·    R/C             Retiro de ciclo
    // r11   Tutor(a)             value           ·    ·    ·    R/M             Retiro de materia
    // r12   —                    —               ·    ·    ·    AB/M            Abandono de materia
    // r13   Inscritos (general)  value           ·    ·    ·    AB/C            Abandono del ciclo
    // r14   Inscritos (nvo ing.) value           ·    ·    ·    —               —
    // ────────────────────────────────────────────────────────────────────────
    public function headings(): array
    {
        $this->periodo->loadMissing('ciclo');

        $seccion = $this->item?->seccion;
        $materia = $seccion?->materia;
        $tutor = $this->item?->tutor;

        return [
            // ── Bloque header ─────────────────────────────────────────
            $this->fila(['UNIVERSIDAD TECNOLÓGICA DE EL SALVADOR']),                  // 1
            $this->fila(['FACULTAD DE INFORMÁTICA Y CIENCIAS APLICADAS']),            // 2
            $this->fila(['PROGRAMA DE TUTORES · DECANATO DE ESTUDIANTES']),           // 3
            $this->fila([]),                                                          // 4
            $this->fila(['NÓMINA DE INASISTENCIA A ' . $this->nombrePeriodoMayusculas()]), // 5

            // ── Sección info ──────────────────────────────────────────
            [
                'Facultad',
                'Informática y Ciencias Aplicadas',
                null,
                null,
                null,
                'Formulario',
                'Inasistencia a ' . $this->nombrePeriodo(),
                null,
                null,
                null,
            ], // 6

            [
                'Ciclo',
                $this->periodo->ciclo?->nombre ?? '—',
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ], // 7

            [
                'Asignatura',
                $materia?->nombre ?? '—',
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ], // 8

            [
                'Sección',
                $seccion?->numero_seccion ?? '—',
                null,
                null,
                null,
                'SIMBOLOGÍA',
                null,
                null,
                null,
                null,
            ], // 9

            [
                'Docente',
                $seccion?->nombre_titular ?? '—',
                null,
                null,
                null,
                'R/C',
                'Retiro de ciclo',
                null,
                null,
                null,
            ], // 10

            [
                'Tutor(a)',
                $tutor?->nombre_completo ?? '—',
                null,
                null,
                null,
                'R/M',
                'Retiro de materia',
                null,
                null,
                null,
            ], // 11

            [
                null,
                null,
                null,
                null,
                null,
                'AB/M',
                'Abandono de materia',
                null,
                null,
                null,
            ], // 12

            [
                'Inscritos (general)',
                'No disponible',
                null,
                null,
                null,
                'AB/C',
                'Abandono del ciclo',
                null,
                null,
                null,
            ], // 13

            [
                'Inscritos (nuevo ing.)',
                'No disponible',
                null,
                null,
                null,
                null,
                null,
                null,
                null,
                null,
            ], // 14

            // ── Spacers ───────────────────────────────────────────────
            $this->fila([]), // 15
            $this->fila([]), // 16
            $this->fila([]), // 17
            $this->fila([]), // 18
            $this->fila([]), // 19
            $this->fila([]), // 20

            // ── Bloque tabla ──────────────────────────────────────────
            $this->fila(['NÓMINA DE ALUMNOS QUE NO REALIZARON ' . $this->nombrePeriodoMayusculas()]), // 21

            [
                'Carnet',
                'Nombre del Alumno',
                'Apellidos del Alumno',
                $this->detalleInasistenciaPeriodo(),
                'R/C',
                'R/M',
                'AB/M',
                'AB/C',
                'Matrícula',
                'Nº/cuota cancelada',
            ], // 22
        ];
    }

    // ────────────────────────────────────────────────────────────────────────
    // METADATA
    // ────────────────────────────────────────────────────────────────────────

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

        return Str::limit($nombre, 31, '');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,   // labels / Carnet
            'B' => 28,   // valores info / Nombre
            'C' => 26,   // valores info / Apellidos
            'D' => 40,   // valores info / Detalle inasistencia
            'E' => 4,    // separador visual (sin contenido)
            'F' => 14,   // código simbología
            'G' => 10,   // descripción simbología
            'H' => 10,
            'I' => 12,
            'J' => 14,   // Nº cuota
        ];
    }

    // ────────────────────────────────────────────────────────────────────────
    // ESTILOS BASE (se aplican antes de registerEvents)
    // ────────────────────────────────────────────────────────────────────────

    public function styles(Worksheet $sheet): array
    {
        return [
            self::ROW_UNIV => [
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => self::WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_900]],
            ],
            self::ROW_FACULTY => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_700]],
            ],
            self::ROW_PROGRAM => [
                'font' => ['bold' => false, 'size' => 9, 'color' => ['rgb' => 'E8C8D4']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_700]],
            ],
            self::ROW_BANNER => [
                'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => self::WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_500]],
            ],
            self::ROW_TITLE => [
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => self::MAROON_900]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_100]],
            ],
            self::ROW_HEADERS => [
                'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::WHITE]],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_700]],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                    'wrapText'   => true,
                ],
            ],
        ];
    }

    // ────────────────────────────────────────────────────────────────────────
    // EVENTOS (layout y estilos post-render)
    // ────────────────────────────────────────────────────────────────────────

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $ws = $event->sheet->getDelegate();

                $rowCount    = max($this->collection()->count(), 1);
                $lastDataRow = self::ROW_HEADERS + $rowCount;

                // ── Configuración hoja ────────────────────────────────
                $ws->setAutoFilter('A22:J' . $lastDataRow);
                $ws->setShowGridLines(false);
                $ws->setPrintGridlines(false);

                // ── Global: centrado vertical + wrap ──────────────────
                $ws->getStyle('A1:J' . $lastDataRow)
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);

                // ── Merges ────────────────────────────────────────────
                // Filas full-width
                foreach ([1, 2, 3, 4, 5, 21] as $r) {
                    $ws->mergeCells("A{$r}:J{$r}");
                }
                // Alineación centrada en filas full-width (excepto row 4)
                foreach ([1, 2, 3, 5, 21] as $r) {
                    $ws->getStyle("A{$r}:J{$r}")
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Panel izquierdo: B:D merged por fila
                foreach ([6, 7, 8, 9, 10, 11, 13, 14] as $r) {
                    $ws->mergeCells("B{$r}:D{$r}");
                }

                // Panel derecho
                $ws->mergeCells('G6:J6');    // valor "Formulario"
                $ws->mergeCells('F9:J9');    // cabecera SIMBOLOGÍA (span completo)
                foreach ([10, 11, 12, 13] as $r) {
                    $ws->mergeCells("G{$r}:J{$r}");
                }

                // ── Alturas de fila ───────────────────────────────────
                $ws->getRowDimension(1)->setRowHeight(26);
                $ws->getRowDimension(2)->setRowHeight(20);
                $ws->getRowDimension(3)->setRowHeight(16);
                $ws->getRowDimension(4)->setRowHeight(4);    // separador delgado maroon
                $ws->getRowDimension(5)->setRowHeight(24);

                foreach (range(6, 14) as $r) {
                    $ws->getRowDimension($r)->setRowHeight(22);
                }
                foreach (range(15, 20) as $r) {
                    $ws->getRowDimension($r)->setRowHeight(8);
                }

                $ws->getRowDimension(21)->setRowHeight(22);
                $ws->getRowDimension(22)->setRowHeight(34);

                foreach (range(23, $lastDataRow) as $r) {
                    $ws->getRowDimension($r)->setRowHeight(22);
                }

                // ── Header block ──────────────────────────────────────
                // Row 4: delgado maroon que "sella" el bloque de encabezados
                $ws->getStyle('A4:J4')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_900]],
                ]);

                // ── Panel info izquierdo (A6:D14) ─────────────────────
                // Base blanco
                $ws->getStyle('A6:D14')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WHITE]],
                ]);

                // Celdas label (col A): rosado + bold maroon
                foreach ([6, 7, 8, 9, 10, 11, 13, 14] as $r) {
                    $ws->getStyle("A{$r}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::MAROON_700]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_100]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    ]);
                    $ws->getStyle("B{$r}:D{$r}")->applyFromArray([
                        'font'      => ['size' => 9, 'color' => ['rgb' => self::TEXT_BODY]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    ]);
                }
                // Row 12: fila vacía en el lado izquierdo
                $ws->getStyle('A12:D12')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F6F6F6']],
                ]);

                // Bordes panel izquierdo
                $ws->getStyle('A6:D14')->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::MAROON_700]]],
                ]);
                foreach (range(7, 14) as $r) {
                    $ws->getStyle("A{$r}:D{$r}")->applyFromArray([
                        'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::RULE_INNER]]],
                    ]);
                }
                // Divisor vertical acento entre A (label) y B (valor)
                $ws->getStyle('A6:A14')->applyFromArray([
                    'borders' => ['right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::ROSE_DIVIDER]]],
                ]);

                // ── Columna E: separador visual ───────────────────────
                $ws->getStyle('E6:E14')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0EBF0']],
                ]);

                // ── Panel info derecho (F6:J14) ───────────────────────
                // Base blanco
                $ws->getStyle('F6:J14')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::WHITE]],
                ]);

                // Row 6: label "Formulario" + valor
                $ws->getStyle('F6')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::MAROON_700]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_100]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $ws->getStyle('G6:J6')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::MAROON_900]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                ]);

                // Rows 7-8: sin contenido en el panel derecho
                $ws->getStyle('F7:J8')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFAFA']],
                ]);

                // Row 9: cabecera SIMBOLOGÍA (merge F9:J9)
                $ws->getStyle('F9:J9')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::WHITE]],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_500]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Rows 10-13: código (F) + descripción (G:J)
                foreach ([10, 11, 12, 13] as $r) {
                    $ws->getStyle("F{$r}")->applyFromArray([
                        'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => self::MAROON_700]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_100]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                    $ws->getStyle("G{$r}:J{$r}")->applyFromArray([
                        'font'      => ['size' => 9, 'color' => ['rgb' => self::TEXT_BODY]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
                    ]);
                }

                // Row 14: lado derecho vacío
                $ws->getStyle('F14:J14')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FAFAFA']],
                ]);

                // Bordes panel derecho (espejo del izquierdo)
                $ws->getStyle('F6:J14')->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::MAROON_700]]],
                ]);
                foreach (range(7, 14) as $r) {
                    $ws->getStyle("F{$r}:J{$r}")->applyFromArray([
                        'borders' => ['top' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::RULE_INNER]]],
                    ]);
                }
                // Divisor vertical entre código (F) y descripción (G:J)
                $ws->getStyle('F6:F14')->applyFromArray([
                    'borders' => ['right' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::ROSE_DIVIDER]]],
                ]);

                // ── Filas spacer (15–20) ──────────────────────────────
                $ws->getStyle('A15:J20')->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_050]],
                ]);

                // ── Bloque tabla ──────────────────────────────────────
                // Borde marco general
                $ws->getStyle("A22:J{$lastDataRow}")->applyFromArray([
                    'borders' => ['outline' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::MAROON_700]]],
                ]);
                // Separador cabecera/datos más grueso
                $ws->getStyle('A22:J22')->applyFromArray([
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::MAROON_900]]],
                ]);

                // Filas de datos: color alternado + líneas horizontales
                for ($r = 23; $r <= $lastDataRow; $r++) {
                    $bg = ($r % 2 !== 0) ? self::ROW_ALT : self::WHITE;
                    $ws->getStyle("A{$r}:J{$r}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bg]],
                        'borders' => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => self::RULE_INNER]]],
                    ]);
                }

                // Divisores verticales entre columnas de datos
                $ws->getStyle("A23:J{$lastDataRow}")->applyFromArray([
                    'borders' => ['vertical' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'E5D5DB']]],
                ]);

                // Alineación en datos
                $ws->getStyle("A23:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $ws->getStyle("E23:J{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // ── Estado vacío ──────────────────────────────────────
                $first = $this->collection()->first();
                $emptyMessages = [
                    'Todos han hecho examen parcial',
                    'Pendiente de entrega del tutor',
                    'Pendiente de confirmación del tutor',
                    'Consolidado con observaciones',
                    'No hay secciones asignadas para este periodo.',
                ];

                if (is_array($first) && in_array($first[0] ?? '', $emptyMessages, true)) {
                    $ws->mergeCells('A23:J23');
                    $ws->getStyle('A23:J23')->applyFromArray([
                        'font'      => ['bold' => true, 'italic' => true, 'size' => 9, 'color' => ['rgb' => self::MAROON_500]],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::MAROON_100]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
    }

    // ────────────────────────────────────────────────────────────────────────
    // DIBUJOS
    // ────────────────────────────────────────────────────────────────────────

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
        $drawing->setResizeProportional(false);
        $drawing->setWidth(90);
        $drawing->setHeight(90);
        $drawing->setCoordinates('H1');
        $drawing->setOffsetX(18);
        $drawing->setOffsetY(5);

        return [$drawing];
    }

    // ────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ────────────────────────────────────────────────────────────────────────

    private function inscritosGeneral(): string|int
    {
        $seccion = $this->item?->seccion;

        if (! $seccion) {
            return 'No disponible';
        }

        $totalNomina = $seccion->nominasSeccion()->count();

        if ($totalNomina > 0) {
            return $totalNomina;
        }

        return 'No disponible';
    }

    private function mensajeSeccionSinCasos(): string
    {
        $consolidado = $this->consolidadoDeLaSeccion();

        if (! $consolidado) {
            return 'Pendiente de entrega del tutor';
        }

        if ($consolidado->estado_entrega === 'con_observaciones') {
            return 'Consolidado con observaciones';
        }

        if ($consolidado->estado_entrega !== 'entregado') {
            return 'Pendiente de entrega del tutor';
        }

        if ($this->seccionConfirmadaSinCasos($consolidado)) {
            return 'Todos han hecho examen parcial';
        }

        return 'Pendiente de confirmación del tutor';
    }

    private function consolidadoDeLaSeccion(): ?Consolidado
    {
        if (! $this->item) {
            return null;
        }

        return Consolidado::query()
            ->where('periodo_evaluacion_id', $this->periodo->id)
            ->where('tutor_id', $this->item->tutor_id)
            ->first();
    }

    private function seccionConfirmadaSinCasos(Consolidado $consolidado): bool
    {
        if (! $this->item) {
            return false;
        }

        return ConfirmacionSeccionConsolidado::query()
            ->where('consolidado_id', $consolidado->id)
            ->where('seccion_id', $this->item->seccion_id)
            ->exists();
    }

    private function nombrePeriodo(): string
    {
        return $this->periodo->nombre ?: 'Periodo de evaluación';
    }

    private function nombrePeriodoMayusculas(): string
    {
        return Str::upper($this->nombrePeriodo());
    }

    private function detalleInasistenciaPeriodo(): string
    {
        return 'Detalle de inasistencia a ' . Str::lower($this->nombrePeriodo());
    }

    private function fila(array $valores): array
    {
        return array_pad($valores, 10, null);
    }
}
