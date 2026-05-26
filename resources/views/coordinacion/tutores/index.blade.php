<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Tutores</h2>
                <p class="text-sm text-gray-500">Catálogo de Docentes de Tiempo Completo asignables como tutores.</p>
            </div>

            <a href="{{ route('tutores.create') }}" class="btn-primary">
                Nuevo tutor
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
                    <form method="GET" action="{{ route('tutores.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-4 md:items-end">
                        <div class="md:col-span-2">
                            <label for="busqueda" class="form-label">Buscar</label>
                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Código, nombre, correo o departamento">
                        </div>

                        <div>
                            <label for="estado" class="form-label">Estado</label>
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

                            <a href="{{ route('tutores.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Código</th>
                                    <th class="th-utec">Tutor</th>
                                    <th class="th-utec">Correo</th>
                                    <th class="th-utec">Departamento</th>
                                    <th class="th-utec">Cuenta</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($tutores as $tutor)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec font-semibold">
                                        {{ $tutor->codigo_empleado }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $tutor->nombre_completo }}
                                        <span class="ml-2 badge-success">DTC</span>
                                    </td>
                                    <td class="td-utec">
                                        {{ $tutor->correo_institucional }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $tutor->departamento ?: 'No definido' }}
                                    </td>
                                    <td class="td-utec">
                                        @if($tutor->usuario)
                                        <span class="badge-success">Vinculada</span>
                                        <p class="mt-1 text-xs text-gray-500">{{ $tutor->usuario->correo }}</p>
                                        @else
                                        <span class="badge-muted">Sin cuenta</span>
                                        @endif
                                    </td>
                                    <td class="td-utec">
                                        @if($tutor->trashed() || ! $tutor->activo)
                                        <span class="badge-muted">Inactivo</span>
                                        @else
                                        <span class="badge-success">Activo</span>
                                        @endif
                                    </td>
                                    <td class="td-utec">
                                        <div class="flex justify-end gap-2">
                                            @if(! $tutor->trashed())
                                            <a href="{{ route('tutores.show', $tutor) }}" class="link-utec">
                                                Ver
                                            </a>

                                            <a href="{{ route('tutores.edit', $tutor) }}" class="link-utec">
                                                Editar
                                            </a>

                                            <form method="POST" action="{{ route('tutores.destroy', $tutor) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-700 hover:text-red-900"
                                                    onclick="return confirm('¿Seguro que deseas desactivar este tutor?')">
                                                    Desactivar
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-sm text-gray-400">Sin acciones</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay tutores registrados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $tutores->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>