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
        'cerrado',
        'cerrado_en',
        'registrado_por',
    ];

    protected function casts(): array
    {
        return [
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
}
