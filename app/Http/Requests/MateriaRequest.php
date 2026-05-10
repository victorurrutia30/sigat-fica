<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('codigo')) {
            $this->merge([
                'codigo' => strtoupper(trim($this->input('codigo'))),
            ]);
        }

        if ($this->filled('nombre')) {
            $this->merge([
                'nombre' => trim($this->input('nombre')),
            ]);
        }

        if ($this->filled('departamento')) {
            $this->merge([
                'departamento' => trim($this->input('departamento')),
            ]);
        }
    }

    public function rules(): array
    {
        $materia = $this->route('materia');

        return [
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('materias', 'codigo')->ignore($materia?->id),
            ],
            'nombre' => [
                'required',
                'string',
                'max:150',
            ],
            'creditos' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'ciclo_plan' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],
            'departamento' => [
                'nullable',
                'string',
                'max:100',
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
            'codigo.required' => 'El código de la materia es obligatorio.',
            'codigo.string' => 'El código de la materia debe ser texto.',
            'codigo.max' => 'El código no debe superar los 20 caracteres.',
            'codigo.unique' => 'Ya existe una materia con este código.',

            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'nombre.string' => 'El nombre de la materia debe ser texto.',
            'nombre.max' => 'El nombre no debe superar los 150 caracteres.',

            'creditos.required' => 'La cantidad de créditos es obligatoria.',
            'creditos.integer' => 'La cantidad de créditos debe ser un número entero.',
            'creditos.min' => 'La cantidad de créditos debe ser al menos 1.',
            'creditos.max' => 'La cantidad de créditos no debe ser mayor que 10.',

            'ciclo_plan.required' => 'El ciclo del plan es obligatorio.',
            'ciclo_plan.integer' => 'El ciclo del plan debe ser un número entero.',
            'ciclo_plan.min' => 'El ciclo del plan debe estar entre 1 y 10.',
            'ciclo_plan.max' => 'El ciclo del plan debe estar entre 1 y 10.',

            'departamento.string' => 'El departamento debe ser texto.',
            'departamento.max' => 'El departamento no debe superar los 100 caracteres.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'codigo' => 'código',
            'nombre' => 'nombre',
            'creditos' => 'créditos',
            'ciclo_plan' => 'ciclo del plan',
            'departamento' => 'departamento',
            'activo' => 'activo',
        ];
    }
}
