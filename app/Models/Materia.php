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
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'creditos' => 'integer',
            'ciclo_plan' => 'integer',
            'activo' => 'boolean',
        ];
    }

    public function secciones(): HasMany
    {
        return $this->hasMany(Seccion::class, 'materia_id');
    }
}
