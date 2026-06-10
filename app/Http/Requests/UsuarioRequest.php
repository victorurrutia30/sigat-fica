<?php

namespace App\Http\Requests;

use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


class UsuarioRequest extends FormRequest
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

        if ($this->filled('correo')) {
            $this->merge([
                'correo' => strtolower(trim($this->input('correo'))),
            ]);
        }

        if ($this->input('password') === '') {
            $this->merge([
                'password' => null,
            ]);
        }

        if ($this->input('tutor_id') === '') {
            $this->merge([
                'tutor_id' => null,
            ]);
        }
    }

    public function rules(): array
    {
        $usuario = $this->route('usuario');

        return [
            'nombre' => [
                'required',
                'string',
                'max:255',
            ],
            'correo' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'correo')->ignore($usuario?->id),
            ],
            'rol' => [
                'required',
                Rule::in(['coordinacion', 'tutor']),
            ],
            'password' => [
                $usuario ? 'nullable' : 'required',
                'string',
                'min:8',
                'confirmed',
            ],
            'activo' => [
                'nullable',
                'boolean',
            ],
            'tutor_id' => [
                'nullable',
                'integer',
                Rule::exists('tutores', 'id')
                    ->where('activo', true)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $usuario = $this->route('usuario');
            $tutorId = $this->input('tutor_id');

            if ($tutorId) {
                if ($this->input('rol') !== 'tutor') {
                    $validator->errors()->add(
                        'tutor_id',
                        'Solo los usuarios con rol tutor pueden vincularse a un tutor.'
                    );

                    return;
                }

                $tutor = Tutor::query()
                    ->whereKey($tutorId)
                    ->first();

                if (! $tutor || ! $tutor->puedeAsignarseComoTutor()) {
                    $validator->errors()->add(
                        'tutor_id',
                        'Solo puedes vincular usuarios a tutores activos, habilitados y aptos para tutorías.'
                    );

                    return;
                }

                $tutorOcupado = Tutor::withTrashed()
                    ->whereKey($tutorId)
                    ->whereNotNull('usuario_id')
                    ->when($usuario, function ($query) use ($usuario) {
                        $query->where('usuario_id', '!=', $usuario->id);
                    })
                    ->exists();

                if ($tutorOcupado) {
                    $validator->errors()->add(
                        'tutor_id',
                        'El tutor seleccionado ya tiene una cuenta de usuario vinculada.'
                    );
                }
            }

            if (! $usuario) {
                return;
            }

            $rolSolicitado = (string) $this->input('rol');
            $activoSolicitado = $this->boolean('activo');

            if ((int) $this->user()?->id === (int) $usuario->id) {
                if ($rolSolicitado !== 'coordinacion') {
                    $validator->errors()->add(
                        'rol',
                        'No puedes cambiar el rol de tu propia cuenta.'
                    );
                }

                if (! $activoSolicitado) {
                    $validator->errors()->add(
                        'activo',
                        'No puedes desactivar tu propia cuenta.'
                    );
                }
            }

            $eraCoordinacionActiva = $usuario->rol === 'coordinacion' && $usuario->activo;
            $dejariaDeSerCoordinacionActiva = $rolSolicitado !== 'coordinacion' || ! $activoSolicitado;

            if ($eraCoordinacionActiva && $dejariaDeSerCoordinacionActiva) {
                $coordinacionesActivas = User::query()
                    ->where('rol', 'coordinacion')
                    ->where('activo', true)
                    ->count();

                if ($coordinacionesActivas <= 1) {
                    $validator->errors()->add(
                        'rol',
                        'Debe existir al menos una cuenta activa de Coordinación.'
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del usuario es obligatorio.',
            'nombre.max' => 'El nombre no debe superar los 255 caracteres.',

            'correo.required' => 'El correo del usuario es obligatorio.',
            'correo.email' => 'El correo debe tener un formato válido.',
            'correo.max' => 'El correo no debe superar los 255 caracteres.',
            'correo.unique' => 'Ya existe un usuario con este correo.',

            'rol.required' => 'El rol del usuario es obligatorio.',
            'rol.in' => 'El rol seleccionado no es válido.',

            'password.required' => 'La contraseña inicial es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',

            'activo.boolean' => 'El estado activo no tiene un valor válido.',

            'tutor_id.integer' => 'El tutor seleccionado no es válido.',
            'tutor_id.exists' => 'El tutor seleccionado no existe, no está activo o no está disponible.',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'correo' => 'correo',
            'rol' => 'rol',
            'password' => 'contraseña',
            'activo' => 'activo',
            'tutor_id' => 'tutor vinculado',
        ];
    }
}
