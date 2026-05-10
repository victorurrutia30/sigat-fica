<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarItemPropuestaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    public function rules(): array
    {
        return [
            'seccion_id' => ['required', 'integer', 'exists:secciones,id'],
            'tutor_id' => ['required', 'integer', 'exists:tutores,id'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'seccion_id.required' => 'Debe seleccionar una sección.',
            'seccion_id.integer' => 'La sección seleccionada no es válida.',
            'seccion_id.exists' => 'La sección seleccionada no existe.',

            'tutor_id.required' => 'Debe seleccionar un tutor.',
            'tutor_id.integer' => 'El tutor seleccionado no es válido.',
            'tutor_id.exists' => 'El tutor seleccionado no existe.',

            'observaciones.string' => 'Las observaciones deben ser texto.',
            'observaciones.max' => 'Las observaciones no deben exceder 1000 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'seccion_id' => 'sección',
            'tutor_id' => 'tutor',
            'observaciones' => 'observaciones',
        ];
    }
}
