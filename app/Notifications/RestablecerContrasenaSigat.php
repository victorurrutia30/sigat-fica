<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RestablecerContrasenaSigat extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $token
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'correo' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $nombre = $notifiable->nombre ?: 'usuario';
        $minutosExpiracion = config('auth.passwords.users.expire', 60);

        return (new MailMessage)
            ->theme('utec')
            ->subject('Invitación de acceso a SIGAT-FICA')
            ->greeting("Hola, {$nombre}.")
            ->line('Coordinación ha generado tu acceso al Sistema de Gestión de Tutorías de FICA.')
            ->line('Para ingresar por primera vez, establece tu contraseña personal mediante el siguiente enlace.')
            ->action('Establecer contraseña', $url)
            ->line("Por seguridad, este enlace vence en {$minutosExpiracion} minutos.")
            ->line('Si no reconoces esta solicitud, informa a Coordinación.')
            ->salutation('SIGAT-FICA');
    }
}
