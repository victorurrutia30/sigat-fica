<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('codigo_empleado')) {
            $this->merge([
                'codigo_empleado' => strtoupper(trim($this->input('codigo_empleado'))),
            ]);
        }

        if ($this->filled('nombre_completo')) {
            $this->merge([
                'nombre_completo' => trim($this->input('nombre_completo')),
            ]);
        }

        if ($this->filled('correo_institucional')) {
            $this->merge([
                'correo_institucional' => strtolower(trim($this->input('correo_institucional'))),
            ]);
        }

        if ($this->filled('departamento')) {
            $this->merge([
                'departamento' => trim($this->input('departamento')),
            ]);
        }

        $this->merge([
            'tiempo_completo' => true,
        ]);
    }

    public function rules(): array
    {
        $tutor = $this->route('tutor');

        return [
            'usuario_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('rol', 'tutor')
                    ->where('activo', true),
                Rule::unique('tutores', 'usuario_id')->ignore($tutor?->id),
            ],
            'codigo_empleado' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tutores', 'codigo_empleado')->ignore($tutor?->id),
            ],
            'nombre_completo' => [
                'required',
                'string',
                'max:200',
            ],
            'correo_institucional' => [
                'required',
                'email',
                'max:191',
                Rule::unique('tutores', 'correo_institucional')->ignore($tutor?->id),
            ],
            'departamento' => [
                'nullable',
                'string',
                'max:100',
            ],
            'fecha_contratacion' => [
                'nullable',
                'date',
            ],
            'tiempo_completo' => [
                'accepted',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario_id.integer' => 'La cuenta de usuario seleccionada no es válida.',
            'usuario_id.exists' => 'La cuenta seleccionada no existe, no está activa o no pertenece al rol tutor.',
            'usuario_id.unique' => 'La cuenta seleccionada ya está vinculada a otro tutor.',

            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.string' => 'El código de empleado debe ser texto.',
            'codigo_empleado.max' => 'El código de empleado no debe superar los 30 caracteres.',
            'codigo_empleado.unique' => 'Ya existe un tutor con este código de empleado.',

            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'nombre_completo.string' => 'El nombre completo debe ser texto.',
            'nombre_completo.max' => 'El nombre completo no debe superar los 200 caracteres.',

            'correo_institucional.required' => 'El correo institucional es obligatorio.',
            'correo_institucional.email' => 'El correo institucional debe tener un formato válido.',
            'correo_institucional.max' => 'El correo institucional no debe superar los 191 caracteres.',
            'correo_institucional.unique' => 'Ya existe un tutor con este correo institucional.',

            'departamento.string' => 'El departamento debe ser texto.',
            'departamento.max' => 'El departamento no debe superar los 100 caracteres.',

            'fecha_contratacion.date' => 'La fecha de contratación no tiene un formato válido.',

            'tiempo_completo.accepted' => 'Solo los Docentes de Tiempo Completo pueden registrarse como tutores.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'usuario_id' => 'cuenta de usuario',
            'codigo_empleado' => 'código de empleado',
            'nombre_completo' => 'nombre completo',
            'correo_institucional' => 'correo institucional',
            'departamento' => 'departamento',
            'fecha_contratacion' => 'fecha de contratación',
            'tiempo_completo' => 'docente de tiempo completo',
            'activo' => 'activo',
        ];
    }
}
