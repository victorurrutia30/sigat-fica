<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tutor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tutores';

    protected $fillable = [
        'usuario_id',
        'codigo_empleado',
        'nombre_completo',
        'correo_institucional',
        'departamento',
        'categoria_docente',
        'fecha_contratacion',
        'tiempo_completo',
        'habilitado_para_tutorias',
        'es_excepcion_tutoria',
        'motivo_excepcion_tutoria',
        'origen_registro',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_contratacion' => 'date',
            'tiempo_completo' => 'boolean',
            'habilitado_para_tutorias' => 'boolean',
            'es_excepcion_tutoria' => 'boolean',
            'activo' => 'boolean',
        ];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function itemsPropuesta(): HasMany
    {
        return $this->hasMany(ItemPropuesta::class, 'tutor_id');
    }

    public function casosSeguimiento(): HasMany
    {
        return $this->hasMany(CasoSeguimiento::class, 'tutor_id');
    }

    public function consolidados(): HasMany
    {
        return $this->hasMany(Consolidado::class, 'tutor_id');
    }

    public function puedeAsignarseComoTutor(): bool
    {
        return $this->activo
            && $this->habilitado_para_tutorias
            && ($this->tiempo_completo || $this->es_excepcion_tutoria);
    }

    public function tipoHabilitacion(): string
    {
        if ($this->tiempo_completo) {
            return 'DTC';
        }

        if ($this->es_excepcion_tutoria) {
            return 'Excepción autorizada';
        }

        return 'No habilitado';
    }
}
