<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Tutores</h2>
                <p class="text-sm text-gray-500">Catálogo de personas habilitadas por Coordinación para asignaciones de tutoría.</p>
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
                                placeholder="Código, nombre, correo, departamento o categoría">
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
                                    <th class="th-utec">Área / categoría</th>
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
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $tutor->nombre_completo }}
                                        </div>

                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @if($tutor->tiempo_completo)
                                            <span class="badge-success">DTC</span>
                                            @elseif($tutor->es_excepcion_tutoria)
                                            <span class="badge-warning">Excepción</span>
                                            @else
                                            <span class="badge-muted">No DTC</span>
                                            @endif

                                            @if($tutor->habilitado_para_tutorias)
                                            <span class="badge-info">Habilitado</span>
                                            @else
                                            <span class="badge-muted">No habilitado</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="td-utec">
                                        {{ $tutor->correo_institucional }}
                                    </td>
                                    <td class="td-utec">
                                        <div>{{ $tutor->departamento ?: 'No definido' }}</div>

                                        @if($tutor->categoria_docente)
                                        <div class="mt-1 text-xs text-gray-500">
                                            CAT DOC: {{ $tutor->categoria_docente }}
                                        </div>
                                        @endif
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
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if(! $tutor->trashed())
                                            <a href="{{ route('tutores.show', $tutor) }}" class="link-utec">
                                                Ver
                                            </a>
                                            @endif

                                            @if(! $tutor->trashed() && $tutor->activo)
                                            <a href="{{ route('tutores.edit', $tutor) }}" class="link-utec">
                                                Editar
                                            </a>

                                            <form
                                                action="{{ route('tutores.destroy', $tutor) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Deseas desactivar este tutor? No se eliminará físicamente.');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="text-sm font-medium text-red-700 hover:text-red-900">
                                                    Desactivar
                                                </button>
                                            </form>
                                            @else
                                            <form
                                                action="{{ route('tutores.reactivar', $tutor->id) }}"
                                                method="POST"
                                                onsubmit="return confirm('¿Deseas reactivar este tutor?');">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="text-sm font-medium text-utec-primary hover:text-utec-primary-dark">
                                                    Reactivar
                                                </button>
                                            </form>
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