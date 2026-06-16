<?php

namespace App\Services\CargaAcademica;

use App\Models\Ciclo;
use App\Models\HorarioSeccion;
use App\Models\Materia;
use App\Models\Seccion;
use Illuminate\Support\Facades\DB;

class GuardarCargaAcademicaService
{
    public function __construct(
        private readonly NormalizarTextoService $normalizador
    ) {}

    public function guardar(
        Ciclo $ciclo,
        array $datos,
        array $modalidadInterpretada,
        array $horarios,
        ResultadoCargaAcademica $resultado
    ): void {
        DB::transaction(function () use ($ciclo, $datos, $modalidadInterpretada, $horarios, $resultado) {
            $materia = $this->guardarMateria($datos, $resultado);

            $seccion = $this->guardarSeccion(
                ciclo: $ciclo,
                materia: $materia,
                datos: $datos,
                modalidadInterpretada: $modalidadInterpretada,
                resultado: $resultado
            );

            $this->reemplazarHorarios($seccion, $horarios, $resultado);
        });
    }

    private function guardarMateria(array $datos, ResultadoCargaAcademica $resultado): Materia
    {
        $codigoMateria = $this->normalizador->codigo($datos['codigo_materia'] ?? '');
        $nombreMateria = $this->normalizador->texto((string) ($datos['nombre_materia'] ?? ''));
        $codigoCatedra = $this->normalizador->textoOpcional($datos['codigo_catedra'] ?? null);

        $materia = Materia::query()
            ->where('codigo', $codigoMateria)
            ->first();

        if (! $materia) {
            $materia = new Materia();
            $materia->codigo = $codigoMateria;
            $materia->nombre = $nombreMateria;
            $materia->creditos = (int) config('carga_academica.reglas.creditos_por_defecto', 3);
            $materia->ciclo_plan = config('carga_academica.reglas.ciclo_plan_por_defecto');
            $materia->departamento = $codigoCatedra;
            $materia->gestionada_por_coordinacion = (bool) config(
                'carga_academica.reglas.materia_nueva_gestionada_por_coordinacion',
                false
            );
            $materia->requiere_revision = (bool) config(
                'carga_academica.reglas.materia_nueva_requiere_revision',
                true
            );
            $materia->activo = true;
            $materia->save();

            $resultado->incrementar('materias_creadas');

            return $materia;
        }

        $materia->nombre = $nombreMateria;

        if (! $materia->departamento && $codigoCatedra) {
            $materia->departamento = $codigoCatedra;
        }

        if (! $materia->activo) {
            $materia->activo = true;
        }

        if ($materia->isDirty()) {
            $materia->save();
            $resultado->incrementar('materias_actualizadas');
        }

        return $materia;
    }

    private function guardarSeccion(
        Ciclo $ciclo,
        Materia $materia,
        array $datos,
        array $modalidadInterpretada,
        ResultadoCargaAcademica $resultado
    ): Seccion {
        $numeroSeccion = $this->normalizador->texto((string) ($datos['numero_seccion'] ?? ''));

        $seccion = Seccion::query()
            ->where('ciclo_id', $ciclo->id)
            ->where('materia_id', $materia->id)
            ->where('numero_seccion', $numeroSeccion)
            ->first();

        $esNueva = false;

        if (! $seccion) {
            $seccion = new Seccion();
            $seccion->ciclo_id = $ciclo->id;
            $seccion->materia_id = $materia->id;
            $seccion->numero_seccion = $numeroSeccion;
            $esNueva = true;
        }

        $seccion->modalidad = $modalidadInterpretada['modalidad'];
        $seccion->requiere_tutor = $materia->gestionada_por_coordinacion
            ? (bool) $modalidadInterpretada['requiere_tutor']
            : false;
        $seccion->aula = $this->normalizador->textoOpcional($datos['aula'] ?? null);
        $seccion->nombre_titular = $this->normalizador->texto((string) ($datos['docente_titular'] ?? ''));
        $seccion->correo_titular = $this->correoValido($datos['correo_institucional'] ?? null);
        $seccion->codigo_docente_titular = $this->normalizador->textoOpcional($datos['codigo_docente'] ?? null);
        $seccion->categoria_docente_titular = $this->normalizador->textoOpcional($datos['categoria_docente'] ?? null);
        $seccion->capacidad = $this->capacidad($datos['capacidad'] ?? null);
        $seccion->observaciones_carga = $this->normalizador->textoOpcional($datos['observaciones'] ?? null);

        $seccion->save();

        if ($esNueva) {
            $resultado->incrementar('secciones_creadas');
        } else {
            $resultado->incrementar('secciones_actualizadas');
        }

        return $seccion;
    }

    private function reemplazarHorarios(
        Seccion $seccion,
        array $horarios,
        ResultadoCargaAcademica $resultado
    ): void {
        if ((bool) config('carga_academica.reglas.reemplazar_horarios_en_reimportacion', true)) {
            $seccion->horarios()->delete();
        }

        foreach ($horarios as $horarioDatos) {
            $horario = new HorarioSeccion();
            $horario->seccion_id = $seccion->id;
            $horario->dia_semana = $horarioDatos['dia_semana'];
            $horario->hora_inicio = $horarioDatos['hora_inicio'];
            $horario->hora_fin = $horarioDatos['hora_fin'];
            $horario->save();

            $resultado->incrementar('horarios_creados');
        }
    }

    private function capacidad(mixed $valor): int
    {
        if (is_numeric($valor) && (int) $valor > 0) {
            return (int) $valor;
        }

        return 35;
    }

    private function correoValido(mixed $valor): ?string
    {
        $correo = $this->normalizador->correoOpcional($valor);

        if ($correo === null) {
            return null;
        }

        return filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : null;
    }
}
