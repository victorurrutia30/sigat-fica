<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PeriodoEvaluacion extends Model
{
    protected $table = 'periodos_evaluacion';

    protected $fillable = [
        'ciclo_id',
        'nombre',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite_consolidado',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'fecha_limite_consolidado' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }

    public function casosSeguimiento(): HasMany
    {
        return $this->hasMany(CasoSeguimiento::class, 'periodo_evaluacion_id');
    }

    public function consolidados(): HasMany
    {
        return $this->hasMany(Consolidado::class, 'periodo_evaluacion_id');
    }
}
