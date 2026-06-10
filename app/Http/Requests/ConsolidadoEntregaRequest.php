<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConsolidadoEntregaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'tutor';
    }

    public function rules(): array
    {
        return [
            'confirmar_sin_casos' => [
                'nullable',
                'boolean',
            ],
            'secciones_sin_casos' => [
                'nullable',
                'array',
            ],
            'secciones_sin_casos.*' => [
                'integer',
                'exists:secciones,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'confirmar_sin_casos.boolean' => 'La confirmación de ausencia de casos no tiene un valor válido.',

            'secciones_sin_casos.array' => 'La confirmación por sección no tiene un formato válido.',
            'secciones_sin_casos.*.integer' => 'Una de las secciones confirmadas no es válida.',
            'secciones_sin_casos.*.exists' => 'Una de las secciones confirmadas no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'confirmar_sin_casos' => 'confirmación de ausencia de casos',
            'secciones_sin_casos' => 'secciones sin casos',
        ];
    }
}
