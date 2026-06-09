<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Revisión de consolidado
                </h2>
                <p class="text-sm text-gray-500">
                    Detalle de casos, gestiones y observaciones del consolidado.
                </p>
            </div>

            <a href="{{ route('consolidados.index', ['periodo_id' => $consolidado->periodo_evaluacion_id]) }}" class="btn-secondary">
                Volver
            </a>
        </div>
    </x-slot>

    @php
    $badgeConsolidado = [
    'pendiente' => 'badge-warning',
    'entregado' => 'badge-success',
    'con_observaciones' => 'badge-info',
    ];
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            <div class="mb-6 grid gap-4 md:grid-cols-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Tutor</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $consolidado->tutor?->nombre_completo }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $consolidado->tutor?->correo_institucional }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Periodo</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $consolidado->periodoEvaluacion?->nombre }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $consolidado->periodoEvaluacion?->ciclo?->nombre }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Estado</p>
                        <p class="mt-2">
                            <span class="{{ $badgeConsolidado[$consolidado->estado_entrega] ?? 'badge-muted' }}">
                                {{ ucfirst(str_replace('_', ' ', $consolidado->estado_entrega)) }}
                            </span>
                        </p>

                        @if($consolidado->entregado_en)
                        <p class="mt-2 text-sm text-gray-500">
                            Entregado: {{ $consolidado->entregado_en->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Casos completos</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['cerrados'] }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            de {{ $diagnostico['total'] }} registrados
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Casos del consolidado
                            </h3>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-utec-gray-medium">
                                <thead>
                                    <tr>
                                        <th class="th-utec">Estudiante</th>
                                        <th class="th-utec">Sección</th>
                                        <th class="th-utec">Causa</th>
                                        <th class="th-utec">Resultado</th>
                                        <th class="th-utec">Gestiones</th>
                                        <th class="th-utec">Estado</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-utec-gray-medium bg-white">
                                    @forelse($casos as $caso)
                                    <tr class="hover:bg-utec-primary-soft">
                                        <td class="td-utec">
                                            <div class="font-semibold">
                                                {{ $caso->estudiante?->nombre_completo }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $caso->estudiante?->carne }}
                                            </div>
                                        </td>

                                        <td class="td-utec">
                                            <div class="font-semibold">
                                                {{ $caso->seccion?->materia?->codigo }}
                                                —
                                                {{ $caso->seccion?->numero_seccion }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $caso->seccion?->materia?->nombre }}
                                            </div>
                                        </td>

                                        <td class="td-utec">
                                            {{ $caso->causa?->nombre ?? 'Pendiente' }}
                                        </td>

                                        <td class="td-utec">
                                            {{ $caso->resultado_final ? ucfirst($caso->resultado_final) : 'Pendiente' }}
                                        </td>

                                        <td class="td-utec">
                                            {{ $caso->gestiones->count() }}
                                        </td>

                                        <td class="td-utec">
                                            @if($caso->cerrado && $caso->causa_id && $caso->resultado_final && $caso->gestiones->isNotEmpty())
                                            <span class="badge-success">Completo</span>
                                            @else
                                            <span class="badge-warning">Incompleto</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                            El tutor no registró casos para este periodo.
                                            @if($consolidado->sin_casos)
                                            Existe constancia expresa de ausencia de casos.
                                            @endif
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Gestiones registradas
                            </h3>
                        </div>

                        <div class="card-body">
                            @forelse($casos as $caso)
                            <div class="mb-5 rounded-lg border border-utec-gray-medium bg-white p-4">
                                <div class="mb-3">
                                    <p class="font-semibold text-utec-primary">
                                        {{ $caso->estudiante?->nombre_completo }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $caso->seccion?->materia?->codigo }}
                                        /
                                        Sección {{ $caso->seccion?->numero_seccion }}
                                    </p>
                                </div>

                                @if($caso->gestiones->isEmpty())
                                <p class="text-sm text-gray-500">
                                    Sin gestiones registradas.
                                </p>
                                @else
                                <div class="space-y-3">
                                    @foreach($caso->gestiones as $gestion)
                                    <div class="rounded-md border border-utec-gray-medium bg-gray-50 p-3 text-sm">
                                        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                                            <span class="font-semibold text-utec-gray-dark">
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
                                @endif
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">
                                No hay gestiones porque no hay casos registrados.
                            </p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Diagnóstico
                            </h3>
                        </div>

                        <div class="card-body">
                            <dl class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Total de casos</dt>
                                    <dd class="font-semibold text-utec-gray-dark">{{ $diagnostico['total'] }}</dd>
                                </div>

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Cerrados</dt>
                                    <dd class="font-semibold text-utec-gray-dark">{{ $diagnostico['cerrados'] }}</dd>
                                </div>

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Abiertos</dt>
                                    <dd class="font-semibold text-utec-gray-dark">{{ $diagnostico['abiertos'] }}</dd>
                                </div>

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Incompletos</dt>
                                    <dd class="font-semibold text-utec-gray-dark">{{ $diagnostico['incompletos'] }}</dd>
                                </div>

                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Constancia sin casos</dt>
                                    <dd class="font-semibold text-utec-gray-dark">
                                        {{ $consolidado->sin_casos ? 'Sí' : 'No' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    @if($diagnostico['incompletos'] > 0)
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Campos faltantes
                            </h3>
                        </div>

                        <div class="card-body">
                            <div class="space-y-3">
                                @foreach($diagnostico['detalle_incompletos'] as $item)
                                <div class="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                                    <p class="font-semibold">
                                        {{ $item['caso']->estudiante?->nombre_completo }}
                                    </p>
                                    <p class="mt-1">
                                        Falta: {{ implode(', ', $item['faltantes']) }}.
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Observación de Coordinación
                            </h3>
                        </div>

                        <div class="card-body">
                            @if($consolidado->observaciones_coord)
                            <div class="mb-4 rounded-md border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                                {{ $consolidado->observaciones_coord }}
                            </div>
                            @endif

                            <form method="POST" action="{{ route('consolidados.observacion', $consolidado) }}">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="observaciones_coord" class="form-label">
                                        Observación <span class="text-red-500">*</span>
                                    </label>

                                    <textarea
                                        name="observaciones_coord"
                                        id="observaciones_coord"
                                        rows="5"
                                        maxlength="2000"
                                        class="input-field"
                                        required>{{ old('observaciones_coord', $consolidado->observaciones_coord) }}</textarea>

                                    @error('observaciones_coord')
                                    <p class="form-error">{{ $message }}</p>
                                    @enderror

                                    <p class="form-hint">
                                        Al guardar, el consolidado quedará en estado “con observaciones”.
                                    </p>
                                </div>

                                <div class="mt-5">
                                    <button type="submit" class="btn-primary w-full">
                                        Guardar observación
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>