<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Password;

class UsuarioInvitacionService
{
    public const USUARIO_INACTIVO = 'usuario_inactivo';

    public function enviar(User $usuario): string
    {
        if (! $usuario->activo) {
            return self::USUARIO_INACTIVO;
        }

        return Password::sendResetLink([
            'correo' => $usuario->correo,
        ]);
    }
}
