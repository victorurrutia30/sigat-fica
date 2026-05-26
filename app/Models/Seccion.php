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
        'requiere_tutor',
        'aula',
        'nombre_titular',
        'correo_titular',
        'codigo_docente_titular',
        'categoria_docente_titular',
        'capacidad',
        'observaciones_carga',
    ];

    protected function casts(): array
    {
        return [
            'capacidad' => 'integer',
            'requiere_tutor' => 'boolean',
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

    public function esVirtual(): bool
    {
        return $this->modalidad === 'virtual';
    }

    public function esEnLinea(): bool
    {
        return $this->modalidad === 'en_linea';
    }

    public function puedeEntrarEnPropuesta(): bool
    {
        return $this->requiere_tutor && $this->materia?->gestionada_por_coordinacion;
    }
}
