<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Cerrar caso de seguimiento
                </h2>
                <p class="text-sm text-gray-500">
                    Selecciona la causa identificada y el resultado final del caso.
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
                        <p class="text-xs text-gray-500">Gestiones registradas</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $caso->gestiones->count() }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Requisito mínimo cumplido.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Datos de cierre
                    </h3>
                </div>

                <div class="card-body">
                    <div class="mb-5 rounded-md border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                        Al cerrar el caso, ya no se podrán agregar nuevas gestiones. Verifica que la información sea correcta.
                    </div>

                    <form method="POST" action="{{ route('casos.cerrar', $caso) }}">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label for="causa_id" class="form-label">
                                    Causa identificada <span class="text-red-500">*</span>
                                </label>

                                <select name="causa_id" id="causa_id" class="input-field" required>
                                    <option value="">Seleccione...</option>

                                    @foreach($causas as $causa)
                                    <option value="{{ $causa->id }}" @selected((string) old('causa_id', $caso->causa_id) === (string) $causa->id)>
                                        {{ $causa->nombre }}
                                    </option>
                                    @endforeach
                                </select>

                                @error('causa_id')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Solo se muestran causas activas del catálogo.
                                </p>
                            </div>

                            <div>
                                <label for="resultado_final" class="form-label">
                                    Resultado final <span class="text-red-500">*</span>
                                </label>

                                <select name="resultado_final" id="resultado_final" class="input-field" required>
                                    <option value="">Seleccione...</option>
                                    <option value="retiro" @selected(old('resultado_final', $caso->resultado_final) === 'retiro')>
                                        Retiro
                                    </option>
                                    <option value="abandono" @selected(old('resultado_final', $caso->resultado_final) === 'abandono')>
                                        Abandono
                                    </option>
                                </select>

                                @error('resultado_final')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 rounded-md border border-utec-gray-medium bg-gray-50 p-4">
                            <h4 class="text-sm font-semibold text-utec-gray-dark">
                                Resumen de gestiones
                            </h4>

                            <div class="mt-3 space-y-3">
                                @foreach($caso->gestiones as $gestion)
                                <div class="rounded-md border border-utec-gray-medium bg-white p-3 text-sm">
                                    <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                                        <span class="font-semibold text-utec-primary">
                                            {{ ucfirst(str_replace('_', ' ', $gestion->medio_contacto)) }}
                                        </span>

                                        <span class="text-xs text-gray-500">
                                            {{ $gestion->fecha_gestion->format('d/m/Y') }}
                                        </span>
                                    </div>

                                    <p class="mt-2 text-utec-gray-dark">
                                        {{ $gestion->accion_realizada }}
                                    </p>

                                    @if($gestion->resultado)
                                    <p class="mt-1 text-gray-600">
                                        <span class="font-semibold">Resultado:</span>
                                        {{ $gestion->resultado }}
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3">
                            <a href="{{ route('casos.show', $caso) }}" class="btn-secondary">
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="btn-primary"
                                onclick="return confirm('¿Seguro que deseas cerrar este caso?')">
                                Cerrar caso
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>