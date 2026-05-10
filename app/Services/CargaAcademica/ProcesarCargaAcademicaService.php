<?php

namespace App\Services\CargaAcademica;

use App\Models\Ciclo;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class ProcesarCargaAcademicaService
{
    public function __construct(
        private readonly NormalizarTextoService $normalizador,
        private readonly DetectarEncabezadoService $detectorEncabezado,
        private readonly MapearColumnasCargaAcademicaService $mapeadorColumnas,
        private readonly InterpretarModalidadService $interpreteModalidad,
        private readonly InterpretarHorarioService $interpreteHorario,
        private readonly GuardarCargaAcademicaService $guardador
    ) {}

    public function procesarHoja(
        string $nombreHoja,
        Collection $filas,
        Ciclo $ciclo,
        ResultadoCargaAcademica $resultado
    ): void {
        if (! $this->hojaPermitida($nombreHoja)) {
            return;
        }

        $resultado->registrarHoja($nombreHoja);

        $encabezado = $this->detectorEncabezado->detectar($filas);

        if (! $encabezado['encontrado']) {
            $resultado->registrarError(
                hoja: $nombreHoja,
                fila: 'Encabezado',
                mensaje: 'No se encontraron todas las columnas obligatorias.',
                datos: [
                    'columnas_faltantes' => $encabezado['faltantes'],
                ]
            );

            return;
        }

        $grupos = $this->agruparFilasValidas(
            nombreHoja: $nombreHoja,
            filas: $filas,
            encabezado: $encabezado,
            resultado: $resultado
        );

        foreach ($grupos as $grupo) {
            try {
                $this->guardador->guardar(
                    ciclo: $ciclo,
                    datos: $grupo['datos'],
                    modalidadInterpretada: $grupo['modalidad'],
                    horarios: $grupo['horarios'],
                    resultado: $resultado
                );

                $resultado->incrementar('filas_importadas', $grupo['filas_importadas']);
            } catch (\Throwable $e) {
                $resultado->registrarError(
                    hoja: $nombreHoja,
                    fila: implode(', ', $grupo['filas_excel']),
                    mensaje: 'No se pudo guardar la sección: ' . $e->getMessage(),
                    datos: [
                        'codigo_materia' => $grupo['datos']['codigo_materia'] ?? null,
                        'numero_seccion' => $grupo['datos']['numero_seccion'] ?? null,
                    ]
                );
            }
        }
    }

    private function agruparFilasValidas(
        string $nombreHoja,
        Collection $filas,
        array $encabezado,
        ResultadoCargaAcademica $resultado
    ): array {
        $grupos = [];
        $indiceEncabezado = $encabezado['indice_fila'];
        $mapa = $encabezado['mapa'];

        foreach ($filas->slice($indiceEncabezado + 1) as $indice => $fila) {
            $numeroFilaExcel = $indice + 1;

            if ($this->filaVacia($fila)) {
                continue;
            }

            $datos = $this->normalizarDatosFila(
                $this->mapeadorColumnas->extraerDatosFila($fila, $mapa)
            );

            if ($this->filaNoAcademica($datos)) {
                continue;
            }

            $resultado->incrementar('filas_leidas');

            try {
                $this->validarDatosMinimos($datos);

                $modalidad = $this->interpreteModalidad->interpretar($datos['tipo']);

                if (! $modalidad['importar']) {
                    $resultado->incrementar('filas_ignoradas');
                    $resultado->registrarAdvertencia(
                        hoja: $nombreHoja,
                        fila: $numeroFilaExcel,
                        mensaje: 'Fila ignorada porque el tipo/modalidad no se ocupa para tutorías.',
                        datos: [
                            'tipo' => $modalidad['tipo_original'],
                            'codigo_materia' => $datos['codigo_materia'] ?? null,
                            'numero_seccion' => $datos['numero_seccion'] ?? null,
                        ]
                    );

                    continue;
                }

                $horarios = $this->interpretarHorariosSegunModalidad(
                    modalidad: $modalidad,
                    datos: $datos
                );

                $claveGrupo = $this->claveGrupo($datos);

                if (! isset($grupos[$claveGrupo])) {
                    $grupos[$claveGrupo] = [
                        'datos' => $datos,
                        'modalidad' => $modalidad,
                        'horarios' => [],
                        'filas_excel' => [],
                        'filas_importadas' => 0,
                    ];
                }

                $grupos[$claveGrupo]['datos'] = $this->fusionarDatosSeccion(
                    $grupos[$claveGrupo]['datos'],
                    $datos
                );

                $grupos[$claveGrupo]['horarios'] = $this->fusionarHorarios(
                    $grupos[$claveGrupo]['horarios'],
                    $horarios
                );

                $grupos[$claveGrupo]['filas_excel'][] = $numeroFilaExcel;
                $grupos[$claveGrupo]['filas_importadas']++;
            } catch (\Throwable $e) {
                $resultado->registrarError(
                    hoja: $nombreHoja,
                    fila: $numeroFilaExcel,
                    mensaje: $e->getMessage(),
                    datos: [
                        'codigo_materia' => $datos['codigo_materia'] ?? null,
                        'numero_seccion' => $datos['numero_seccion'] ?? null,
                        'tipo' => $datos['tipo'] ?? null,
                    ]
                );
            }
        }

        return $grupos;
    }

    private function normalizarDatosFila(array $datos): array
    {
        return [
            'codigo_catedra' => $this->normalizador->textoOpcional($datos['codigo_catedra'] ?? null),
            'codigo_materia' => $this->normalizador->codigo((string) ($datos['codigo_materia'] ?? '')),
            'nombre_materia' => $this->normalizador->texto((string) ($datos['nombre_materia'] ?? '')),
            'numero_seccion' => $this->normalizador->texto((string) ($datos['numero_seccion'] ?? '')),
            'horas' => $this->normalizador->textoOpcional($datos['horas'] ?? null),
            'dias' => $this->normalizador->textoOpcional($datos['dias'] ?? null),
            'tipo' => $this->normalizador->texto((string) ($datos['tipo'] ?? '')),
            'docente_titular' => $this->normalizador->texto((string) ($datos['docente_titular'] ?? '')),
            'codigo_docente' => $this->normalizador->textoOpcional($datos['codigo_docente'] ?? null),
            'categoria_docente' => $this->normalizador->textoOpcional($datos['categoria_docente'] ?? null),
            'correo_institucional' => $this->normalizador->correoOpcional($datos['correo_institucional'] ?? null),
            'correo_personal' => $this->normalizador->correoOpcional($datos['correo_personal'] ?? null),
            'observaciones' => $this->normalizador->textoOpcional($datos['observaciones'] ?? null),
            'aula' => $this->normalizador->textoOpcional($datos['aula'] ?? null),
            'capacidad' => $datos['capacidad'] ?? null,
        ];
    }

    private function validarDatosMinimos(array $datos): void
    {
        $requeridos = [
            'codigo_materia' => 'El código de materia es obligatorio.',
            'nombre_materia' => 'El nombre de materia es obligatorio.',
            'numero_seccion' => 'El número de sección es obligatorio.',
            'tipo' => 'El tipo/modalidad es obligatorio.',
            'docente_titular' => 'El docente titular es obligatorio.',
        ];

        foreach ($requeridos as $campo => $mensaje) {
            if (($datos[$campo] ?? '') === '') {
                throw new InvalidArgumentException($mensaje);
            }
        }
    }

    private function interpretarHorariosSegunModalidad(array $modalidad, array $datos): array
    {
        if (! $modalidad['requiere_horario']) {
            return [];
        }

        if (($datos['dias'] ?? null) === null) {
            throw new InvalidArgumentException('Los días son obligatorios para modalidad presencial o en línea.');
        }

        if (($datos['horas'] ?? null) === null) {
            throw new InvalidArgumentException('Las horas son obligatorias para modalidad presencial o en línea.');
        }

        return $this->interpreteHorario->construirHorarios(
            dias: $datos['dias'],
            horas: $datos['horas']
        );
    }

    private function claveGrupo(array $datos): string
    {
        return implode('|', [
            $datos['codigo_materia'],
            $datos['numero_seccion'],
        ]);
    }

    private function fusionarDatosSeccion(array $actuales, array $nuevos): array
    {
        foreach ($nuevos as $campo => $valor) {
            if ($valor !== null && $valor !== '') {
                $actuales[$campo] = $valor;
            }
        }

        return $actuales;
    }

    private function fusionarHorarios(array $actuales, array $nuevos): array
    {
        $horarios = [];

        foreach ([...$actuales, ...$nuevos] as $horario) {
            $clave = $horario['dia_semana'] . '|' . $horario['hora_inicio'] . '|' . $horario['hora_fin'];
            $horarios[$clave] = $horario;
        }

        return array_values($horarios);
    }
    private function filaNoAcademica(array $datos): bool
    {
        $codigoMateria = trim((string) ($datos['codigo_materia'] ?? ''));
        $tipo = trim((string) ($datos['tipo'] ?? ''));

        // Los cuadros resumen al final de algunas hojas traen números en columnas
        // como SEC, pero no traen código de materia ni modalidad. No son secciones.
        if ($codigoMateria === '' && $tipo === '') {
            return true;
        }

        return false;
    }

    private function filaVacia(Collection|array $fila): bool
    {
        return collect($fila)->filter(function ($valor) {
            return trim((string) $valor) !== '';
        })->isEmpty();
    }

    private function hojaPermitida(string $nombreHoja): bool
    {
        $hojaNormalizada = $this->normalizador->encabezado($nombreHoja);

        return collect(config('carga_academica.hojas_permitidas', []))
            ->map(fn(string $hoja) => $this->normalizador->encabezado($hoja))
            ->contains($hojaNormalizada);
    }
}
