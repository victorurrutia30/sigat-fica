<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CasoCierreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'tutor';
    }

    public function rules(): array
    {
        return [
            'causa_id' => [
                'required',
                'integer',
                Rule::exists('causas', 'id')->where('activo', true),
            ],
            'resultado_final' => [
                'required',
                Rule::in([
                    'retiro',
                    'abandono',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'causa_id.required' => 'Debe seleccionar una causa para cerrar el caso.',
            'causa_id.integer' => 'La causa seleccionada no es válida.',
            'causa_id.exists' => 'La causa seleccionada no existe o está inactiva.',

            'resultado_final.required' => 'Debe seleccionar el resultado final del caso.',
            'resultado_final.in' => 'El resultado final seleccionado no es válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'causa_id' => 'causa',
            'resultado_final' => 'resultado final',
        ];
    }
}
