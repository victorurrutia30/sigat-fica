<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">
                Consolidados
            </h2>
            <p class="text-sm text-gray-500">
                Revisión del estado de entrega de consolidados por tutor y periodo.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            <div class="mb-6 grid gap-4 md:grid-cols-5">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $metricas['total'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Pendientes</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $metricas['pendientes'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Entregados</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $metricas['entregados'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Con observaciones</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $metricas['con_observaciones'] }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Atrasados</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $metricas['atrasados'] }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('consolidados.index') }}" class="mb-5 grid gap-3 md:grid-cols-4 md:items-end">
                        <div>
                            <label for="periodo_id" class="form-label">
                                Periodo
                            </label>

                            <select name="periodo_id" id="periodo_id" class="input-field">
                                <option value="">Todos</option>

                                @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" @selected((string) $periodoId===(string) $periodo->id)>
                                    {{ $periodo->nombre }}
                                    —
                                    {{ $periodo->ciclo?->nombre }}
                                    {{ $periodo->activo ? '(Activo)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="form-label">
                                Estado
                            </label>

                            <select name="estado" id="estado" class="input-field">
                                <option value="">Todos</option>
                                <option value="pendiente" @selected($estado==='pendiente' )>Pendiente</option>
                                <option value="entregado" @selected($estado==='entregado' )>Entregado</option>
                                <option value="con_observaciones" @selected($estado==='con_observaciones' )>Con observaciones</option>
                            </select>
                        </div>

                        <div>
                            <label for="busqueda" class="form-label">
                                Buscar tutor
                            </label>

                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Nombre, código o correo">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('consolidados.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    @if($periodoId)
                    <div class="mb-5 flex flex-wrap justify-end gap-3">
                        <a
                            href="{{ route('consolidados.periodos.exportar-institucional', $periodoId) }}"
                            class="btn-primary">
                            Exportar consolidado institucional del periodo
                        </a>
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Tutor</th>
                                    <th class="th-utec">Periodo</th>
                                    <th class="th-utec">Casos</th>
                                    <th class="th-utec">Entrega</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec">Atraso</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($consolidados as $consolidado)
                                @php
                                $limite = $consolidado->periodoEvaluacion?->fecha_limite_consolidado;
                                $atrasado = $limite
                                && $limite->startOfDay()->lt(now()->startOfDay())
                                && $consolidado->estado_entrega !== 'entregado';

                                $badgeConsolidado = [
                                'pendiente' => 'badge-warning',
                                'entregado' => 'badge-success',
                                'con_observaciones' => 'badge-info',
                                ];
                                @endphp

                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec">
                                        <div class="font-semibold">
                                            {{ $consolidado->tutor?->nombre_completo }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $consolidado->tutor?->codigo_empleado }}
                                            —
                                            {{ $consolidado->tutor?->correo_institucional }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div class="font-semibold">
                                            {{ $consolidado->periodoEvaluacion?->nombre }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $consolidado->periodoEvaluacion?->ciclo?->nombre }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div class="text-sm">
                                            Total: {{ $consolidado->casos_total_count }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Cerrados: {{ $consolidado->casos_cerrados_count }}
                                            /
                                            Abiertos: {{ $consolidado->casos_abiertos_count }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        @if($consolidado->entregado_en)
                                        {{ $consolidado->entregado_en->format('d/m/Y H:i') }}
                                        @else
                                        Pendiente
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <span class="{{ $badgeConsolidado[$consolidado->estado_entrega] ?? 'badge-muted' }}">
                                            {{ ucfirst(str_replace('_', ' ', $consolidado->estado_entrega)) }}
                                        </span>
                                    </td>

                                    <td class="td-utec">
                                        @if($atrasado)
                                        <span class="badge-danger">Atrasado</span>
                                        @else
                                        <span class="badge-muted">Sin atraso</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div class="flex justify-end">
                                            <a href="{{ route('consolidados.show', $consolidado) }}" class="link-utec">
                                                Revisar
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay consolidados registrados para los filtros seleccionados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $consolidados->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>