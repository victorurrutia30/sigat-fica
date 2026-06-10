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

    protected function prepareForValidation(): void
    {
        if ($this->filled('detalle_inasistencia')) {
            $this->merge([
                'detalle_inasistencia' => trim($this->input('detalle_inasistencia')),
            ]);
        }
        if ($this->input('cuota_cancelada') === '') {
            $this->merge([
                'cuota_cancelada' => null,
            ]);
        }

        if ($this->filled('cuota_cancelada')) {
            $this->merge([
                'cuota_cancelada' => trim($this->input('cuota_cancelada')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'causa_id' => [
                'required',
                'integer',
                Rule::exists('causas', 'id')->where('activo', true),
            ],
            'detalle_inasistencia' => [
                'required',
                'string',
                'max:2000',
            ],
            'resultado_consolidado' => [
                'required',
                Rule::in([
                    'rc',
                    'rm',
                    'abm',
                    'abc',
                ]),
            ],
            'matricula' => [
                'required',
                'boolean',
            ],
            'cuota_cancelada' => [
                'nullable',
                'integer',
                'min:0',
                'max:99',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'causa_id.required' => 'Debe seleccionar una causa para cerrar el caso.',
            'causa_id.integer' => 'La causa seleccionada no es válida.',
            'causa_id.exists' => 'La causa seleccionada no existe o está inactiva.',

            'detalle_inasistencia.required' => 'Debe ingresar el detalle de inasistencia a la evaluación.',
            'detalle_inasistencia.string' => 'El detalle de inasistencia debe ser texto.',
            'detalle_inasistencia.max' => 'El detalle de inasistencia no debe superar los 2000 caracteres.',

            'resultado_consolidado.required' => 'Debe seleccionar el resultado para el consolidado.',
            'resultado_consolidado.in' => 'El resultado para el consolidado seleccionado no es válido.',

            'matricula.required' => 'Debe indicar si el estudiante tiene matrícula.',
            'matricula.boolean' => 'El valor de matrícula no es válido.',

            'cuota_cancelada.integer' => 'La cuota cancelada debe ser un número entero.',
            'cuota_cancelada.min' => 'La cuota cancelada no puede ser menor que 0.',
            'cuota_cancelada.max' => 'La cuota cancelada no puede ser mayor que 99.',
        ];
    }

    public function attributes(): array
    {
        return [
            'causa_id' => 'causa',
            'detalle_inasistencia' => 'detalle de inasistencia',
            'resultado_consolidado' => 'resultado para consolidado',
            'matricula' => 'matrícula',
            'cuota_cancelada' => 'cuota cancelada',
        ];
    }
}
