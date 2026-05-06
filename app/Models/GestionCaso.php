<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GestionCaso extends Model
{
    use HasFactory;

    protected $table = 'gestiones_caso';

    protected $fillable = [
        'caso_seguimiento_id',
        'registrado_por',
        'fecha_gestion',
        'medio_contacto',
        'accion_realizada',
        'resultado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_gestion' => 'date',
        ];
    }

    public function casoSeguimiento(): BelongsTo
    {
        return $this->belongsTo(CasoSeguimiento::class, 'caso_seguimiento_id');
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }
}
