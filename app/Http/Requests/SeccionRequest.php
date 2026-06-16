<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SeccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->rol === 'coordinacion';
    }

    protected function prepareForValidation(): void
    {
        $horarios = collect($this->input('horarios', []))
            ->map(function ($horario) {
                return [
                    'dia_semana' => $horario['dia_semana'] ?? null,
                    'hora_inicio' => $horario['hora_inicio'] ?? null,
                    'hora_fin' => $horario['hora_fin'] ?? null,
                ];
            })
            ->filter(function ($horario) {
                return filled($horario['dia_semana'])
                    || filled($horario['hora_inicio'])
                    || filled($horario['hora_fin']);
            })
            ->values()
            ->all();

        $this->merge([
            'numero_seccion' => trim((string) $this->input('numero_seccion')),
            'modalidad' => trim((string) $this->input('modalidad')),
            'aula' => $this->filled('aula') ? trim((string) $this->input('aula')) : null,
            'nombre_titular' => trim((string) $this->input('nombre_titular')),
            'correo_titular' => $this->filled('correo_titular') ? trim((string) $this->input('correo_titular')) : null,
            'codigo_docente_titular' => $this->filled('codigo_docente_titular')
                ? strtoupper(trim((string) $this->input('codigo_docente_titular')))
                : null,
            'categoria_docente_titular' => $this->filled('categoria_docente_titular')
                ? strtoupper(trim((string) $this->input('categoria_docente_titular')))
                : null,
            'observaciones_carga' => $this->filled('observaciones_carga')
                ? trim((string) $this->input('observaciones_carga'))
                : null,
            'requiere_tutor' => $this->boolean('requiere_tutor'),
            'horarios' => $horarios,
        ]);
    }

    public function rules(): array
    {
        $seccion = $this->route('seccion');

        return [
            'numero_seccion' => [
                'required',
                'string',
                'max:10',
                Rule::unique('secciones', 'numero_seccion')
                    ->where(fn($query) => $query
                        ->where('ciclo_id', $seccion?->ciclo_id)
                        ->where('materia_id', $seccion?->materia_id))
                    ->ignore($seccion?->id),
            ],
            'modalidad' => [
                'required',
                Rule::in(['presencial', 'en_linea', 'virtual', 'mixta']),
            ],
            'requiere_tutor' => [
                'nullable',
                'boolean',
            ],
            'aula' => [
                'nullable',
                'string',
                'max:60',
            ],
            'nombre_titular' => [
                'required',
                'string',
                'max:200',
            ],
            'correo_titular' => [
                'nullable',
                'email',
                'max:191',
            ],
            'codigo_docente_titular' => [
                'nullable',
                'string',
                'max:30',
            ],
            'categoria_docente_titular' => [
                'nullable',
                'string',
                'max:30',
            ],
            'observaciones_carga' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'horarios' => [
                'nullable',
                'array',
            ],
            'horarios.*.dia_semana' => [
                'nullable',
                'integer',
                'min:1',
                'max:7',
            ],
            'horarios.*.hora_inicio' => [
                'nullable',
                'date_format:H:i',
            ],
            'horarios.*.hora_fin' => [
                'nullable',
                'date_format:H:i',
            ],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $modalidad = $this->input('modalidad');
            $requiereTutor = $this->boolean('requiere_tutor');
            $horarios = $this->input('horarios', []);

            foreach ($horarios as $indice => $horario) {
                $tieneDia = filled($horario['dia_semana'] ?? null);
                $tieneInicio = filled($horario['hora_inicio'] ?? null);
                $tieneFin = filled($horario['hora_fin'] ?? null);

                if (! $tieneDia && ! $tieneInicio && ! $tieneFin) {
                    continue;
                }

                if (! $tieneDia || ! $tieneInicio || ! $tieneFin) {
                    $validator->errors()->add(
                        "horarios.{$indice}",
                        'Cada horario debe tener día, hora de inicio y hora de fin.'
                    );

                    continue;
                }

                if (($horario['hora_inicio'] ?? '') >= ($horario['hora_fin'] ?? '')) {
                    $validator->errors()->add(
                        "horarios.{$indice}.hora_fin",
                        'La hora de fin debe ser posterior a la hora de inicio.'
                    );
                }
            }

            $horariosCompletos = collect($horarios)
                ->filter(function ($horario) {
                    return filled($horario['dia_semana'] ?? null)
                        && filled($horario['hora_inicio'] ?? null)
                        && filled($horario['hora_fin'] ?? null);
                })
                ->values();

            foreach ($horariosCompletos as $indiceA => $horarioA) {
                foreach ($horariosCompletos as $indiceB => $horarioB) {
                    if ($indiceB <= $indiceA) {
                        continue;
                    }

                    if ((int) $horarioA['dia_semana'] !== (int) $horarioB['dia_semana']) {
                        continue;
                    }

                    $seTraslapan = $horarioA['hora_inicio'] < $horarioB['hora_fin']
                        && $horarioB['hora_inicio'] < $horarioA['hora_fin'];

                    if ($seTraslapan) {
                        $validator->errors()->add(
                            'horarios',
                            'La sección no puede tener horarios duplicados o traslapados en el mismo día.'
                        );

                        return;
                    }
                }
            }

            if ($modalidad !== 'virtual' && $requiereTutor && count($horariosCompletos) === 0) {
                $validator->errors()->add(
                    'horarios',
                    'Las secciones presenciales, en línea o mixtas que requieren tutor deben tener al menos un horario.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'numero_seccion.required' => 'El número de sección es obligatorio.',
            'numero_seccion.max' => 'El número de sección no debe superar los 10 caracteres.',
            'numero_seccion.unique' => 'Ya existe una sección con ese número para esta materia y ciclo.',

            'modalidad.required' => 'La modalidad es obligatoria.',
            'modalidad.in' => 'La modalidad seleccionada no es válida.',

            'aula.max' => 'El aula no debe superar los 60 caracteres.',

            'nombre_titular.required' => 'El nombre del docente titular es obligatorio.',
            'nombre_titular.max' => 'El nombre del docente titular no debe superar los 200 caracteres.',

            'correo_titular.email' => 'El correo del docente titular no tiene un formato válido.',
            'correo_titular.max' => 'El correo del docente titular no debe superar los 191 caracteres.',

            'codigo_docente_titular.max' => 'El código del docente titular no debe superar los 30 caracteres.',
            'categoria_docente_titular.max' => 'La categoría del docente titular no debe superar los 30 caracteres.',

            'observaciones_carga.max' => 'Las observaciones no deben superar los 2000 caracteres.',

            'horarios.array' => 'Los horarios enviados no tienen un formato válido.',
            'horarios.*.dia_semana.integer' => 'El día del horario debe ser numérico.',
            'horarios.*.dia_semana.min' => 'El día del horario no es válido.',
            'horarios.*.dia_semana.max' => 'El día del horario no es válido.',
            'horarios.*.hora_inicio.date_format' => 'La hora de inicio debe tener formato HH:MM.',
            'horarios.*.hora_fin.date_format' => 'La hora de fin debe tener formato HH:MM.',
        ];
    }
}
