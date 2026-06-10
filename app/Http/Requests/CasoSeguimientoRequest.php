<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CasoSeguimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'tutor';
    }

    protected function prepareForValidation(): void
    {
        $carneNormalizado = null;
        $correoNormalizado = null;

        if ($this->filled('carne')) {
            $digitosCarne = $this->digitosCarne((string) $this->input('carne'));

            if (strlen($digitosCarne) === 10) {
                $carneNormalizado = $this->formatearCarne($digitosCarne);
                $correoNormalizado = $this->correoDesdeCarne($digitosCarne);
            } else {
                $carneNormalizado = strtoupper(trim((string) $this->input('carne')));
            }
        }

        if ($this->filled('correo')) {
            $correoNormalizado = strtolower(trim((string) $this->input('correo')));
        }

        $merge = [];

        if ($carneNormalizado !== null) {
            $merge['carne'] = $carneNormalizado;
        }

        if ($correoNormalizado !== null) {
            $merge['correo'] = $correoNormalizado;
        }

        if ($this->filled('nombres')) {
            $merge['nombres'] = trim((string) $this->input('nombres'));
        }

        if ($this->filled('apellidos')) {
            $merge['apellidos'] = trim((string) $this->input('apellidos'));
        }

        $nombres = trim((string) ($merge['nombres'] ?? $this->input('nombres')));
        $apellidos = trim((string) ($merge['apellidos'] ?? $this->input('apellidos')));

        if ($nombres !== '' || $apellidos !== '') {
            $merge['nombre_completo'] = trim($nombres . ' ' . $apellidos);
        }

        if ($this->filled('carrera')) {
            $merge['carrera'] = trim((string) $this->input('carrera'));
        }

        if (! empty($merge)) {
            $this->merge($merge);
        }
    }

    public function rules(): array
    {
        return [
            'seccion_id' => [
                'required',
                'integer',
                'exists:secciones,id',
            ],
            'carne' => [
                'required',
                'string',
                'max:20',
            ],
            'nombres' => [
                'required',
                'string',
                'max:100',
            ],
            'apellidos' => [
                'required',
                'string',
                'max:100',
            ],
            'nombre_completo' => [
                'required',
                'string',
                'max:200',
            ],
            'correo' => [
                'required',
                'email',
                'max:191',
            ],
            'carrera' => [
                'nullable',
                'string',
                'max:150',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $digitosCarne = $this->digitosCarne((string) $this->input('carne'));

            if (strlen($digitosCarne) !== 10) {
                $validator->errors()->add(
                    'carne',
                    'El carné debe contener exactamente 10 dígitos. Ejemplo: 27-4855-2026.'
                );

                return;
            }

            $anioIngreso = (int) substr($digitosCarne, 6, 4);
            $anioActual = now()->year;

            if ($anioIngreso < 1990 || $anioIngreso > $anioActual) {
                $validator->errors()->add(
                    'carne',
                    'El año de ingreso del carné no es válido.'
                );
            }

            $correoEsperado = $this->correoDesdeCarne($digitosCarne);
            $correoIngresado = strtolower(trim((string) $this->input('correo')));

            if ($correoIngresado !== $correoEsperado) {
                $validator->errors()->add(
                    'correo',
                    "El correo institucional debe ser {$correoEsperado}."
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'seccion_id.required' => 'Debe seleccionar una sección.',
            'seccion_id.integer' => 'La sección seleccionada no es válida.',
            'seccion_id.exists' => 'La sección seleccionada no existe.',

            'carne.required' => 'El carné del estudiante es obligatorio.',
            'carne.string' => 'El carné debe ser texto.',
            'carne.max' => 'El carné no debe superar los 20 caracteres.',

            'nombres.required' => 'Los nombres del estudiante son obligatorios.',
            'nombres.string' => 'Los nombres deben ser texto.',
            'nombres.max' => 'Los nombres no deben superar los 100 caracteres.',

            'apellidos.required' => 'Los apellidos del estudiante son obligatorios.',
            'apellidos.string' => 'Los apellidos deben ser texto.',
            'apellidos.max' => 'Los apellidos no deben superar los 100 caracteres.',

            'nombre_completo.required' => 'No se pudo construir el nombre completo del estudiante.',
            'nombre_completo.string' => 'El nombre completo debe ser texto.',
            'nombre_completo.max' => 'El nombre completo no debe superar los 200 caracteres.',

            'correo.required' => 'El correo institucional del estudiante es obligatorio.',
            'correo.email' => 'El correo del estudiante debe tener un formato válido.',
            'correo.max' => 'El correo no debe superar los 191 caracteres.',

            'carrera.string' => 'La carrera debe ser texto.',
            'carrera.max' => 'La carrera no debe superar los 150 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'seccion_id' => 'sección',
            'carne' => 'carné',
            'nombres' => 'nombres',
            'apellidos' => 'apellidos',
            'nombre_completo' => 'nombre completo',
            'correo' => 'correo',
            'carrera' => 'carrera',
        ];
    }

    private function digitosCarne(string $carne): string
    {
        return preg_replace('/\D+/', '', $carne) ?? '';
    }

    private function formatearCarne(string $digitos): string
    {
        return substr($digitos, 0, 2)
            . '-'
            . substr($digitos, 2, 4)
            . '-'
            . substr($digitos, 6, 4);
    }

    private function correoDesdeCarne(string $digitos): string
    {
        return $digitos . '@mail.utec.edu.sv';
    }
}
