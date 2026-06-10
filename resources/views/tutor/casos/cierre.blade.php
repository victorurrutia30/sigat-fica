<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-utec-gray-dark">
                Cerrar caso de seguimiento
            </h2>
            <p class="text-sm text-gray-500">
                Completa los datos requeridos para el consolidado institucional.
            </p>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-10">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if(session('error'))
            <div class="alert-error mb-6">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error mb-6">
                Revisa los campos marcados antes de cerrar el caso.
            </div>
            @endif

            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Datos del caso
                    </h3>
                </div>

                <div class="card-body">
                    <dl class="grid gap-4 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estudiante</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                {{ $caso->estudiante?->nombre_completo }}
                                <span class="block text-xs text-gray-500">
                                    Carné: {{ $caso->estudiante?->carne }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Sección</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                {{ $caso->seccion?->materia?->codigo }} —
                                {{ $caso->seccion?->materia?->nombre }}
                                <span class="block text-xs text-gray-500">
                                    Sección {{ $caso->seccion?->numero_seccion }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Periodo</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                {{ $caso->periodoEvaluacion?->nombre }}
                                <span class="block text-xs text-gray-500">
                                    Ciclo {{ $caso->periodoEvaluacion?->ciclo?->nombre }}
                                </span>
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Gestiones registradas</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                {{ $caso->gestiones->count() }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Información de cierre
                    </h3>
                </div>

                <div class="card-body">
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
                                <label for="resultado_consolidado" class="form-label">
                                    Resultado para consolidado <span class="text-red-500">*</span>
                                </label>

                                <select name="resultado_consolidado" id="resultado_consolidado" class="input-field" required>
                                    <option value="">Seleccione...</option>

                                    <option value="rc" @selected(old('resultado_consolidado', $caso->resultado_consolidado) === 'rc')>
                                        R/C — Retiro de ciclo
                                    </option>

                                    <option value="rm" @selected(old('resultado_consolidado', $caso->resultado_consolidado) === 'rm')>
                                        R/M — Retiro de materia
                                    </option>

                                    <option value="abm" @selected(old('resultado_consolidado', $caso->resultado_consolidado) === 'abm')>
                                        AB/M — Abandono de materia
                                    </option>

                                    <option value="abc" @selected(old('resultado_consolidado', $caso->resultado_consolidado) === 'abc')>
                                        AB/C — Abandono del ciclo
                                    </option>
                                </select>

                                @error('resultado_consolidado')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Este valor marcará la columna institucional R/C, R/M, AB/M o AB/C.
                                </p>
                            </div>

                            <div class="md:col-span-2">
                                <label for="detalle_inasistencia" class="form-label">
                                    Detalle de inasistencia a la evaluación <span class="text-red-500">*</span>
                                </label>

                                <textarea
                                    name="detalle_inasistencia"
                                    id="detalle_inasistencia"
                                    rows="4"
                                    maxlength="2000"
                                    class="input-field"
                                    required
                                    placeholder="Ej. El estudiante no se presentó a la primera evaluación ordinaria. Se intentó contacto por WhatsApp y correo institucional.">{{ old('detalle_inasistencia', $caso->detalle_inasistencia) }}</textarea>

                                @error('detalle_inasistencia')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Este texto aparecerá en la columna “Detalle de inasistencia a primera evaluación”.
                                </p>
                            </div>

                            <div>
                                <label for="matricula" class="form-label">
                                    Matrícula <span class="text-red-500">*</span>
                                </label>

                                <select name="matricula" id="matricula" class="input-field" required>
                                    <option value="">Seleccione...</option>

                                    <option value="1" @selected((string) old('matricula', is_null($caso->matricula) ? '' : (int) $caso->matricula) === '1')>
                                        Sí
                                    </option>

                                    <option value="0" @selected((string) old('matricula', is_null($caso->matricula) ? '' : (int) $caso->matricula) === '0')>
                                        No
                                    </option>
                                </select>

                                @error('matricula')
                                <p class="form-error">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="cuota_cancelada" class="form-label">
                                    Nº cuota cancelada
                                </label>

                                <input
                                    type="number"
                                    name="cuota_cancelada"
                                    id="cuota_cancelada"
                                    value="{{ old('cuota_cancelada', $caso->cuota_cancelada) }}"
                                    min="0"
                                    max="99"
                                    step="1"
                                    class="input-field"
                                    placeholder="Ej. 1, 2, 3">

                                @error('cuota_cancelada')
                                <p class="form-error">{{ $message }}</p>
                                @enderror

                                <p class="form-hint">
                                    Campo opcional. Si no se conoce la cuota cancelada, déjalo vacío.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap justify-end gap-3">
                            <a href="{{ route('casos.show', $caso) }}" class="btn-secondary">
                                Cancelar
                            </a>

                            <button
                                type="submit"
                                class="btn-primary"
                                onclick="return confirm('¿Seguro que deseas cerrar este caso? Después no podrás agregar más gestiones.')">
                                Cerrar caso
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="alert-info mt-6">
                Al cerrar el caso, el sistema guardará el resultado institucional y también calculará el resultado interno:
                R/C y R/M se guardan como retiro; AB/M y AB/C se guardan como abandono.
            </div>
        </div>
    </div>
</x-app-layout>