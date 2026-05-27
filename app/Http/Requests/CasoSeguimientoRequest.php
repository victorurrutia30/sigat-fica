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

        if ($this->filled('nombre_completo')) {
            $this->merge([
                'nombre_completo' => trim($this->input('nombre_completo')),
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

            'nombre_completo.required' => 'El nombre completo del estudiante es obligatorio.',
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
            'nombre_completo' => 'nombre completo',
            'correo' => 'correo',
            'carrera' => 'carrera',
        ];
    }
}
