<?php

namespace App\Services\CargaAcademica;

use Illuminate\Support\Str;

class NormalizarTextoService
{
    public function texto(?string $valor): string
    {
        $texto = trim((string) $valor);
        $texto = preg_replace('/\s+/u', ' ', $texto);

        return $texto ?? '';
    }

    public function encabezado(?string $valor): string
    {
        $texto = (string) $valor;

        $texto = str_replace(["\r", "\n", "\t"], ' ', $texto);
        $texto = Str::ascii($texto);
        $texto = mb_strtoupper($texto, 'UTF-8');
        $texto = preg_replace('/\s+/u', ' ', $texto);

        return trim($texto ?? '');
    }

    public function codigo(?string $valor): string
    {
        return mb_strtoupper($this->texto($valor), 'UTF-8');
    }

    public function claveConfig(?string $valor): string
    {
        $texto = $this->encabezado($valor);

        return preg_replace('/[^A-Z0-9]+/', '', $texto) ?? '';
    }

    public function modalidad(?string $valor): string
    {
        $texto = $this->encabezado($valor);
        $texto = str_replace(' ', '', $texto);

        return $texto;
    }

    public function dia(?string $valor): string
    {
        $texto = $this->encabezado($valor);
        $texto = str_replace('.', '', $texto);

        return $texto;
    }

    public function correoOpcional(mixed $valor): ?string
    {
        $texto = strtolower($this->texto((string) $valor));

        return $texto === '' ? null : $texto;
    }

    public function textoOpcional(mixed $valor): ?string
    {
        $texto = $this->texto((string) $valor);

        return $texto === '' ? null : $texto;
    }
}
