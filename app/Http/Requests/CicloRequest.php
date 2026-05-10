<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CicloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('anio') && $this->filled('periodo')) {
            $this->merge([
                'nombre' => $this->construirNombreCiclo(
                    (int) $this->input('anio'),
                    (int) $this->input('periodo')
                ),
            ]);
        }
    }

    public function rules(): array
    {
        $ciclo = $this->route('ciclo');

        return [
            'nombre' => [
                'required',
                'string',
                'max:20',
                Rule::unique('ciclos', 'nombre')->ignore($ciclo?->id),
            ],
            'anio' => [
                'required',
                'integer',
                'min:2020',
                'max:2035',
            ],
            'periodo' => [
                'required',
                'integer',
                Rule::in([1, 2, 3]),
            ],
            'fecha_inicio' => [
                'required',
                'date',
            ],
            'fecha_fin' => [
                'required',
                'date',
                'after:fecha_inicio',
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
            'nombre.required' => 'No se pudo generar el nombre del ciclo.',
            'nombre.string' => 'El nombre del ciclo debe ser texto.',
            'nombre.max' => 'El nombre del ciclo no debe superar los 20 caracteres.',
            'nombre.unique' => 'Ya existe un ciclo académico para el año y ciclo seleccionados.',

            'anio.required' => 'El año del ciclo es obligatorio.',
            'anio.integer' => 'El año debe ser un número entero.',
            'anio.min' => 'El año no puede ser menor que 2020.',
            'anio.max' => 'El año no puede ser mayor que 2035.',

            'periodo.required' => 'El ciclo es obligatorio.',
            'periodo.integer' => 'El ciclo debe ser un número válido.',
            'periodo.in' => 'El ciclo seleccionado no es válido.',

            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio no tiene un formato válido.',

            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin no tiene un formato válido.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'anio' => 'año',
            'periodo' => 'ciclo',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'activo' => 'activo',
        ];
    }

    private function construirNombreCiclo(int $anio, int $periodo): string
    {
        return sprintf('%d-%02d', $anio, $periodo);
    }
}
