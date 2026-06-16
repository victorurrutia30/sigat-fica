<?php

namespace App\Http\Requests;

use App\Models\Ciclo;
use App\Models\PeriodoEvaluacion;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class PeriodoEvaluacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('nombre')) {
            $this->merge([
                'nombre' => trim($this->input('nombre')),
            ]);
        }
    }

    public function rules(): array
    {
        $periodo = $this->route('periodoEvaluacion');
        $cicloId = $this->input('ciclo_id') ?? $periodo?->ciclo_id;

        return [
            'ciclo_id' => [
                'required',
                'integer',
                'exists:ciclos,id',
            ],
            'nombre' => [
                'required',
                'string',
                'max:100',
                Rule::in(PeriodoEvaluacion::nombresPermitidos()),
                Rule::unique('periodos_evaluacion', 'nombre')
                    ->where(fn($query) => $query->where('ciclo_id', $cicloId))
                    ->ignore($periodo?->id),
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
            'fecha_limite_consolidado' => [
                'required',
                'date',
                'after_or_equal:fecha_fin',
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

            $periodo = $this->route('periodoEvaluacion');
            $cicloId = (int) $this->input('ciclo_id');

            $ciclo = Ciclo::query()->find($cicloId);

            if (! $ciclo) {
                return;
            }

            if (! $ciclo->activo) {
                $validator->errors()->add(
                    'ciclo_id',
                    'Solo se pueden crear o editar periodos del ciclo académico activo.'
                );

                return;
            }

            $fechaInicio = $this->fechaValida($this->input('fecha_inicio'));
            $fechaFin = $this->fechaValida($this->input('fecha_fin'));
            $fechaLimiteConsolidado = $this->fechaValida($this->input('fecha_limite_consolidado'));

            if (! $fechaInicio || ! $fechaFin || ! $fechaLimiteConsolidado) {
                return;
            }

            $inicioCiclo = CarbonImmutable::parse($ciclo->fecha_inicio->toDateString())->startOfDay();
            $finCiclo = CarbonImmutable::parse($ciclo->fecha_fin->toDateString())->startOfDay();

            if ($fechaInicio->lt($inicioCiclo) || $fechaInicio->gt($finCiclo)) {
                $validator->errors()->add(
                    'fecha_inicio',
                    'La fecha de inicio del periodo debe estar dentro de las fechas del ciclo académico activo.'
                );
            }

            if ($fechaFin->lt($inicioCiclo) || $fechaFin->gt($finCiclo)) {
                $validator->errors()->add(
                    'fecha_fin',
                    'La fecha de fin del periodo debe estar dentro de las fechas del ciclo académico activo.'
                );
            }

            if ($fechaLimiteConsolidado->lt($inicioCiclo) || $fechaLimiteConsolidado->gt($finCiclo)) {
                $validator->errors()->add(
                    'fecha_limite_consolidado',
                    'La fecha límite de consolidado debe estar dentro de las fechas del ciclo académico activo.'
                );
            }

            $existeTraslape = PeriodoEvaluacion::query()
                ->where('ciclo_id', $ciclo->id)
                ->when($periodo, function ($query) use ($periodo) {
                    $query->whereKeyNot($periodo->id);
                })
                ->whereDate('fecha_inicio', '<=', $fechaFin->toDateString())
                ->whereDate('fecha_fin', '>=', $fechaInicio->toDateString())
                ->exists();

            if ($existeTraslape) {
                $validator->errors()->add(
                    'fecha_inicio',
                    'Las fechas del periodo de evaluación se traslapan con otro periodo del mismo ciclo.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'ciclo_id.required' => 'Debe seleccionar un ciclo académico.',
            'ciclo_id.integer' => 'El ciclo académico seleccionado no es válido.',
            'ciclo_id.exists' => 'El ciclo académico seleccionado no existe.',

            'nombre.required' => 'El nombre del periodo es obligatorio.',
            'nombre.string' => 'El nombre del periodo debe ser texto.',
            'nombre.in' => 'Debe seleccionar un periodo válido: Primera Evaluación, Segunda Evaluación, Tercera Evaluación, Cuarta Evaluación o Quinta Evaluación.',
            'nombre.unique' => 'Ya existe un periodo con este nombre para el ciclo seleccionado.',

            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio no tiene un formato válido.',

            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin no tiene un formato válido.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',

            'fecha_limite_consolidado.required' => 'La fecha límite de consolidado es obligatoria.',
            'fecha_limite_consolidado.date' => 'La fecha límite de consolidado no tiene un formato válido.',
            'fecha_limite_consolidado.after_or_equal' => 'La fecha límite de consolidado debe ser igual o posterior a la fecha de fin del periodo.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'ciclo_id' => 'ciclo académico',
            'nombre' => 'nombre',
            'fecha_inicio' => 'fecha de inicio',
            'fecha_fin' => 'fecha de fin',
            'fecha_limite_consolidado' => 'fecha límite de consolidado',
            'activo' => 'activo',
        ];
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
