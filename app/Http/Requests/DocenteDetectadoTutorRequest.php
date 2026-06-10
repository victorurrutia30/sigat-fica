<?php

namespace App\Http\Requests;

use App\Models\Ciclo;
use App\Models\Seccion;
use App\Models\Tutor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DocenteDetectadoTutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'codigo_docente' => strtoupper(trim((string) $this->route('codigoDocente'))),
        ]);
    }

    public function rules(): array
    {
        return [
            'codigo_docente' => [
                'required',
                'string',
                'max:30',
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $ciclo = Ciclo::query()
                ->where('activo', true)
                ->first();

            if (! $ciclo) {
                $validator->errors()->add(
                    'codigo_docente',
                    'No hay un ciclo activo para buscar docentes detectados.'
                );

                return;
            }

            $codigoDocente = strtoupper(trim((string) $this->input('codigo_docente')));

            $existeDocente = Seccion::query()
                ->where('ciclo_id', $ciclo->id)
                ->where('codigo_docente_titular', $codigoDocente)
                ->exists();

            if (! $existeDocente) {
                $validator->errors()->add(
                    'codigo_docente',
                    'El docente seleccionado no existe en la carga académica del ciclo activo.'
                );
            }

            $yaExisteTutor = Tutor::withTrashed()
                ->where('codigo_empleado', $codigoDocente)
                ->exists();

            if ($yaExisteTutor) {
                $validator->errors()->add(
                    'codigo_docente',
                    'Ya existe un tutor con este código de docente. Búscalo en el catálogo de tutores.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'codigo_docente.required' => 'El código docente es obligatorio.',
            'codigo_docente.string' => 'El código docente debe ser texto.',
            'codigo_docente.max' => 'El código docente no debe superar los 30 caracteres.',
        ];
    }
}
