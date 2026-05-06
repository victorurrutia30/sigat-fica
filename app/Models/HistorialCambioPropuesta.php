<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HistorialCambioPropuesta extends Model
{
    use HasFactory;

    protected $table = 'historial_cambios_propuesta';

    const UPDATED_AT = null;

    protected $fillable = [
        'propuesta_asignacion_id',
        'modificado_por',
        'tipo_cambio',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function propuestaAsignacion(): BelongsTo
    {
        return $this->belongsTo(PropuestaAsignacion::class, 'propuesta_asignacion_id');
    }

    public function modificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }
}
