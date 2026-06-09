<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Registrar gestión
                </h2>
                <p class="text-sm text-gray-500">
                    Agrega una acción realizada para el caso de seguimiento.
                </p>
            </div>

            <a href="{{ route('casos.show', $caso) }}" class="btn-secondary">
                Volver al caso
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Estudiante</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $caso->estudiante?->nombre_completo }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Carné: {{ $caso->estudiante?->carne }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Sección</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $caso->seccion?->materia?->codigo }}
                            —
                            {{ $caso->seccion?->numero_seccion }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $caso->seccion?->materia?->nombre }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Periodo</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $caso->periodoEvaluacion?->nombre }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Ciclo {{ $caso->periodoEvaluacion?->ciclo?->nombre }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Datos de la gestión
                    </h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('gestiones.store', $caso) }}">
                        @csrf

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="fecha_gestion" class="form-label">
                                    Fecha de gestión <span class="text-red-500">*</span>
                                </label>

                                <input
                                    type="date"
                                    name="fecha_gestion"
                                    id="fecha_gestion"
                                    value="{{ old('fecha_gestion', now()->toDateString()) }}"
                                    class="input-field"
                                    required>

                                @error('fecha_gestion')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="medio_contacto" class="form-label">
                                    Medio de contacto <span class="text-red-500">*</span>
                                </label>

                                <select name="medio_contacto" id="medio_contacto" class="input-field" required>
                                    <option value="">Seleccione...</option>
                                    <option value="llamada" @selected(old('medio_contacto')==='llamada' )>Llamada</option>
                                    <option value="correo" @selected(old('medio_contacto')==='correo' )>Correo</option>
                                    <option value="presencial" @selected(old('medio_contacto')==='presencial' )>Presencial</option>
                                    <option value="whatsapp" @selected(old('medio_contacto')==='whatsapp' )>WhatsApp</option>
                                    <option value="otro" @selected(old('medio_contacto')==='otro' )>Otro</option>
                                </select>

                                @error('medio_contacto')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="accion_realizada" class="form-label">
                                    Acción realizada <span class="text-red-500">*</span>
                                </label>

                                <textarea
                                    name="accion_realizada"
                                    id="accion_realizada"
                                    rows="5"
                                    class="input-field"
                                    maxlength="2000"
                                    placeholder="Describe qué se hizo: llamada, correo enviado, conversación con estudiante, seguimiento con docente titular, etc."
                                    required>{{ old('accion_realizada') }}</textarea>

                                @error('accion_realizada')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Este campo es obligatorio. El sistema no permite guardar una gestión sin descripción.
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="resultado" class="form-label">
                                    Resultado de la gestión
                                </label>

                                <textarea
                                    name="resultado"
                                    id="resultado"
                                    rows="4"
                                    class="input-field"
                                    maxlength="2000"
                                    placeholder="Opcional. Ej. Estudiante respondió, no respondió, solicitó apoyo, indicó retiro, etc.">{{ old('resultado') }}</textarea>

                                @error('resultado')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 rounded-md border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                            Registrar una gestión no cierra el caso. El cierre se implementa aparte y requerirá causa, resultado final y al menos una gestión.
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('casos.show', $caso) }}" class="btn-secondary">
                                Cancelar
                            </a>

                            <button type="submit" class="btn-primary">
                                Guardar gestión
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>