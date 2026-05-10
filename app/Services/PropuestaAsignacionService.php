<?php

namespace App\Services;

use App\Models\Ciclo;
use App\Models\HistorialCambioPropuesta;
use App\Models\ItemPropuesta;
use App\Models\PropuestaAsignacion;
use App\Models\Seccion;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PropuestaAsignacionService
{
    public function __construct(
        private readonly HorarioService $horarioService
    ) {}

    public function obtenerCicloActivo(): Ciclo
    {
        $ciclo = Ciclo::query()
            ->where('activo', true)
            ->first();

        if (! $ciclo) {
            throw ValidationException::withMessages([
                'ciclo_id' => 'No hay un ciclo académico activo. Activa un ciclo antes de crear la propuesta.',
            ]);
        }

        return $ciclo;
    }

    public function obtenerOCrearParaCicloActivo(int $usuarioId): PropuestaAsignacion
    {
        $ciclo = $this->obtenerCicloActivo();

        $propuesta = PropuestaAsignacion::query()
            ->firstOrCreate(
                ['ciclo_id' => $ciclo->id],
                [
                    'creado_por' => $usuarioId,
                    'estado_aprobacion' => 'pendiente',
                    'publicado' => false,
                    'version' => 1,
                ]
            );

        return $this->cargarDetalle($propuesta);
    }

    public function cargarDetalle(PropuestaAsignacion $propuesta): PropuestaAsignacion
    {
        return $propuesta->load([
            'ciclo',
            'items.tutor',
            'items.seccion.materia',
            'items.seccion.horarios',
            'historialCambios.modificadoPor',
        ]);
    }

    public function seccionesCandidatas(Ciclo $ciclo): Collection
    {
        return Seccion::query()
            ->with(['materia', 'horarios'])
            ->join('materias', 'materias.id', '=', 'secciones.materia_id')
            ->where('secciones.ciclo_id', $ciclo->id)
            ->where('secciones.requiere_tutor', true)
            ->where('materias.gestionada_por_coordinacion', true)
            ->where('materias.activo', true)
            ->select('secciones.*')
            ->orderBy('materias.nombre')
            ->orderBy('secciones.numero_seccion')
            ->get();
    }

    public function tutoresElegibles(): Collection
    {
        return Tutor::query()
            ->where('activo', true)
            ->where('tiempo_completo', true)
            ->orderBy('nombre_completo')
            ->get();
    }

    public function asignarTutor(
        PropuestaAsignacion $propuesta,
        int $seccionId,
        int $tutorId,
        ?string $observaciones,
        int $usuarioId
    ): ItemPropuesta {
        return DB::transaction(function () use (
            $propuesta,
            $seccionId,
            $tutorId,
            $observaciones,
            $usuarioId
        ) {
            $propuesta->refresh();

            $seccion = $this->validarSeccionCandidata($propuesta, $seccionId);
            $tutor = $this->validarTutorElegible($tutorId);

            $itemExistente = ItemPropuesta::query()
                ->where('propuesta_asignacion_id', $propuesta->id)
                ->where('seccion_id', $seccion->id)
                ->first();

            $choques = $this->horarioService->obtenerChoques(
                tutorId: $tutor->id,
                seccionId: $seccion->id,
                propuestaId: $propuesta->id,
                itemIgnoradoId: $itemExistente?->id
            );

            if (! empty($choques)) {
                throw ValidationException::withMessages([
                    'tutor_id' => $this->horarioService->mensajeChoque($choques),
                ]);
            }

            $prioridad = $this->calcularPrioridad($seccion);

            if ($itemExistente) {
                $datosAnteriores = $this->snapshotItem($itemExistente);

                $cambioTutor = (int) $itemExistente->tutor_id !== (int) $tutor->id;

                $itemExistente->update([
                    'tutor_id' => $tutor->id,
                    'prioridad' => $prioridad,
                    'observaciones' => $observaciones,
                ]);

                $itemExistente->refresh();

                if ($cambioTutor) {
                    $this->reactivarAprobacionSiAplica($propuesta);

                    $this->registrarHistorial(
                        propuesta: $propuesta,
                        usuarioId: $usuarioId,
                        tipoCambio: 'tutor_reemplazado',
                        descripcion: 'Se reemplazó el tutor asignado a una sección de la propuesta.',
                        datosAnteriores: $datosAnteriores,
                        datosNuevos: $this->snapshotItem($itemExistente)
                    );
                } else {
                    $this->registrarHistorial(
                        propuesta: $propuesta,
                        usuarioId: $usuarioId,
                        tipoCambio: 'ajuste_coordinacion',
                        descripcion: 'Se actualizó una asignación de la propuesta.',
                        datosAnteriores: $datosAnteriores,
                        datosNuevos: $this->snapshotItem($itemExistente)
                    );
                }

                return $itemExistente;
            }

            $item = ItemPropuesta::create([
                'propuesta_asignacion_id' => $propuesta->id,
                'tutor_id' => $tutor->id,
                'seccion_id' => $seccion->id,
                'prioridad' => $prioridad,
                'observaciones' => $observaciones,
            ]);

            $this->reactivarAprobacionSiAplica($propuesta);

            $this->registrarHistorial(
                propuesta: $propuesta,
                usuarioId: $usuarioId,
                tipoCambio: 'item_agregado',
                descripcion: 'Se agregó una asignación tutor-sección a la propuesta.',
                datosAnteriores: null,
                datosNuevos: $this->snapshotItem($item)
            );

            return $item;
        });
    }

    public function quitarItem(
        ItemPropuesta $item,
        int $usuarioId
    ): void {
        DB::transaction(function () use ($item, $usuarioId) {
            $item->load(['propuestaAsignacion', 'tutor', 'seccion.materia']);

            $propuesta = $item->propuestaAsignacion;
            $datosAnteriores = $this->snapshotItem($item);

            $item->delete();

            $this->reactivarAprobacionSiAplica($propuesta);

            $this->registrarHistorial(
                propuesta: $propuesta,
                usuarioId: $usuarioId,
                tipoCambio: 'item_eliminado',
                descripcion: 'Se eliminó una asignación tutor-sección de la propuesta.',
                datosAnteriores: $datosAnteriores,
                datosNuevos: null
            );
        });
    }

    public function registrarRespuestaDecano(
        PropuestaAsignacion $propuesta,
        string $estadoAprobacion,
        ?string $observaciones,
        string $fechaRespuesta,
        int $usuarioId
    ): PropuestaAsignacion {
        if (! in_array($estadoAprobacion, ['aprobado', 'requiere_ajustes'], true)) {
            throw ValidationException::withMessages([
                'estado_aprobacion' => 'La respuesta del Decano no es válida.',
            ]);
        }

        return DB::transaction(function () use (
            $propuesta,
            $estadoAprobacion,
            $observaciones,
            $fechaRespuesta,
            $usuarioId
        ) {
            $datosAnteriores = [
                'estado_aprobacion' => $propuesta->estado_aprobacion,
                'observaciones_decano' => $propuesta->observaciones_decano,
                'fecha_respuesta_decano' => $propuesta->fecha_respuesta_decano?->format('Y-m-d'),
                'publicado' => $propuesta->publicado,
            ];

            $propuesta->update([
                'estado_aprobacion' => $estadoAprobacion,
                'observaciones_decano' => $observaciones,
                'fecha_respuesta_decano' => $fechaRespuesta,
                'respuesta_registrada_por' => $usuarioId,
                'publicado' => false,
            ]);

            $this->registrarHistorial(
                propuesta: $propuesta,
                usuarioId: $usuarioId,
                tipoCambio: 'observacion_decano',
                descripcion: 'Se registró la respuesta del Decano.',
                datosAnteriores: $datosAnteriores,
                datosNuevos: [
                    'estado_aprobacion' => $propuesta->estado_aprobacion,
                    'observaciones_decano' => $propuesta->observaciones_decano,
                    'fecha_respuesta_decano' => $propuesta->fecha_respuesta_decano?->format('Y-m-d'),
                    'publicado' => $propuesta->publicado,
                ]
            );

            return $this->cargarDetalle($propuesta);
        });
    }

    public function publicar(
        PropuestaAsignacion $propuesta,
        int $usuarioId
    ): PropuestaAsignacion {
        return DB::transaction(function () use ($propuesta, $usuarioId) {
            if ($propuesta->publicado) {
                throw ValidationException::withMessages([
                    'publicado' => 'La propuesta ya está publicada.',
                ]);
            }

            if ($propuesta->estado_aprobacion !== 'aprobado') {
                throw ValidationException::withMessages([
                    'publicado' => 'No se puede publicar una propuesta que no ha sido aprobada por el Decano.',
                ]);
            }

            if ($propuesta->items()->count() === 0) {
                throw ValidationException::withMessages([
                    'items' => 'No se puede publicar una propuesta sin asignaciones.',
                ]);
            }

            $datosAnteriores = [
                'publicado' => $propuesta->publicado,
                'estado_aprobacion' => $propuesta->estado_aprobacion,
            ];

            $propuesta->update([
                'publicado' => true,
            ]);

            $this->registrarHistorial(
                propuesta: $propuesta,
                usuarioId: $usuarioId,
                tipoCambio: 'publicado',
                descripcion: 'Se publicó la propuesta de asignación para los tutores.',
                datosAnteriores: $datosAnteriores,
                datosNuevos: [
                    'publicado' => $propuesta->publicado,
                    'estado_aprobacion' => $propuesta->estado_aprobacion,
                ]
            );

            return $this->cargarDetalle($propuesta);
        });
    }

    private function validarSeccionCandidata(
        PropuestaAsignacion $propuesta,
        int $seccionId
    ): Seccion {
        $seccion = Seccion::query()
            ->with(['materia', 'horarios'])
            ->findOrFail($seccionId);

        if ((int) $seccion->ciclo_id !== (int) $propuesta->ciclo_id) {
            throw ValidationException::withMessages([
                'seccion_id' => 'La sección seleccionada no pertenece al ciclo de la propuesta.',
            ]);
        }

        if (! $seccion->puedeEntrarEnPropuesta()) {
            throw ValidationException::withMessages([
                'seccion_id' => 'La sección seleccionada no está habilitada para propuesta de tutorías.',
            ]);
        }

        return $seccion;
    }

    private function validarTutorElegible(int $tutorId): Tutor
    {
        $tutor = Tutor::query()->findOrFail($tutorId);

        if (! $tutor->activo || ! $tutor->tiempo_completo) {
            throw ValidationException::withMessages([
                'tutor_id' => 'Solo se pueden asignar tutores DTC activos.',
            ]);
        }

        return $tutor;
    }

    private function calcularPrioridad(Seccion $seccion): bool
    {
        $cicloPlan = $seccion->materia?->ciclo_plan;

        return $cicloPlan !== null && (int) $cicloPlan <= 2;
    }

    private function reactivarAprobacionSiAplica(PropuestaAsignacion $propuesta): void
    {
        $propuesta->refresh();

        if ($propuesta->estado_aprobacion === 'pendiente' && ! $propuesta->publicado) {
            return;
        }

        $propuesta->update([
            'estado_aprobacion' => 'pendiente',
            'publicado' => false,
            'version' => ((int) $propuesta->version) + 1,
        ]);
    }

    private function registrarHistorial(
        PropuestaAsignacion $propuesta,
        int $usuarioId,
        string $tipoCambio,
        string $descripcion,
        ?array $datosAnteriores,
        ?array $datosNuevos
    ): void {
        HistorialCambioPropuesta::create([
            'propuesta_asignacion_id' => $propuesta->id,
            'modificado_por' => $usuarioId,
            'tipo_cambio' => $tipoCambio,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
        ]);
    }

    private function snapshotItem(ItemPropuesta $item): array
    {
        $item->loadMissing(['tutor', 'seccion.materia']);

        return [
            'item_id' => $item->id,
            'tutor_id' => $item->tutor_id,
            'tutor' => $item->tutor?->nombre_completo,
            'seccion_id' => $item->seccion_id,
            'numero_seccion' => $item->seccion?->numero_seccion,
            'materia_id' => $item->seccion?->materia_id,
            'materia_codigo' => $item->seccion?->materia?->codigo,
            'materia_nombre' => $item->seccion?->materia?->nombre,
            'prioridad' => (bool) $item->prioridad,
            'observaciones' => $item->observaciones,
        ];
    }
}
