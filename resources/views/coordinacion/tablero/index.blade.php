<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Tablero de cumplimiento
                </h2>
                <p class="text-sm text-gray-500">
                    Seguimiento general de entregas, atrasos y avance por tutor.
                </p>
            </div>

            <a href="{{ route('consolidados.index') }}" class="btn-secondary">
                Ver consolidados
            </a>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if($periodos->isEmpty())
            <div class="alert-warning">
                No hay periodos de evaluación registrados. Debe crear un periodo antes de usar el tablero.
            </div>
            @endif

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">{{ $metricas['total'] }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pendientes</p>
                        <p class="mt-2 text-3xl font-bold text-orange-700">{{ $metricas['pendientes'] }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">En progreso</p>
                        <p class="mt-2 text-3xl font-bold text-blue-700">{{ $metricas['en_progreso'] }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Entregados</p>
                        <p class="mt-2 text-3xl font-bold text-green-700">{{ $metricas['entregados'] }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Observaciones</p>
                        <p class="mt-2 text-3xl font-bold text-blue-700">{{ $metricas['con_observaciones'] }}</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Atrasados</p>
                        <p class="mt-2 text-3xl font-bold text-red-700">{{ $metricas['atrasados'] }}</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Filtros
                    </h3>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('tablero.index') }}" class="grid gap-4 md:grid-cols-4">
                        <div>
                            <label for="periodo_id" class="form-label">Periodo</label>
                            <select name="periodo_id" id="periodo_id" class="input-field">
                                <option value="">Periodo activo</option>
                                @foreach($periodos as $periodo)
                                <option value="{{ $periodo->id }}" @selected((int) $periodoId===(int) $periodo->id)>
                                    {{ $periodo->ciclo?->nombre ?? 'Sin ciclo' }} — {{ $periodo->nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="input-field">
                                <option value="">Todos</option>
                                <option value="pendiente" @selected($estado==='pendiente' )>Pendiente</option>
                                <option value="en_progreso" @selected($estado==='en_progreso' )>En progreso</option>
                                <option value="entregado" @selected($estado==='entregado' )>Entregado</option>
                                <option value="con_observaciones" @selected($estado==='con_observaciones' )>Con observaciones</option>
                                <option value="atrasado" @selected($estado==='atrasado' )>Atrasado</option>
                            </select>
                        </div>

                        <div>
                            <label for="busqueda" class="form-label">Buscar tutor</label>
                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Nombre, código o correo">
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('tablero.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Cumplimiento por tutor
                    </h3>
                </div>

                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Tutor</th>
                                    <th class="th-utec">Periodo</th>
                                    <th class="th-utec">Fecha límite</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-center">Casos</th>
                                    <th class="th-utec text-center">Cerrados</th>
                                    <th class="th-utec text-center">Pendientes</th>
                                    <th class="th-utec text-center">Incompletos</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($registros as $registro)
                                @php
                                $estadoCumplimiento = $registro->estado_cumplimiento;

                                $badgeEstado = match ($estadoCumplimiento) {
                                'entregado' => 'badge-success',
                                'con_observaciones' => 'badge-warning',
                                'atrasado' => 'badge-danger',
                                'en_progreso' => 'badge-warning',
                                default => 'badge-muted',
                                };

                                $textoEstado = match ($estadoCumplimiento) {
                                'entregado' => 'Entregado',
                                'con_observaciones' => 'Con observaciones',
                                'atrasado' => 'Atrasado',
                                'en_progreso' => 'En progreso',
                                default => 'Pendiente',
                                };
                                @endphp

                                <tr class="tr-utec">
                                    <td class="td-utec">
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $registro->tutor?->nombre_completo ?? 'Tutor no definido' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $registro->tutor?->correo_institucional ?? 'Sin correo' }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div>
                                            {{ $registro->periodoEvaluacion?->nombre ?? 'Sin periodo' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $registro->periodoEvaluacion?->ciclo?->nombre ?? 'Sin ciclo' }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        {{ $registro->periodoEvaluacion?->fecha_limite_consolidado?->format('d/m/Y') ?? 'No definida' }}
                                    </td>

                                    <td class="td-utec">
                                        <span class="{{ $badgeEstado }}">
                                            {{ $textoEstado }}
                                        </span>
                                    </td>

                                    <td class="td-utec text-center">
                                        {{ (int) $registro->casos_total_count }}
                                    </td>

                                    <td class="td-utec text-center">
                                        {{ (int) $registro->casos_cerrados_count }}
                                    </td>

                                    <td class="td-utec text-center">
                                        {{ (int) $registro->casos_pendientes_count }}
                                    </td>

                                    <td class="td-utec text-center">
                                        {{ (int) $registro->casos_incompletos_count }}
                                    </td>

                                    <td class="td-utec text-right">
                                        <a href="{{ route('consolidados.show', $registro) }}" class="link-utec">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay registros de cumplimiento para los filtros seleccionados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $registros->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>