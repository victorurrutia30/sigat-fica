<?php

namespace App\Services;

use App\Models\Consolidado;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificacionService
{
    private const DIAS_RECORDATORIO = 3;

    public function sincronizarCumplimiento(): array
    {
        $resueltas = $this->resolverNotificacionesDeConsolidadosEntregados();
        $creadas = 0;

        $consolidados = $this->consolidadosPendientesDelPeriodoActivo();

        foreach ($consolidados as $consolidado) {
            $creadas += $this->generarNotificacionesParaConsolidado($consolidado);
        }

        return [
            'evaluados' => $consolidados->count(),
            'creadas' => $creadas,
            'resueltas' => $resueltas,
        ];
    }

    public function cantidadNoLeidas(User $usuario): int
    {
        return Notificacion::query()
            ->where('usuario_id', $usuario->id)
            ->where('leido', false)
            ->count();
    }

    public function recientes(User $usuario, int $limite = 5): Collection
    {
        return Notificacion::query()
            ->where('usuario_id', $usuario->id)
            ->latest()
            ->limit($limite)
            ->get();
    }

    public function marcarComoLeida(Notificacion $notificacion, User $usuario): void
    {
        if ((int) $notificacion->usuario_id !== (int) $usuario->id) {
            abort(403, 'No tienes permiso para modificar esta notificación.');
        }

        if ($notificacion->leido) {
            return;
        }

        $notificacion->update([
            'leido' => true,
            'leido_en' => now(),
        ]);
    }

    private function consolidadosPendientesDelPeriodoActivo(): Collection
    {
        return Consolidado::query()
            ->with([
                'periodoEvaluacion.ciclo',
                'tutor.usuario',
            ])
            ->where('estado_entrega', '!=', 'entregado')
            ->whereHas('periodoEvaluacion', function ($query) {
                $query->where('activo', true)
                    ->whereHas('ciclo', function ($cicloQuery) {
                        $cicloQuery->where('activo', true);
                    });
            })
            ->get();
    }

    private function generarNotificacionesParaConsolidado(Consolidado $consolidado): int
    {
        $periodo = $consolidado->periodoEvaluacion;

        if (! $periodo || ! $periodo->fecha_limite_consolidado) {
            return 0;
        }

        $hoy = now()->startOfDay();
        $fechaLimite = $periodo->fecha_limite_consolidado->copy()->startOfDay();

        if ($fechaLimite->lt($hoy)) {
            return $this->generarNotificacionesDeAtraso($consolidado);
        }

        $diasRestantes = (int) $hoy->diffInDays($fechaLimite, false);

        if ($diasRestantes >= 0 && $diasRestantes <= self::DIAS_RECORDATORIO) {
            return $this->generarRecordatorioParaTutor($consolidado, $diasRestantes);
        }

        return 0;
    }

    private function generarNotificacionesDeAtraso(Consolidado $consolidado): int
    {
        $creadas = 0;

        $usuarioTutor = $consolidado->tutor?->usuario;

        if ($this->usuarioPuedeRecibirNotificaciones($usuarioTutor)) {
            $creadas += $this->crearSiNoExiste(
                usuario: $usuarioTutor,
                tipo: 'consolidado_atrasado_tutor',
                consolidado: $consolidado,
                titulo: 'Consolidado atrasado',
                mensaje: $this->mensajeAtrasoTutor($consolidado)
            );
        }

        $usuariosCoordinacion = User::query()
            ->where('rol', 'coordinacion')
            ->where('activo', true)
            ->get();

        foreach ($usuariosCoordinacion as $usuarioCoordinacion) {
            $creadas += $this->crearSiNoExiste(
                usuario: $usuarioCoordinacion,
                tipo: 'consolidado_atrasado_coordinacion',
                consolidado: $consolidado,
                titulo: 'Tutor con consolidado atrasado',
                mensaje: $this->mensajeAtrasoCoordinacion($consolidado)
            );
        }

        return $creadas;
    }

    private function generarRecordatorioParaTutor(Consolidado $consolidado, int $diasRestantes): int
    {
        $usuarioTutor = $consolidado->tutor?->usuario;

        if (! $this->usuarioPuedeRecibirNotificaciones($usuarioTutor)) {
            return 0;
        }

        $textoDias = match ($diasRestantes) {
            0 => 'hoy',
            1 => 'mañana',
            default => "en {$diasRestantes} días",
        };

        $periodo = $consolidado->periodoEvaluacion;

        return $this->crearSiNoExiste(
            usuario: $usuarioTutor,
            tipo: 'consolidado_recordatorio_tutor',
            consolidado: $consolidado,
            titulo: 'Recordatorio de entrega de consolidado',
            mensaje: "El consolidado del periodo {$periodo->nombre} vence {$textoDias}. Revise sus casos y entregue el consolidado cuando esté completo."
        );
    }

    private function crearSiNoExiste(
        User $usuario,
        string $tipo,
        Consolidado $consolidado,
        string $titulo,
        string $mensaje
    ): int {
        $notificacion = Notificacion::query()->firstOrCreate(
            [
                'usuario_id' => $usuario->id,
                'tipo' => $tipo,
                'modelo_tipo' => Consolidado::class,
                'modelo_id' => $consolidado->id,
            ],
            [
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'leido' => false,
                'leido_en' => null,
            ]
        );

        return $notificacion->wasRecentlyCreated ? 1 : 0;
    }

    private function resolverNotificacionesDeConsolidadosEntregados(): int
    {
        return Notificacion::query()
            ->whereIn('tipo', [
                'consolidado_atrasado_tutor',
                'consolidado_atrasado_coordinacion',
                'consolidado_recordatorio_tutor',
            ])
            ->where('modelo_tipo', Consolidado::class)
            ->where('leido', false)
            ->whereExists(function ($query) {
                $query->selectRaw('1')
                    ->from('consolidados')
                    ->whereColumn('consolidados.id', 'notificaciones.modelo_id')
                    ->where('consolidados.estado_entrega', 'entregado');
            })
            ->update([
                'leido' => true,
                'leido_en' => now(),
                'updated_at' => now(),
            ]);
    }

    private function usuarioPuedeRecibirNotificaciones(?User $usuario): bool
    {
        return $usuario !== null && $usuario->activo;
    }

    private function mensajeAtrasoTutor(Consolidado $consolidado): string
    {
        $periodo = $consolidado->periodoEvaluacion;
        $fechaLimite = $periodo->fecha_limite_consolidado->format('d/m/Y');

        return "El consolidado del periodo {$periodo->nombre} venció el {$fechaLimite}. Debe completar los casos pendientes y entregar el consolidado.";
    }

    private function mensajeAtrasoCoordinacion(Consolidado $consolidado): string
    {
        $periodo = $consolidado->periodoEvaluacion;
        $tutor = $consolidado->tutor;
        $fechaLimite = $periodo->fecha_limite_consolidado->format('d/m/Y');

        return "El tutor {$tutor->nombre_completo} tiene pendiente el consolidado del periodo {$periodo->nombre}. La fecha límite fue {$fechaLimite}.";
    }
}
