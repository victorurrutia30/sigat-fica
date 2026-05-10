<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistrarRespuestaDecanoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    public function rules(): array
    {
        return [
            'estado_aprobacion' => [
                'required',
                Rule::in(['aprobado', 'requiere_ajustes']),
            ],
            'fecha_respuesta_decano' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'observaciones_decano' => [
                'nullable',
                'string',
                'max:2000',
                'required_if:estado_aprobacion,requiere_ajustes',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'estado_aprobacion.required' => 'Debe seleccionar la respuesta del Decano.',
            'estado_aprobacion.in' => 'La respuesta del Decano seleccionada no es válida.',

            'fecha_respuesta_decano.required' => 'Debe ingresar la fecha de respuesta del Decano.',
            'fecha_respuesta_decano.date' => 'La fecha de respuesta del Decano no es válida.',
            'fecha_respuesta_decano.before_or_equal' => 'La fecha de respuesta del Decano no puede ser futura.',

            'observaciones_decano.required_if' => 'Debe ingresar observaciones cuando la respuesta requiere ajustes.',
            'observaciones_decano.string' => 'Las observaciones del Decano deben ser texto.',
            'observaciones_decano.max' => 'Las observaciones del Decano no deben exceder 2000 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'estado_aprobacion' => 'respuesta del Decano',
            'fecha_respuesta_decano' => 'fecha de respuesta',
            'observaciones_decano' => 'observaciones del Decano',
        ];
    }
}
