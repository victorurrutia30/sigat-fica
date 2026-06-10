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

    public function tipoCambioLegible(): string
    {
        return ucfirst(str_replace('_', ' ', $this->tipo_cambio));
    }

    public function esReemplazoTutor(): bool
    {
        return $this->tipo_cambio === 'tutor_reemplazado';
    }

    public function tutorAnterior(): ?string
    {
        return data_get($this->datos_anteriores, 'tutor');
    }

    public function tutorNuevo(): ?string
    {
        return data_get($this->datos_nuevos, 'tutor');
    }

    public function materiaCodigo(): ?string
    {
        return data_get($this->datos_nuevos, 'materia_codigo')
            ?? data_get($this->datos_anteriores, 'materia_codigo');
    }

    public function materiaNombre(): ?string
    {
        return data_get($this->datos_nuevos, 'materia_nombre')
            ?? data_get($this->datos_anteriores, 'materia_nombre');
    }

    public function numeroSeccion(): ?string
    {
        return data_get($this->datos_nuevos, 'numero_seccion')
            ?? data_get($this->datos_anteriores, 'numero_seccion');
    }

    public function aula(): ?string
    {
        return data_get($this->datos_nuevos, 'aula')
            ?? data_get($this->datos_anteriores, 'aula');
    }

    public function motivoReemplazo(): ?string
    {
        return data_get($this->datos_nuevos, 'observaciones');
    }
}
