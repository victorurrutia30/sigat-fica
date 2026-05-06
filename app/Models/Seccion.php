<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seccion extends Model
{
    use HasFactory;

    protected $table = 'secciones';

    protected $fillable = [
        'ciclo_id',
        'materia_id',
        'numero_seccion',
        'modalidad',
        'aula',
        'nombre_titular',
        'correo_titular',
        'capacidad',
    ];

    protected function casts(): array
    {
        return [
            'capacidad' => 'integer',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class, 'materia_id');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(HorarioSeccion::class, 'seccion_id');
    }

    public function itemsPropuesta(): HasMany
    {
        return $this->hasMany(ItemPropuesta::class, 'seccion_id');
    }

    public function nominasSeccion(): HasMany
    {
        return $this->hasMany(NominaSeccion::class, 'seccion_id');
    }

    public function casosSeguimiento(): HasMany
    {
        return $this->hasMany(CasoSeguimiento::class, 'seccion_id');
    }
}
