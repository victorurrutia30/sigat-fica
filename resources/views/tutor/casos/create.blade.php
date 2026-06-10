<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">
                Nuevo caso de seguimiento
            </h2>
            <p class="text-sm text-gray-500">
                Registra un estudiante no evaluado para una de tus secciones asignadas.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 grid gap-4 md:grid-cols-2">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Periodo activo</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $periodo->nombre }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $periodo->fecha_inicio->format('d/m/Y') }}
                            -
                            {{ $periodo->fecha_fin->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Tutor</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $tutor->nombre_completo }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $tutor->correo_institucional }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Datos del caso
                    </h3>
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ route('casos.store') }}"
                        x-data="{
        carne: '{{ old('carne') }}',
        correo: '{{ old('correo') }}',
        normalizarCarne() {
            let digitos = (this.carne || '').replace(/\D/g, '').slice(0, 10);

            if (digitos.length <= 2) {
                this.carne = digitos;
            } else if (digitos.length <= 6) {
                this.carne = digitos.slice(0, 2) + '-' + digitos.slice(2);
            } else {
                this.carne = digitos.slice(0, 2) + '-' + digitos.slice(2, 6) + '-' + digitos.slice(6, 10);
            }

            if (digitos.length === 10) {
                this.correo = digitos + '@mail.utec.edu.sv';
            }
        }
    }">
                        @csrf

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="seccion_id" class="form-label">
                                    Sección asignada <span class="text-red-500">*</span>
                                </label>

                                <select name="seccion_id" id="seccion_id" class="input-field" required>
                                    <option value="">Seleccione...</option>

                                    @foreach($secciones as $seccion)
                                    <option value="{{ $seccion->id }}" @selected((string) old('seccion_id')===(string) $seccion->id)>
                                        {{ $seccion->materia?->codigo }}
                                        —
                                        {{ $seccion->materia?->nombre }}
                                        /
                                        Sección {{ $seccion->numero_seccion }}
                                        /
                                        {{ ucfirst(str_replace('_', ' ', $seccion->modalidad)) }}
                                    </option>
                                    @endforeach
                                </select>

                                @error('seccion_id')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Solo aparecen secciones asignadas a tu usuario mediante una propuesta publicada.
                                </p>
                            </div>

                            <div>
                                <label for="carne" class="form-label">
                                    Carné <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="carne"
                                    id="carne"
                                    x-model="carne"
                                    x-on:input="normalizarCarne"
                                    class="input-field"
                                    inputmode="numeric"
                                    maxlength="12"
                                    placeholder="Ej. 27-4855-2026"
                                    required>

                                @error('carne')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Escribe 10 dígitos. El sistema lo formatea como 27-4855-2026.
                                </p>
                            </div>
                            <div>
                                <label for="nombres" class="form-label">
                                    Nombres <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="nombres"
                                    id="nombres"
                                    value="{{ old('nombres') }}"
                                    class="input-field"
                                    maxlength="100"
                                    placeholder="Ej. Juan Carlos"
                                    required>

                                @error('nombres')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="apellidos" class="form-label">
                                    Apellidos <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="text"
                                    name="apellidos"
                                    id="apellidos"
                                    value="{{ old('apellidos') }}"
                                    class="input-field"
                                    maxlength="100"
                                    placeholder="Ej. Pérez López"
                                    required>

                                @error('apellidos')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="correo" class="form-label">
                                    Correo
                                </label>

                                <input
                                    type="email"
                                    name="correo"
                                    id="correo"
                                    x-model="correo"
                                    class="input-field bg-gray-50"
                                    maxlength="191"
                                    placeholder="2748552026@mail.utec.edu.sv"
                                    readonly
                                    required>

                                @error('correo')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Se genera automáticamente con el carné sin guiones: carnet@mail.utec.edu.sv.
                                </p>

                            </div>

                            <div>
                                <label for="carrera" class="form-label">
                                    Carrera
                                </label>

                                <input
                                    type="text"
                                    name="carrera"
                                    id="carrera"
                                    value="{{ old('carrera') }}"
                                    class="input-field"
                                    maxlength="150"
                                    placeholder="Ej. Ingeniería en Sistemas">

                                @error('carrera')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 rounded-md border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                            Si el carné ya existe, el sistema reutilizará el estudiante y actualizará sus datos básicos.
                            No se permitirá duplicar el mismo estudiante en la misma sección y periodo.
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('casos.index') }}" class="btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn-primary">
                                Guardar caso
                            </button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>
</x-app-layout>