<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Materias</h2>
                <p class="text-sm text-gray-500">Catálogo de materias usadas para secciones y asignaciones.</p>
            </div>

            <a href="{{ route('materias.create') }}" class="btn-primary">
                Nueva materia
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
                    <form method="GET" action="{{ route('materias.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-4 md:items-end">
                        <div class="md:col-span-2">
                            <label for="busqueda" class="form-label">Buscar</label>
                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Código, nombre o departamento">
                        </div>

                        <div>
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="input-field">
                                <option value="">Todas</option>
                                <option value="activas" @selected($estado==='activas' )>Activas</option>
                                <option value="inactivas" @selected($estado==='inactivas' )>Inactivas</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('materias.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Código</th>
                                    <th class="th-utec">Materia</th>
                                    <th class="th-utec">Créditos</th>
                                    <th class="th-utec">Ciclo plan</th>
                                    <th class="th-utec">Departamento</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($materias as $materia)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec font-semibold">
                                        {{ $materia->codigo }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $materia->nombre }}
                                        @if($materia->ciclo_plan <= 2)
                                            <span class="ml-2 badge-warning">Prioritaria</span>
                                            @endif
                                    </td>
                                    <td class="td-utec">
                                        {{ $materia->creditos }}
                                    </td>
                                    <td class="td-utec">
                                        Ciclo {{ $materia->ciclo_plan }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $materia->departamento ?: 'No definido' }}
                                    </td>
                                    <td class="td-utec">
                                        @if($materia->activo)
                                        <span class="badge-success">Activa</span>
                                        @else
                                        <span class="badge-muted">Inactiva</span>
                                        @endif
                                    </td>
                                    <td class="td-utec">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('materias.edit', $materia) }}" class="link-utec">
                                                Editar
                                            </a>

                                            @if($materia->activo)
                                            <form method="POST" action="{{ route('materias.destroy', $materia) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-700 hover:text-red-900"
                                                    onclick="return confirm('¿Seguro que deseas desactivar esta materia?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay materias registradas.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $materias->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>