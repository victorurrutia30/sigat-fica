<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasoSeguimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'tutor';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('carne')) {
            $this->merge([
                'carne' => strtoupper(trim($this->input('carne'))),
            ]);
        }

        if ($this->filled('nombres')) {
            $this->merge([
                'nombres' => trim($this->input('nombres')),
            ]);
        }

        if ($this->filled('apellidos')) {
            $this->merge([
                'apellidos' => trim($this->input('apellidos')),
            ]);
        }

        $nombres = trim((string) $this->input('nombres'));
        $apellidos = trim((string) $this->input('apellidos'));

        if ($nombres !== '' || $apellidos !== '') {
            $this->merge([
                'nombre_completo' => trim($nombres . ' ' . $apellidos),
            ]);
        }

        if ($this->filled('correo')) {
            $this->merge([
                'correo' => strtolower(trim($this->input('correo'))),
            ]);
        }

        if ($this->filled('carrera')) {
            $this->merge([
                'carrera' => trim($this->input('carrera')),
            ]);
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
                'nullable',
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
}
