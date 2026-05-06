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
        'fecha_contratacion',
        'tiempo_completo',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_contratacion' => 'date',
            'tiempo_completo' => 'boolean',
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
}
