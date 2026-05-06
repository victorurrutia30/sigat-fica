<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HorarioSeccion extends Model
{
    use HasFactory;

    protected $table = 'horarios_seccion';

    protected $fillable = [
        'seccion_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    protected function casts(): array
    {
        return [
            'dia_semana' => 'integer',
        ];
    }

    public function seccion(): BelongsTo
    {
        return $this->belongsTo(Seccion::class, 'seccion_id');
    }
}
