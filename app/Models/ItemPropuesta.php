<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemPropuesta extends Model
{
    use HasFactory;

    protected $table = 'items_propuesta';

    protected $fillable = [
        'propuesta_asignacion_id',
        'tutor_id',
        'seccion_id',
        'prioridad',
        'observaciones',
    ];

    protected function casts(): array
    {
        return [
            'prioridad' => 'boolean',
        ];
    }

    public function propuestaAsignacion(): BelongsTo
    {
        return $this->belongsTo(PropuestaAsignacion::class, 'propuesta_asignacion_id');
    }

    public function tutor(): BelongsTo
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }
}
