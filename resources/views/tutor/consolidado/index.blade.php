<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-utec-gray-dark">
                Mi consolidado
            </h2>
            <p class="text-sm text-gray-500">
                Revisión y entrega del consolidado del periodo activo.
            </p>
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

            @if($mensajeBloqueo)
            <div class="alert-error">
                {{ $mensajeBloqueo }}
            </div>
            @else
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
                        <p class="text-sm font-medium text-gray-500">Casos cerrados</p>
                        <p class="mt-3 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['cerrados'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Casos abiertos</p>
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
                            Información del periodo
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Periodo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $periodo?->nombre }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Ciclo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $periodo?->ciclo?->nombre }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Fecha límite</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $periodo?->fecha_limite_consolidado?->format('d/m/Y') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tutor</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $tutor?->nombre_completo }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Estado de entrega
                        </h3>
                    </div>

                    <div class="card-body">
                        @php
                        $estado = $consolidado?->estado_entrega;
                        @endphp

                        @if($estado === 'entregado')
                        <span class="badge-success">Entregado</span>
                        @elseif($estado === 'con_observaciones')
                        <span class="badge-warning">Con observaciones</span>
                        @else
                        <span class="badge-muted">Pendiente</span>
                        @endif

                        <p class="mt-3 text-sm text-gray-500">
                            @if($consolidado?->entregado_en)
                            Entregado el {{ $consolidado->entregado_en->format('d/m/Y H:i') }}.
                            @else
                            Aún no se ha entregado este consolidado.
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            @if($consolidado?->observaciones_coord)
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Observaciones de Coordinación
                    </h3>
                </div>

                <div class="card-body">
                    <p class="whitespace-pre-line text-sm text-utec-gray-dark">
                        {{ $consolidado->observaciones_coord }}
                    </p>
                </div>
            </div>
            @endif

            @if($diagnostico['incompletos'] > 0)
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Casos incompletos
                    </h3>
                </div>

                <div class="card-body">
                    <p class="mb-4 text-sm text-gray-500">
                        No se puede entregar el consolidado mientras existan casos con información pendiente.
                    </p>

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

                            <a href="{{ route('casos.show', $casoIncompleto) }}" class="link-utec mt-2 inline-block">
                                Ver caso
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            @if(isset($coberturaSecciones))
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Cobertura por sección asignada
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Antes de entregar, cada sección debe tener casos registrados o confirmación de que todos realizaron la evaluación.
                    </p>
                </div>

                <div class="card-body">
                    <div class="mb-4 grid gap-4 md:grid-cols-4">
                        <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Secciones asignadas</p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">
                                {{ $coberturaSecciones['total_secciones'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Con casos</p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">
                                {{ $coberturaSecciones['con_casos'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Sin casos confirmadas</p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">
                                {{ $coberturaSecciones['sin_casos_confirmadas'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                            <p class="text-xs text-gray-500">Pendientes</p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">
                                {{ $coberturaSecciones['pendientes_confirmacion'] }}
                            </p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Materia / sección</th>
                                    <th class="th-utec">Casos</th>
                                    <th class="th-utec">Estado de cobertura</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @foreach($coberturaSecciones['detalle'] as $fila)
                                @php
                                $seccionCobertura = $fila['seccion'];
                                @endphp

                                <tr>
                                    <td class="td-utec">
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $seccionCobertura->materia?->codigo }}
                                            —
                                            {{ $seccionCobertura->materia?->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Sección {{ $seccionCobertura->numero_seccion }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        {{ $fila['casos_total'] }}
                                    </td>

                                    <td class="td-utec">
                                        @if($fila['casos_total'] > 0)
                                        <span class="badge-success">Con casos registrados</span>
                                        @elseif($fila['confirmada_sin_casos'])
                                        <span class="badge-success">Todos realizaron evaluación</span>
                                        @else
                                        <span class="badge-warning">Requiere confirmación</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($coberturaSecciones['pendientes_confirmacion'] > 0)
                    <div class="alert-warning mt-5">
                        Hay secciones sin casos registrados. Antes de entregar, debes confirmar cuáles no tuvieron estudiantes no evaluados.
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Casos incluidos en el consolidado
                    </h3>
                </div>

                <div class="card-body">
                    @if($casos->isEmpty())
                    <p class="text-sm text-gray-500">
                        No hay casos registrados para este periodo.
                    </p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Estudiante</th>
                                    <th class="th-utec">Materia / sección</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec">Resultado institucional</th>
                                    <th class="th-utec">Matrícula</th>
                                    <th class="th-utec">Cuota</th>
                                    <th class="th-utec">Causa</th>
                                    <th class="th-utec">Detalle inasistencia</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @foreach($casos as $caso)
                                <tr>
                                    <td class="td-utec">
                                        <div class="font-medium text-utec-gray-dark">
                                            {{ $caso->estudiante?->nombre_completo }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Carné: {{ $caso->estudiante?->carne }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div>
                                            {{ $caso->seccion?->materia?->codigo }}
                                            —
                                            {{ $caso->seccion?->materia?->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Sección {{ $caso->seccion?->numero_seccion }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        @if($caso->cerrado)
                                        <span class="badge-success">Cerrado</span>
                                        @else
                                        <span class="badge-warning">Abierto</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        {{ $caso->resultadoConsolidadoTexto() }}
                                    </td>

                                    <td class="td-utec">
                                        @if(is_null($caso->matricula))
                                        Pendiente
                                        @else
                                        {{ $caso->matricula ? 'Sí' : 'No' }}
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        {{ $caso->cuota_cancelada ?: 'No registrada' }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $caso->causa?->nombre ?: 'Pendiente' }}
                                    </td>

                                    <td class="td-utec">
                                        <span class="line-clamp-3">
                                            {{ $caso->detalle_inasistencia ?: 'Pendiente' }}
                                        </span>
                                    </td>

                                    <td class="td-utec text-right">
                                        <a href="{{ route('casos.show', $caso) }}" class="link-utec">
                                            Ver
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

            @if($consolidado?->estado_entrega !== 'entregado')
            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Entrega del consolidado
                    </h3>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('consolidado.entregar') }}">
                        @csrf
                        @method('PATCH')

                        @if(isset($coberturaSecciones) && $coberturaSecciones['pendientes_confirmacion'] > 0)
                        <div class="mb-5 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                            <p class="mb-3 text-sm font-semibold text-yellow-900">
                                Confirma las secciones donde no hubo estudiantes no evaluados:
                            </p>

                            <div class="space-y-3">
                                @foreach($coberturaSecciones['detalle'] as $fila)
                                @if($fila['requiere_confirmacion'])
                                @php
                                $seccionCobertura = $fila['seccion'];
                                @endphp

                                <label class="flex items-start gap-3">
                                    <input
                                        type="checkbox"
                                        name="secciones_sin_casos[]"
                                        value="{{ $seccionCobertura->id }}"
                                        class="mt-1 rounded border-gray-300 text-utec-primary shadow-sm focus:ring-utec-primary"
                                        @checked(in_array((string) $seccionCobertura->id, old('secciones_sin_casos', []), true))>

                                    <span class="text-sm text-yellow-800">
                                        Confirmo que en
                                        <span class="font-semibold">
                                            {{ $seccionCobertura->materia?->codigo }}
                                            —
                                            {{ $seccionCobertura->materia?->nombre }}
                                            / Sección {{ $seccionCobertura->numero_seccion }}
                                        </span>
                                        todos los estudiantes realizaron la evaluación.
                                    </span>
                                </label>
                                @endif
                                @endforeach
                            </div>

                            @error('secciones_sin_casos')
                            <p class="form-error mt-3">{{ $message }}</p>
                            @enderror
                        </div>
                        @endif

                        @if($diagnostico['incompletos'] > 0)
                        <div class="alert-error mb-5">
                            No se puede entregar porque existen casos incompletos.
                        </div>
                        @endif

                        <div class="flex justify-end">
                            <button
                                type="submit"
                                class="btn-primary"
                                @disabled($diagnostico['incompletos']> 0)
                                onclick="return confirm('¿Seguro que deseas entregar este consolidado?')"
                                >
                                Entregar consolidado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>