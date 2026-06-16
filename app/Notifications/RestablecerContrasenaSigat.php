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
            ->subject('Acceso a SIGAT-FICA')
            ->greeting("Hola, {$nombre}.")
            ->line('Este enlace permite establecer o restablecer la contraseña de tu cuenta en SIGAT-FICA.')
            ->line('Usa el siguiente botón para definir una contraseña personal.')
            ->action('Establecer contraseña', $url)
            ->line("Por seguridad, este enlace vence en {$minutosExpiracion} minutos.")
            ->line('Si no reconoces esta solicitud, informa a Coordinación.')
            ->salutation('SIGAT-FICA');
    }
}
