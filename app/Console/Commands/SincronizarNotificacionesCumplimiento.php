<?php

namespace App\Console\Commands;

use App\Services\NotificacionService;
use Illuminate\Console\Command;

class SincronizarNotificacionesCumplimiento extends Command
{
    protected $signature = 'notificaciones:sincronizar-cumplimiento';

    protected $description = 'Sincroniza notificaciones internas relacionadas con cumplimiento de consolidados.';

    public function handle(NotificacionService $notificacionService): int
    {
        $resultado = $notificacionService->sincronizarCumplimiento();

        $this->info('Sincronización de notificaciones completada.');

        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Consolidados evaluados', $resultado['evaluados']],
                ['Notificaciones creadas', $resultado['creadas']],
                ['Notificaciones resueltas', $resultado['resueltas']],
            ]
        );

        return self::SUCCESS;
    }
}
