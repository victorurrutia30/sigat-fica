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
        ];
    }

    public function messages(): array
    {
        return [
            'confirmar_sin_casos.boolean' => 'La confirmación de ausencia de casos no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'confirmar_sin_casos' => 'confirmación de ausencia de casos',
        ];
    }
}
