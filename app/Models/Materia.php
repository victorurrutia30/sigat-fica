<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materias';

    protected $fillable = [
        'codigo',
        'nombre',
        'creditos',
        'ciclo_plan',
        'departamento',
        'gestionada_por_coordinacion',
        'requiere_revision',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'creditos' => 'integer',
            'ciclo_plan' => 'integer',
            'gestionada_por_coordinacion' => 'boolean',
            'requiere_revision' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class, 'materia_id');
    }

    public function esPrioritaria(): bool
    {
        return $this->ciclo_plan !== null && $this->ciclo_plan <= 2;
    }

    public function estaCompletaParaAsignacion(): bool
    {
        return ! $this->requiere_revision && $this->ciclo_plan !== null;
    }
}
