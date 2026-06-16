<?php

namespace App\Services;

use App\Models\HistorialCambioPropuesta;
use App\Models\ItemPropuesta;
use App\Models\Seccion;
use App\Models\Tutor;
use App\Models\Materia;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SeccionService
{

    public function crear(Materia $materia, array $datos, int $usuarioId): Seccion
    {
        return DB::transaction(function () use ($materia, $datos) {
            $seccion = new Seccion();
            $seccion->ciclo_id = (int) $datos['ciclo_id'];
            $seccion->materia_id = $materia->id;
            $seccion->numero_seccion = $datos['numero_seccion'];
            $seccion->modalidad = $datos['modalidad'];
            $seccion->requiere_tutor = $materia->gestionada_por_coordinacion
                ? (bool) ($datos['requiere_tutor'] ?? false)
                : false;
            $seccion->aula = $datos['aula'] ?? null;
            $seccion->nombre_titular = $datos['nombre_titular'];
            $seccion->correo_titular = $datos['correo_titular'] ?? null;
            $seccion->codigo_docente_titular = $datos['codigo_docente_titular'] ?? null;
            $seccion->categoria_docente_titular = $datos['categoria_docente_titular'] ?? null;
            $seccion->capacidad = 35;
            $seccion->observaciones_carga = $datos['observaciones_carga'] ?? null;

            $horarios = $datos['horarios'] ?? [];

            if ($seccion->modalidad === 'virtual') {
                $horarios = [];
            }

            $seccion->save();

            foreach ($horarios as $horario) {
                $seccion->horarios()->create([
                    'dia_semana' => (int) $horario['dia_semana'],
                    'hora_inicio' => $horario['hora_inicio'],
                    'hora_fin' => $horario['hora_fin'],
                ]);
            }

            return $seccion->load(['materia', 'ciclo', 'horarios']);
        });
    }

    public function actualizar(Seccion $seccion, array $datos, int $usuarioId): Seccion
    {
        return DB::transaction(function () use ($seccion, $datos, $usuarioId) {
            $seccion->load([
                'materia',
                'ciclo',
                'horarios',
                'itemsPropuesta.tutor',
                'itemsPropuesta.propuestaAsignacion',
            ]);

            $datosSeccion = [
                'numero_seccion' => $datos['numero_seccion'],
                'modalidad' => $datos['modalidad'],
                'requiere_tutor' => (bool) ($datos['requiere_tutor'] ?? false),
                'aula' => $datos['aula'] ?? null,
                'nombre_titular' => $datos['nombre_titular'],
                'correo_titular' => $datos['correo_titular'] ?? null,
                'codigo_docente_titular' => $datos['codigo_docente_titular'] ?? null,
                'categoria_docente_titular' => $datos['categoria_docente_titular'] ?? null,
                'observaciones_carga' => $datos['observaciones_carga'] ?? null,
            ];

            if (! $seccion->materia?->gestionada_por_coordinacion) {
                $datosSeccion['requiere_tutor'] = false;
            }

            $horarios = $datos['horarios'] ?? [];

            if ($datosSeccion['modalidad'] === 'virtual') {
                $horarios = [];
            }

            $this->validarImpacto(
                seccion: $seccion,
                datosNuevos: $datosSeccion,
                horariosNuevos: $horarios
            );

            $datosAnteriores = $this->snapshotSeccion($seccion);

            $seccion->update($datosSeccion);

            $seccion->horarios()->delete();

            foreach ($horarios as $horario) {
                $seccion->horarios()->create([
                    'dia_semana' => (int) $horario['dia_semana'],
                    'hora_inicio' => $horario['hora_inicio'],
                    'hora_fin' => $horario['hora_fin'],
                ]);
            }

            $seccion->refresh();
            $seccion->load([
                'materia',
                'ciclo',
                'horarios',
                'itemsPropuesta.tutor',
                'itemsPropuesta.propuestaAsignacion',
            ]);

            $this->registrarHistorialSiTienePropuesta(
                seccion: $seccion,
                usuarioId: $usuarioId,
                datosAnteriores: $datosAnteriores,
                datosNuevos: $this->snapshotSeccion($seccion)
            );

            return $seccion;
        });
    }

    private function validarImpacto(
        Seccion $seccion,
        array $datosNuevos,
        array $horariosNuevos
    ): void {
        $tieneCasos = $seccion->casosSeguimiento()->exists();
        $tienePropuesta = $seccion->itemsPropuesta()->exists();




        $numeroCambio = (string) $seccion->numero_seccion !== (string) $datosNuevos['numero_seccion'];
        $modalidadCambio = (string) $seccion->modalidad !== (string) $datosNuevos['modalidad'];
        $requiereTutorCambio = (bool) $seccion->requiere_tutor !== (bool) $datosNuevos['requiere_tutor'];

        if ($numeroCambio && $tieneCasos) {
            throw ValidationException::withMessages([
                'numero_seccion' => 'No se puede cambiar el número de sección porque ya existen casos registrados. Esto preserva la trazabilidad del seguimiento.',
            ]);
        }

        if ($datosNuevos['modalidad'] === 'virtual' && ($tienePropuesta || $tieneCasos)) {
            throw ValidationException::withMessages([
                'modalidad' => 'No se puede cambiar a modalidad virtual porque la sección ya tiene propuesta o casos registrados.',
            ]);
        }

        if (! $datosNuevos['requiere_tutor'] && ($tienePropuesta || $tieneCasos)) {
            throw ValidationException::withMessages([
                'requiere_tutor' => 'No se puede quitar el requerimiento de tutor porque la sección ya tiene propuesta o casos registrados.',
            ]);
        }

        if (($modalidadCambio || $requiereTutorCambio) && $tieneCasos) {
            throw ValidationException::withMessages([
                'modalidad' => 'No se puede cambiar modalidad o requerimiento de tutor porque ya existen casos registrados.',
            ]);
        }

        $itemsAsignados = $seccion->itemsPropuesta()
            ->with(['tutor', 'propuestaAsignacion'])
            ->whereNotNull('tutor_id')
            ->get();

        if ($itemsAsignados->isEmpty()) {
            return;
        }

        foreach ($itemsAsignados as $item) {
            if (! $item->tutor) {
                continue;
            }

            $this->validarTutorNoSeaTitular(
                tutor: $item->tutor,
                codigoDocenteTitular: $datosNuevos['codigo_docente_titular'] ?? null
            );

            $this->validarChoquesConAsignacionesDelTutor(
                itemActual: $item,
                horariosNuevos: $horariosNuevos
            );

            $this->validarChoquesConCargaDocenteDelTutor(
                seccionActual: $seccion,
                tutor: $item->tutor,
                horariosNuevos: $horariosNuevos
            );
        }
    }

    private function validarTutorNoSeaTitular(Tutor $tutor, ?string $codigoDocenteTitular): void
    {
        $codigoTutor = strtoupper(trim((string) $tutor->codigo_empleado));
        $codigoTitular = strtoupper(trim((string) $codigoDocenteTitular));

        if ($codigoTutor !== '' && $codigoTutor === $codigoTitular) {
            throw ValidationException::withMessages([
                'codigo_docente_titular' => 'El tutor asignado no puede quedar registrado como docente titular de la misma sección.',
            ]);
        }
    }

    private function validarChoquesConAsignacionesDelTutor(
        ItemPropuesta $itemActual,
        array $horariosNuevos
    ): void {
        if (empty($horariosNuevos) || ! $itemActual->propuestaAsignacion) {
            return;
        }

        $itemsDelTutor = ItemPropuesta::query()
            ->with(['seccion.materia', 'seccion.horarios'])
            ->where('propuesta_asignacion_id', $itemActual->propuesta_asignacion_id)
            ->where('tutor_id', $itemActual->tutor_id)
            ->whereKeyNot($itemActual->id)
            ->get();

        foreach ($itemsDelTutor as $item) {
            $seccionComparada = $item->seccion;

            if (! $seccionComparada) {
                continue;
            }

            foreach ($horariosNuevos as $horarioNuevo) {
                foreach ($seccionComparada->horarios as $horarioExistente) {
                    if ((int) $horarioNuevo['dia_semana'] !== (int) $horarioExistente->dia_semana) {
                        continue;
                    }

                    if (! $this->seTraslapan(
                        $horarioNuevo['hora_inicio'],
                        $horarioNuevo['hora_fin'],
                        $horarioExistente->hora_inicio,
                        $horarioExistente->hora_fin
                    )) {
                        continue;
                    }

                    throw ValidationException::withMessages([
                        'horarios' => sprintf(
                            'El nuevo horario choca con otra tutoría del mismo tutor: %s %s, %s-%s.',
                            $seccionComparada->materia?->codigo ?? 'Materia',
                            $seccionComparada->numero_seccion,
                            $this->formatearHora($horarioExistente->hora_inicio),
                            $this->formatearHora($horarioExistente->hora_fin)
                        ),
                    ]);
                }
            }
        }
    }

    private function validarChoquesConCargaDocenteDelTutor(
        Seccion $seccionActual,
        Tutor $tutor,
        array $horariosNuevos
    ): void {
        if (empty($horariosNuevos)) {
            return;
        }

        $codigoTutor = strtoupper(trim((string) $tutor->codigo_empleado));

        if ($codigoTutor === '') {
            return;
        }

        $seccionesDocente = Seccion::query()
            ->with(['materia', 'horarios'])
            ->where('ciclo_id', $seccionActual->ciclo_id)
            ->where('codigo_docente_titular', $codigoTutor)
            ->whereKeyNot($seccionActual->id)
            ->whereHas('horarios')
            ->get();

        foreach ($seccionesDocente as $seccionDocente) {
            foreach ($horariosNuevos as $horarioNuevo) {
                foreach ($seccionDocente->horarios as $horarioDocente) {
                    if ((int) $horarioNuevo['dia_semana'] !== (int) $horarioDocente->dia_semana) {
                        continue;
                    }

                    if (! $this->seTraslapan(
                        $horarioNuevo['hora_inicio'],
                        $horarioNuevo['hora_fin'],
                        $horarioDocente->hora_inicio,
                        $horarioDocente->hora_fin
                    )) {
                        continue;
                    }

                    throw ValidationException::withMessages([
                        'horarios' => sprintf(
                            'El nuevo horario choca con una clase que el tutor asignado imparte como docente titular: %s %s, %s-%s.',
                            $seccionDocente->materia?->codigo ?? 'Materia',
                            $seccionDocente->numero_seccion,
                            $this->formatearHora($horarioDocente->hora_inicio),
                            $this->formatearHora($horarioDocente->hora_fin)
                        ),
                    ]);
                }
            }
        }
    }

    private function registrarHistorialSiTienePropuesta(
        Seccion $seccion,
        int $usuarioId,
        array $datosAnteriores,
        array $datosNuevos
    ): void {
        $items = $seccion->itemsPropuesta()
            ->with('propuestaAsignacion')
            ->get();

        foreach ($items as $item) {
            if (! $item->propuestaAsignacion) {
                continue;
            }

            HistorialCambioPropuesta::create([
                'propuesta_asignacion_id' => $item->propuesta_asignacion_id,
                'modificado_por' => $usuarioId,
                'tipo_cambio' => 'ajuste_coordinacion',
                'descripcion' => 'Se actualizó información académica u horario de una sección relacionada con la propuesta.',
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $datosNuevos,
            ]);
        }
    }

    private function snapshotSeccion(Seccion $seccion): array
    {
        $seccion->loadMissing(['materia', 'ciclo', 'horarios']);

        return [
            'seccion_id' => $seccion->id,
            'ciclo_id' => $seccion->ciclo_id,
            'ciclo' => $seccion->ciclo?->nombre,
            'materia_id' => $seccion->materia_id,
            'materia_codigo' => $seccion->materia?->codigo,
            'materia_nombre' => $seccion->materia?->nombre,
            'numero_seccion' => $seccion->numero_seccion,
            'modalidad' => $seccion->modalidad,
            'requiere_tutor' => (bool) $seccion->requiere_tutor,
            'aula' => $seccion->aula,
            'nombre_titular' => $seccion->nombre_titular,
            'correo_titular' => $seccion->correo_titular,
            'codigo_docente_titular' => $seccion->codigo_docente_titular,
            'categoria_docente_titular' => $seccion->categoria_docente_titular,
            'observaciones_carga' => $seccion->observaciones_carga,
            'horarios' => $seccion->horarios
                ->sortBy([
                    ['dia_semana', 'asc'],
                    ['hora_inicio', 'asc'],
                ])
                ->map(fn($horario) => [
                    'dia_semana' => (int) $horario->dia_semana,
                    'hora_inicio' => $this->formatearHora($horario->hora_inicio),
                    'hora_fin' => $this->formatearHora($horario->hora_fin),
                ])
                ->values()
                ->all(),
        ];
    }

    private function seTraslapan(
        string $inicioA,
        string $finA,
        string $inicioB,
        string $finB
    ): bool {
        $inicioA = Carbon::parse($inicioA);
        $finA = Carbon::parse($finA);
        $inicioB = Carbon::parse($inicioB);
        $finB = Carbon::parse($finB);

        return $inicioA->lt($finB) && $inicioB->lt($finA);
    }

    private function formatearHora(string $hora): string
    {
        return Carbon::parse($hora)->format('H:i');
    }
}
