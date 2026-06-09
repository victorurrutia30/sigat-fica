<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GestionCasoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'tutor';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('accion_realizada')) {
            $this->merge([
                'accion_realizada' => trim($this->input('accion_realizada')),
            ]);
        }

        if ($this->filled('resultado')) {
            $this->merge([
                'resultado' => trim($this->input('resultado')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'fecha_gestion' => [
                'required',
                'date',
            ],
            'medio_contacto' => [
                'required',
                Rule::in([
                    'llamada',
                    'correo',
                    'presencial',
                    'whatsapp',
                    'otro',
                ]),
            ],
            'accion_realizada' => [
                'required',
                'string',
                'max:2000',
            ],
            'resultado' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_gestion.required' => 'La fecha de gestión es obligatoria.',
            'fecha_gestion.date' => 'La fecha de gestión no tiene un formato válido.',

            'medio_contacto.required' => 'Debe seleccionar el medio de contacto.',
            'medio_contacto.in' => 'El medio de contacto seleccionado no es válido.',

            'accion_realizada.required' => 'La acción realizada es obligatoria.',
            'accion_realizada.string' => 'La acción realizada debe ser texto.',
            'accion_realizada.max' => 'La acción realizada no debe superar los 2000 caracteres.',

            'resultado.string' => 'El resultado debe ser texto.',
            'resultado.max' => 'El resultado no debe superar los 2000 caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_gestion' => 'fecha de gestión',
            'medio_contacto' => 'medio de contacto',
            'accion_realizada' => 'acción realizada',
            'resultado' => 'resultado',
        ];
    }
}
