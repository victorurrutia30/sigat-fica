<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consolidado extends Model
{
    use HasFactory;

    protected $table = 'consolidados';

    protected $fillable = [
        'periodo_evaluacion_id',
        'tutor_id',
        'estado_entrega',
        'sin_casos',
        'entregado_en',
        'entregado_por',
        'observaciones_coord',
        'revisado_por',
        'revisado_en',
    ];

    protected function casts(): array
    {
        return [
            'sin_casos' => 'boolean',
            'entregado_en' => 'datetime',
            'revisado_en' => 'datetime',
        ];
    }

    public function periodoEvaluacion(): BelongsTo
    {
        return $this->belongsTo(PeriodoEvaluacion::class, 'periodo_evaluacion_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    public function entregadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entregado_por');
    }

    public function revisadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }
}
