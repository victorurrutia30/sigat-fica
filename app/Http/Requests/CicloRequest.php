<?php

namespace App\Http\Requests;

use App\Models\Ciclo;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
        $anioActual = now()->year;

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
                'min:' . ($anioActual - 1),
                'max:' . ($anioActual + 1),
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

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $ciclo = $this->route('ciclo');
            $anio = (int) $this->input('anio');

            $fechaInicio = $this->fechaValida($this->input('fecha_inicio'));
            $fechaFin = $this->fechaValida($this->input('fecha_fin'));

            if (! $fechaInicio || ! $fechaFin) {
                return;
            }

            if ((int) $fechaInicio->year !== $anio) {
                $validator->errors()->add(
                    'fecha_inicio',
                    'La fecha de inicio debe pertenecer al mismo año académico seleccionado.'
                );
            }

            if ((int) $fechaFin->year !== $anio) {
                $validator->errors()->add(
                    'fecha_fin',
                    'La fecha de fin debe pertenecer al mismo año académico seleccionado.'
                );
            }

            $existeTraslape = Ciclo::query()
                ->when($ciclo, function ($query) use ($ciclo) {
                    $query->whereKeyNot($ciclo->id);
                })
                ->whereDate('fecha_inicio', '<=', $fechaFin->toDateString())
                ->whereDate('fecha_fin', '>=', $fechaInicio->toDateString())
                ->exists();

            if ($existeTraslape) {
                $validator->errors()->add(
                    'fecha_inicio',
                    'Las fechas del ciclo académico se traslapan con otro ciclo existente.'
                );
            }
        });
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
            'anio.min' => 'Solo se pueden crear ciclos desde el año anterior al actual.',
            'anio.max' => 'Solo se pueden crear ciclos hasta el año siguiente al actual.',

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

    private function fechaValida(?string $fecha): ?CarbonImmutable
    {
        if (blank($fecha)) {
            return null;
        }

        try {
            return CarbonImmutable::parse($fecha)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
