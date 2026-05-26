<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CausaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('nombre')) {
            $this->merge([
                'nombre' => trim($this->input('nombre')),
            ]);
        }

        if ($this->filled('descripcion')) {
            $this->merge([
                'descripcion' => trim($this->input('descripcion')),
            ]);
        }
    }

    public function rules(): array
    {
        $causa = $this->route('causa');

        return [
            'nombre' => [
                'required',
                'string',
                'max:120',
                Rule::unique('causas', 'nombre')->ignore($causa?->id),
            ],
            'descripcion' => [
                'nullable',
                'string',
                'max:1000',
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
            'nombre.required' => 'El nombre de la causa es obligatorio.',
            'nombre.string' => 'El nombre de la causa debe ser texto.',
            'nombre.max' => 'El nombre de la causa no debe superar los 120 caracteres.',
            'nombre.unique' => 'Ya existe una causa con este nombre.',

            'descripcion.string' => 'La descripción debe ser texto.',
            'descripcion.max' => 'La descripción no debe superar los 1000 caracteres.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'activo' => 'activo',
        ];
    }
}
