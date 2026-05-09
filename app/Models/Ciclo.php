<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ciclo extends Model
{
    use HasFactory;

    protected $table = 'ciclos';

    protected $fillable = [
        'nombre',
        'anio',
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'anio' => 'integer',
            'periodo' => 'integer',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activo' => 'boolean',
        ];
    }

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class, 'ciclo_id');
    }
    public function propuestasAsignacion(): HasMany
    {
        return $this->hasMany(PropuestaAsignacion::class, 'ciclo_id');
    }

    public function periodosEvaluacion(): HasMany
    {
        return $this->hasMany(PeriodoEvaluacion::class, 'ciclo_id');
    }
}
