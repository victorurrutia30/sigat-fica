<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportarCargaAcademicaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    public function rules(): array
    {
        return [
            'ciclo_id' => [
                'required',
                'integer',
                'exists:ciclos,id',
            ],
            'archivo' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ciclo_id.required' => 'Debe seleccionar un ciclo académico.',
            'ciclo_id.integer' => 'El ciclo académico seleccionado no es válido.',
            'ciclo_id.exists' => 'El ciclo académico seleccionado no existe.',

            'archivo.required' => 'Debe seleccionar el archivo Excel de carga académica.',
            'archivo.file' => 'El archivo seleccionado no es válido.',
            'archivo.mimes' => 'El archivo debe ser Excel: xlsx o xls.',
            'archivo.max' => 'El archivo no debe superar los 10 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ciclo_id' => 'ciclo académico',
            'archivo' => 'archivo Excel',
        ];
    }
}
