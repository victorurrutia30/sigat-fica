<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'correo',
        'password',
        'rol',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    public function getEmailForPasswordReset(): string
    {
        return $this->correo;
    }

    public function tutor(): HasOne
    {
        return $this->hasOne(Tutor::class, 'usuario_id');
    }

    public function propuestasCreadas(): HasMany
    {
        return $this->hasMany(PropuestaAsignacion::class, 'creado_por');
    }

    public function propuestasConRespuestaRegistrada(): HasMany
    {
        return $this->hasMany(PropuestaAsignacion::class, 'respuesta_registrada_por');
    }

    public function historialCambiosPropuesta(): HasMany
    {
        return $this->hasMany(HistorialCambioPropuesta::class, 'modificado_por');
    }

    public function casosRegistrados(): HasMany
    {
        return $this->hasMany(CasoSeguimiento::class, 'registrado_por');
    }

    public function gestionesRegistradas(): HasMany
    {
        return $this->hasMany(GestionCaso::class, 'registrado_por');
    }

    public function consolidadosEntregados(): HasMany
    {
        return $this->hasMany(Consolidado::class, 'entregado_por');
    }

    public function consolidadosRevisados(): HasMany
    {
        return $this->hasMany(Consolidado::class, 'revisado_por');
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class, 'usuario_id');
    }
}
