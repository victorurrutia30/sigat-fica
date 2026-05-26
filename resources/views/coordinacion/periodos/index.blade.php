<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Periodos de evaluación
                </h2>
                <p class="text-sm text-gray-500">
                    Administra los periodos usados para seguimiento y consolidado de tutores.
                </p>
            </div>

            <a href="{{ route('periodos.create') }}" class="btn-primary">
                Nuevo periodo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('info'))
            <div class="alert-info mb-4">
                {{ session('info') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="alert-warning mb-4">
                {{ session('warning') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('periodos.index') }}" class="mb-5 grid gap-3 md:grid-cols-4 md:items-end">
                        <div>
                            <label for="busqueda" class="form-label">
                                Buscar
                            </label>

                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Nombre del periodo">
                        </div>

                        <div>
                            <label for="ciclo_id" class="form-label">
                                Ciclo
                            </label>

                            <select name="ciclo_id" id="ciclo_id" class="input-field">
                                <option value="">Todos</option>

                                @foreach($ciclos as $ciclo)
                                <option value="{{ $ciclo->id }}" @selected((string) $cicloId===(string) $ciclo->id)>
                                    {{ $ciclo->nombre }} {{ $ciclo->activo ? '(Activo)' : '' }}
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
                                <option value="activos" @selected($estado==='activos' )>Activos</option>
                                <option value="inactivos" @selected($estado==='inactivos' )>Inactivos</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('periodos.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Periodo</th>
                                    <th class="th-utec">Ciclo</th>
                                    <th class="th-utec">Fechas</th>
                                    <th class="th-utec">Límite consolidado</th>
                                    <th class="th-utec">Consolidados</th>
                                    <th class="th-utec">Casos</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($periodos as $periodo)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec font-semibold">
                                        {{ $periodo->nombre }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $periodo->ciclo?->nombre ?? 'Sin ciclo' }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $periodo->fecha_inicio->format('d/m/Y') }}
                                        -
                                        {{ $periodo->fecha_fin->format('d/m/Y') }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $periodo->fecha_limite_consolidado->format('d/m/Y') }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $periodo->consolidados_count }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $periodo->casos_seguimiento_count }}
                                    </td>

                                    <td class="td-utec">
                                        @if($periodo->activo)
                                        <span class="badge-success">Activo</span>
                                        @else
                                        <span class="badge-muted">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('periodos.edit', $periodo) }}" class="link-utec">
                                                Editar
                                            </a>

                                            @if($periodo->activo)
                                            <form method="POST" action="{{ route('periodos.destroy', $periodo) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-700 hover:text-red-900"
                                                    onclick="return confirm('¿Seguro que deseas desactivar este periodo?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay periodos de evaluación registrados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $periodos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>