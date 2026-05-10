<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Ciclos académicos</h2>
                <p class="text-sm text-gray-500">Gestión de ciclos activos e históricos.</p>
            </div>

            <a href="{{ route('ciclos.create') }}" class="btn-primary">
                Nuevo ciclo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 rounded-md border-l-4 border-green-600 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 rounded-md border-l-4 border-red-600 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('ciclos.index') }}" class="mb-5 flex flex-col gap-3 md:flex-row md:items-end">
                        <div class="flex-1">
                            <label for="busqueda" class="form-label">Buscar</label>
                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Buscar por nombre o año">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('ciclos.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Nombre</th>
                                    <th class="th-utec">Año</th>
                                    <th class="th-utec">Periodo</th>
                                    <th class="th-utec">Fechas</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($ciclos as $ciclo)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec font-semibold">
                                        {{ $ciclo->nombre }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $ciclo->anio }}
                                    </td>
                                    <td class="td-utec">
                                        @switch($ciclo->periodo)
                                        @case(1)
                                        Ciclo 1
                                        @break
                                        @case(2)
                                        Ciclo 2
                                        @break
                                        @case(3)
                                        Ciclo 3
                                        @break
                                        @default
                                        No definido
                                        @endswitch
                                    </td>
                                    <td class="td-utec">
                                        {{ $ciclo->fecha_inicio->format('d/m/Y') }}
                                        -
                                        {{ $ciclo->fecha_fin->format('d/m/Y') }}
                                    </td>
                                    <td class="td-utec">
                                        @if($ciclo->activo)
                                        <span class="badge-success">Activo</span>
                                        @else
                                        <span class="badge-muted">Inactivo</span>
                                        @endif
                                    </td>
                                    <td class="td-utec">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('ciclos.show', $ciclo) }}" class="link-utec">
                                                Ver
                                            </a>

                                            <a href="{{ route('ciclos.edit', $ciclo) }}" class="link-utec">
                                                Editar
                                            </a>

                                            @if($ciclo->activo)
                                            <form method="POST" action="{{ route('ciclos.destroy', $ciclo) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-700 hover:text-red-900"
                                                    onclick="return confirm('¿Seguro que deseas desactivar este ciclo?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay ciclos académicos registrados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $ciclos->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>