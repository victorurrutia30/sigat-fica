<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportacionCargaAcademica extends Model
{
    use HasFactory;

    protected $table = 'importaciones_carga_academica';

    protected $fillable = [
        'ciclo_id',
        'usuario_id',
        'nombre_archivo',
        'hash_archivo',
        'hojas_procesadas',
        'filas_leidas',
        'filas_importadas',
        'filas_ignoradas',
        'filas_error',
        'materias_creadas',
        'materias_actualizadas',
        'secciones_creadas',
        'secciones_actualizadas',
        'horarios_creados',
        'estado',
        'resumen_json',
        'errores_json',
    ];

    protected function casts(): array
    {
        return [
            'hojas_procesadas' => 'integer',
            'filas_leidas' => 'integer',
            'filas_importadas' => 'integer',
            'filas_ignoradas' => 'integer',
            'filas_error' => 'integer',
            'materias_creadas' => 'integer',
            'materias_actualizadas' => 'integer',
            'secciones_creadas' => 'integer',
            'secciones_actualizadas' => 'integer',
            'horarios_creados' => 'integer',
            'resumen_json' => 'array',
            'errores_json' => 'array',
        ];
    }

    public function ciclo(): BelongsTo
    {
        return $this->belongsTo(Ciclo::class, 'ciclo_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function fueExitosa(): bool
    {
        return $this->estado === 'procesado';
    }

    public function tuvoObservaciones(): bool
    {
        return $this->estado === 'procesado_con_observaciones';
    }

    public function fallo(): bool
    {
        return $this->estado === 'fallido';
    }
}
