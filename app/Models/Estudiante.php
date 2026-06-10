<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    use HasFactory;

    protected $table = 'estudiantes';

    protected $fillable = [
        'carne',
        'nombres',
        'apellidos',
        'nombre_completo',
        'correo',
        'carrera',
    ];

    public function nominasSeccion(): HasMany
    {
        return $this->hasMany(NominaSeccion::class, 'estudiante_id');
    }

    public function casosSeguimiento(): HasMany
    {
        return $this->hasMany(CasoSeguimiento::class, 'estudiante_id');
    }
}
