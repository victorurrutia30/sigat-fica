<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-utec-gray-dark">
                    Revisión de consolidado
                </h2>
                <p class="text-sm text-gray-500">
                    Detalle de casos, gestiones y observaciones de Coordinación.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('consolidados.periodos.exportar-institucional', $consolidado->periodo_evaluacion_id) }}"
                    class="btn-primary">
                    Exportar periodo
                </a>

                <a href="{{ route('consolidados.index', ['periodo_id' => $consolidado->periodo_evaluacion_id]) }}" class="btn-secondary">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-6">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error mb-6">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="mb-6 grid gap-4 md:grid-cols-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Total de casos</p>
                        <p class="mt-3 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['total'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Cerrados</p>
                        <p class="mt-3 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['cerrados'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Abiertos</p>
                        <p class="mt-3 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['abiertos'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Incompletos</p>
                        <p class="mt-3 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['incompletos'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Información general
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tutor</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $consolidado->tutor?->nombre_completo }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Código empleado</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $consolidado->tutor?->codigo_empleado }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Periodo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $consolidado->periodoEvaluacion?->nombre }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ciclo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $consolidado->periodoEvaluacion?->ciclo?->nombre }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha límite</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $consolidado->periodoEvaluacion?->fecha_limite_consolidado?->format('d/m/Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Entregado en</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $consolidado->entregado_en?->format('d/m/Y H:i') ?: 'No entregado' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Estado
                        </h3>
                    </div>

                    <div class="card-body">
                        @if($consolidado->estado_entrega === 'entregado')
                        <span class="badge-success">Entregado</span>
                        @elseif($consolidado->estado_entrega === 'con_observaciones')
                        <span class="badge-warning">Con observaciones</span>
                        @else
                        <span class="badge-muted">Pendiente</span>
                        @endif

                        <p class="mt-3 text-sm text-gray-500">
                            @if($consolidado->sin_casos)
                            El tutor confirmó que no hubo estudiantes no evaluados.
                            @else
                            Consolidado con casos registrados o pendiente de confirmación.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if($diagnostico['incompletos'] > 0)
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Casos incompletos detectados
                    </h3>
                </div>

                <div class="card-body">
                    <div class="space-y-3">
                        @foreach($diagnostico['detalle_incompletos'] as $detalle)
                        @php
                        $casoIncompleto = $detalle['caso'];
                        @endphp

                        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
                            <p class="text-sm font-semibold text-red-800">
                                {{ $casoIncompleto->estudiante?->nombre_completo }}
                                <span class="font-normal">
                                    — {{ $casoIncompleto->seccion?->materia?->nombre }}
                                    / Sección {{ $casoIncompleto->seccion?->numero_seccion }}
                                </span>
                            </p>

                            <p class="mt-2 text-sm text-red-700">
                                Faltante:
                                {{ implode(', ', $detalle['faltantes']) }}.
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Observación de Coordinación
                    </h3>
                </div>

                <div class="card-body">
                    @if($consolidado->observaciones_coord)
                    <div class="mb-5 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                        <p class="whitespace-pre-line text-sm text-yellow-800">
                            {{ $consolidado->observaciones_coord }}
                        </p>
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
                                rows="4"
                                maxlength="2000"
                                class="input-field"
                                required
                                placeholder="Escribe observaciones para el tutor o para control administrativo.">{{ old('observaciones_coord', $consolidado->observaciones_coord) }}</textarea>

                            @error('observaciones_coord')
                            <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mt-5 flex justify-end">
                            <button type="submit" class="btn-primary">
                                Guardar observación
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Casos del consolidado
                    </h3>
                </div>

                <div class="card-body">
                    @if($casos->isEmpty())
                    <p class="text-sm text-gray-500">
                        No hay casos registrados para este tutor en el periodo seleccionado.
                    </p>
                    @else
                    <div class="space-y-5">
                        @foreach($casos as $caso)
                        <div class="rounded-xl border border-utec-gray-medium bg-white p-5">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <p class="font-semibold text-utec-gray-dark">
                                        {{ $caso->estudiante?->nombre_completo }}
                                    </p>

                                    <p class="text-sm text-gray-500">
                                        Carné: {{ $caso->estudiante?->carne }}
                                        @if($caso->estudiante?->correo)
                                        · {{ $caso->estudiante?->correo }}
                                        @endif
                                    </p>

                                    <p class="mt-1 text-sm text-gray-500">
                                        {{ $caso->seccion?->materia?->codigo }}
                                        —
                                        {{ $caso->seccion?->materia?->nombre }}
                                        · Sección {{ $caso->seccion?->numero_seccion }}
                                    </p>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($caso->cerrado)
                                    <span class="badge-success">Cerrado</span>
                                    @else
                                    <span class="badge-warning">Abierto</span>
                                    @endif

                                    @if($caso->resultado_consolidado)
                                    <span class="badge-info">
                                        {{ $caso->resultadoConsolidadoMarca() }}
                                    </span>
                                    @else
                                    <span class="badge-muted">Sin resultado institucional</span>
                                    @endif
                                </div>
                            </div>

                            <dl class="mt-5 grid gap-4 md:grid-cols-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Causa</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->causa?->nombre ?: 'Pendiente' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Resultado interno</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->resultadoFinalTexto() }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Resultado institucional</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->resultadoConsolidadoTexto() }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Matrícula</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        @if(is_null($caso->matricula))
                                        Pendiente
                                        @else
                                        {{ $caso->matricula ? 'Sí' : 'No' }}
                                        @endif
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nº cuota cancelada</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->cuota_cancelada ?: 'No registrada' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Gestiones</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->gestiones->count() }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Cerrado en</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->cerrado_en?->format('d/m/Y H:i') ?: 'Pendiente' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Carrera</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->estudiante?->carrera ?: 'No registrada' }}
                                    </dd>
                                </div>

                                <div class="md:col-span-4">
                                    <dt class="text-sm font-medium text-gray-500">Detalle de inasistencia</dt>
                                    <dd class="mt-1 whitespace-pre-line text-sm text-utec-gray-dark">
                                        {{ $caso->detalle_inasistencia ?: 'Pendiente' }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-5">
                                <h4 class="text-sm font-semibold text-utec-gray-dark">
                                    Gestiones registradas
                                </h4>

                                @if($caso->gestiones->isEmpty())
                                <p class="mt-2 text-sm text-gray-500">
                                    No hay gestiones registradas.
                                </p>
                                @else
                                <div class="mt-3 overflow-x-auto">
                                    <table class="min-w-full divide-y divide-utec-gray-medium">
                                        <thead>
                                            <tr>
                                                <th class="th-utec">Fecha</th>
                                                <th class="th-utec">Medio</th>
                                                <th class="th-utec">Acción realizada</th>
                                                <th class="th-utec">Resultado</th>
                                                <th class="th-utec">Registrado por</th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-utec-gray-medium bg-white">
                                            @foreach($caso->gestiones as $gestion)
                                            <tr>
                                                <td class="td-utec">
                                                    {{ $gestion->fecha_gestion?->format('d/m/Y') }}
                                                </td>

                                                <td class="td-utec">
                                                    {{ ucfirst(str_replace('_', ' ', $gestion->medio_contacto)) }}
                                                </td>

                                                <td class="td-utec">
                                                    {{ $gestion->accion_realizada }}
                                                </td>

                                                <td class="td-utec">
                                                    {{ $gestion->resultado ?: 'Sin resultado registrado' }}
                                                </td>

                                                <td class="td-utec">
                                                    {{ $gestion->registradoPor?->nombre ?: 'No disponible' }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>