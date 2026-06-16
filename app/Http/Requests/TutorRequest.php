<?php

namespace App\Http\Requests;

use App\Models\Tutor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use App\Models\Seccion;


class TutorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('codigo_empleado')) {
            $this->merge([
                'codigo_empleado' => strtoupper(trim($this->input('codigo_empleado'))),
            ]);
        }

        if ($this->filled('nombre_completo')) {
            $this->merge([
                'nombre_completo' => trim($this->input('nombre_completo')),
            ]);
        }

        if ($this->filled('correo_institucional')) {
            $this->merge([
                'correo_institucional' => strtolower(trim($this->input('correo_institucional'))),
            ]);
        }

        if ($this->filled('departamento')) {
            $this->merge([
                'departamento' => trim($this->input('departamento')),
            ]);
        }

        if ($this->filled('categoria_docente')) {
            $this->merge([
                'categoria_docente' => strtoupper(trim($this->input('categoria_docente'))),
            ]);
        }

        if ($this->filled('motivo_excepcion_tutoria')) {
            $this->merge([
                'motivo_excepcion_tutoria' => trim($this->input('motivo_excepcion_tutoria')),
            ]);
        }
    }

    public function rules(): array
    {
        $tutor = $this->route('tutor');

        return [
            'usuario_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')
                    ->where('rol', 'tutor')
                    ->where('activo', true),
                Rule::unique('tutores', 'usuario_id')->ignore($tutor?->id),
            ],
            'codigo_empleado' => [
                'required',
                'string',
                'max:30',
                Rule::unique('tutores', 'codigo_empleado')->ignore($tutor?->id),
            ],
            'nombre_completo' => [
                'required',
                'string',
                'max:200',
            ],
            'correo_institucional' => [
                'required',
                'email',
                'max:191',
                Rule::unique('tutores', 'correo_institucional')->ignore($tutor?->id),
            ],
            'departamento' => [
                'nullable',
                'string',
                'max:100',
            ],
            'categoria_docente' => [
                'nullable',
                'string',
                'max:30',
            ],
            'fecha_contratacion' => [
                'nullable',
                'date',
            ],
            'tiempo_completo' => [
                'nullable',
                'boolean',
            ],
            'habilitado_para_tutorias' => [
                'nullable',
                'boolean',
            ],
            'es_excepcion_tutoria' => [
                'nullable',
                'boolean',
            ],
            'motivo_excepcion_tutoria' => [
                'nullable',
                'string',
                'max:1000',
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

            $tutorActual = $this->route('tutor');
            $codigoEmpleado = trim((string) $this->input('codigo_empleado'));

            if ($tutorActual && $codigoEmpleado !== '') {
                $codigoActual = strtoupper(trim((string) $tutorActual->codigo_empleado));

                if ($codigoEmpleado !== $codigoActual) {
                    $codigoUsadoEnCargaAcademica = Seccion::query()
                        ->where('codigo_docente_titular', $codigoActual)
                        ->exists();

                    if ($codigoUsadoEnCargaAcademica) {
                        $validator->errors()->add(
                            'codigo_empleado',
                            'No puedes cambiar el código de empleado porque ya está vinculado a secciones importadas desde la carga académica.'
                        );
                    }
                }
            }

            if ($codigoEmpleado !== '') {
                $codigoYaExiste = Tutor::withTrashed()
                    ->where('codigo_empleado', $codigoEmpleado)
                    ->when($tutorActual, function ($query) use ($tutorActual) {
                        $query->whereKeyNot($tutorActual->id);
                    })
                    ->exists();

                if ($codigoYaExiste) {
                    $validator->errors()->add(
                        'codigo_empleado',
                        'Ya existe un tutor con este código de empleado/docente. Busca el registro existente y edítalo.'
                    );
                }
            }

            $tiempoCompleto = $this->boolean('tiempo_completo');
            $habilitado = $this->boolean('habilitado_para_tutorias');
            $esExcepcion = $this->boolean('es_excepcion_tutoria');
            $motivo = trim((string) $this->input('motivo_excepcion_tutoria'));

            if ($habilitado && ! $tiempoCompleto && ! $esExcepcion) {
                $validator->errors()->add(
                    'habilitado_para_tutorias',
                    'Para habilitar a un tutor que no es DTC, debes marcar excepción autorizada.'
                );
            }

            if ($esExcepcion && $tiempoCompleto) {
                $validator->errors()->add(
                    'es_excepcion_tutoria',
                    'Un DTC no necesita marcarse como excepción.'
                );
            }

            if ($esExcepcion && $motivo === '') {
                $validator->errors()->add(
                    'motivo_excepcion_tutoria',
                    'El motivo de excepción es obligatorio cuando el tutor no es DTC.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'usuario_id.integer' => 'La cuenta de usuario seleccionada no es válida.',
            'usuario_id.exists' => 'La cuenta seleccionada no existe, no está activa o no pertenece al rol tutor.',
            'usuario_id.unique' => 'La cuenta seleccionada ya está vinculada a otro tutor.',

            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.string' => 'El código de empleado debe ser texto.',
            'codigo_empleado.max' => 'El código de empleado no debe superar los 30 caracteres.',
            'codigo_empleado.unique' => 'Ya existe un tutor con este código de empleado.',

            'nombre_completo.required' => 'El nombre completo es obligatorio.',
            'nombre_completo.string' => 'El nombre completo debe ser texto.',
            'nombre_completo.max' => 'El nombre completo no debe superar los 200 caracteres.',

            'correo_institucional.required' => 'El correo institucional es obligatorio.',
            'correo_institucional.email' => 'El correo institucional debe tener un formato válido.',
            'correo_institucional.max' => 'El correo institucional no debe superar los 191 caracteres.',
            'correo_institucional.unique' => 'Ya existe un tutor con este correo institucional.',

            'departamento.string' => 'El departamento debe ser texto.',
            'departamento.max' => 'El departamento no debe superar los 100 caracteres.',

            'categoria_docente.string' => 'La categoría docente debe ser texto.',
            'categoria_docente.max' => 'La categoría docente no debe superar los 30 caracteres.',

            'fecha_contratacion.date' => 'La fecha de contratación no tiene un formato válido.',

            'tiempo_completo.boolean' => 'El valor de DTC no tiene un formato válido.',
            'habilitado_para_tutorias.boolean' => 'El valor de habilitación para tutorías no tiene un formato válido.',
            'es_excepcion_tutoria.boolean' => 'El valor de excepción no tiene un formato válido.',
            'motivo_excepcion_tutoria.string' => 'El motivo de excepción debe ser texto.',
            'motivo_excepcion_tutoria.max' => 'El motivo de excepción no debe superar los 1000 caracteres.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'usuario_id' => 'cuenta de usuario',
            'codigo_empleado' => 'código de empleado',
            'nombre_completo' => 'nombre completo',
            'correo_institucional' => 'correo institucional',
            'departamento' => 'departamento',
            'categoria_docente' => 'categoría docente',
            'fecha_contratacion' => 'fecha de contratación',
            'tiempo_completo' => 'docente de tiempo completo',
            'habilitado_para_tutorias' => 'habilitado para tutorías',
            'es_excepcion_tutoria' => 'excepción autorizada',
            'motivo_excepcion_tutoria' => 'motivo de excepción',
            'activo' => 'activo',
        ];
    }
}
