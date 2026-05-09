<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropuestaAsignacion extends Model
{
    use HasFactory;

    protected $table = 'propuestas_asignacion';

    protected $fillable = [
        'ciclo_id',
        'creado_por',
        'enviado_en',
        'estado_aprobacion',
        'observaciones_decano',
        'fecha_respuesta_decano',
        'respuesta_registrada_por',
        'publicado',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'enviado_en' => 'datetime',
            'fecha_respuesta_decano' => 'date',
            'publicado' => 'boolean',
            'version' => 'integer',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function respuestaRegistradaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respuesta_registrada_por');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ItemPropuesta::class, 'propuesta_asignacion_id');
    }

    public function historialCambios(): HasMany
    {
        return $this->hasMany(HistorialCambioPropuesta::class, 'propuesta_asignacion_id');
    }
}
