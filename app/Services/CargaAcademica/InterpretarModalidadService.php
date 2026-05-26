<?php

namespace App\Services\CargaAcademica;

use InvalidArgumentException;

class InterpretarModalidadService
{
    public function __construct(
        private readonly NormalizarTextoService $normalizador
    ) {}

    public function interpretar(mixed $valor): array
    {
        $tipoOriginal = $this->normalizador->texto((string) $valor);
        $tipo = $this->normalizador->modalidad($tipoOriginal);

        if ($tipo === '') {
            throw new InvalidArgumentException('El tipo/modalidad está vacío.');
        }

        $modalidades = config('carga_academica.modalidades', []);

        if (isset($modalidades[$tipo])) {
            return $this->respuesta($tipoOriginal, $tipo, $modalidades[$tipo]);
        }

        $tipoSinPuntos = str_replace('.', '', $tipo);

        if (isset($modalidades[$tipoSinPuntos])) {
            return $this->respuesta($tipoOriginal, $tipoSinPuntos, $modalidades[$tipoSinPuntos]);
        }

        throw new InvalidArgumentException("El tipo/modalidad [{$tipoOriginal}] no está configurado.");
    }

    private function respuesta(string $tipoOriginal, string $tipoNormalizado, array $configuracion): array
    {
        return [
            'tipo_original' => $tipoOriginal,
            'tipo_normalizado' => $tipoNormalizado,
            'modalidad' => $configuracion['modalidad'],
            'requiere_tutor' => (bool) $configuracion['requiere_tutor'],
            'importar' => (bool) $configuracion['importar'],
            'requiere_horario' => (bool) $configuracion['requiere_horario'],
        ];
    }
}
