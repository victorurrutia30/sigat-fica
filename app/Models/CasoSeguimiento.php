<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CasoSeguimiento extends Model
{
    use HasFactory;

    protected $table = 'casos_seguimiento';

    protected $fillable = [
        'periodo_evaluacion_id',
        'seccion_id',
        'estudiante_id',
        'tutor_id',
        'causa_id',
        'resultado_final',
        'detalle_inasistencia',
        'resultado_consolidado',
        'matricula',
        'cuota_cancelada',
        'cerrado',
        'cerrado_en',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
            'matricula' => 'boolean',
            'cerrado' => 'boolean',
            'cerrado_en' => 'datetime',
        ];
    }

    public function periodoEvaluacion(): BelongsTo
    {
        return $this->belongsTo(PeriodoEvaluacion::class, 'periodo_evaluacion_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }

    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    public function causa(): BelongsTo
    {
        return $this->belongsTo(Causa::class, 'causa_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function gestiones(): HasMany
    {
        return $this->hasMany(GestionCaso::class, 'caso_seguimiento_id');
    }

    public function resultadoFinalTexto(): string
    {
        return match ($this->resultado_final) {
            'retiro' => 'Retiro',
            'abandono' => 'Abandono',
            default => 'Pendiente',
        };
    }

    public function resultadoConsolidadoTexto(): string
    {
        return match ($this->resultado_consolidado) {
            'rc' => 'R/C — Retiro de ciclo',
            'rm' => 'R/M — Retiro de materia',
            'abm' => 'AB/M — Abandono de materia',
            'abc' => 'AB/C — Abandono del ciclo',
            default => 'Pendiente',
        };
    }

    public function resultadoConsolidadoMarca(): string
    {
        return match ($this->resultado_consolidado) {
            'rc' => 'R/C',
            'rm' => 'R/M',
            'abm' => 'AB/M',
            'abc' => 'AB/C',
            default => '',
        };
    }
}
