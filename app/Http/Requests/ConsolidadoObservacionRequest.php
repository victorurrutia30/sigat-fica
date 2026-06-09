<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsolidadoObservacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('observaciones_coord')) {
            $this->merge([
                'observaciones_coord' => trim($this->input('observaciones_coord')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'observaciones_coord' => [
                'required',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'observaciones_coord.required' => 'Debe ingresar una observación para el consolidado.',
            'observaciones_coord.string' => 'La observación debe ser texto.',
            'observaciones_coord.max' => 'La observación no debe superar los 2000 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'observaciones_coord' => 'observación de Coordinación',
        ];
    }
}
