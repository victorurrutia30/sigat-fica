<?php

namespace App\Exports;

use App\Exports\Sheets\ConsolidadoSeccionSheet;
use App\Models\ItemPropuesta;
use App\Models\PeriodoEvaluacion;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConsolidadoPeriodoInstitucionalExport implements WithMultipleSheets
{
    public function __construct(
        private readonly PeriodoEvaluacion $periodo
    ) {}

    public function sheets(): array
    {
        $this->periodo->loadMissing('ciclo');

        $items = ItemPropuesta::query()
            ->with([
                'tutor',
                'seccion.materia',
            ])
            ->whereHas('propuestaAsignacion', function ($query) {
                $query->where('ciclo_id', $this->periodo->ciclo_id)
                    ->where('publicado', true);
            })
            ->whereHas('seccion.materia', function ($query) {
                $query->where('gestionada_por_coordinacion', true);
            })
            ->get()
            ->filter(fn(ItemPropuesta $item) => $item->seccion && $item->tutor)
            ->sortBy(function (ItemPropuesta $item) {
                return sprintf(
                    '%s-%s-%s',
                    $item->seccion?->materia?->nombre ?? '',
                    str_pad((string) ($item->seccion?->numero_seccion ?? ''), 5, '0', STR_PAD_LEFT),
                    $item->tutor?->nombre_completo ?? ''
                );
            })
            ->values();

        if ($items->isEmpty()) {
            return [
                ConsolidadoSeccionSheet::sinAsignaciones($this->periodo),
            ];
        }

        return $items
            ->map(fn(ItemPropuesta $item, int $indice) => new ConsolidadoSeccionSheet(
                periodo: $this->periodo,
                item: $item,
                indice: $indice + 1
            ))
            ->all();
    }
}
