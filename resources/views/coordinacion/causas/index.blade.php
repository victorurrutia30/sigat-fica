<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Catálogo de causas
                </h2>
                <p class="text-sm text-gray-500">
                    Administra las causas usadas en los casos de seguimiento.
                </p>
            </div>

            <a href="{{ route('causas.create') }}" class="btn-primary">
                Nueva causa
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

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('causas.index') }}" class="mb-5 grid gap-3 md:grid-cols-3 md:items-end">
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
                                placeholder="Nombre o descripción">
                        </div>

                        <div>
                            <label for="estado" class="form-label">
                                Estado
                            </label>

                            <select name="estado" id="estado" class="input-field">
                                <option value="">Todos</option>
                                <option value="activas" @selected($estado==='activas' )>Activas</option>
                                <option value="inactivas" @selected($estado==='inactivas' )>Inactivas</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('causas.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Nombre</th>
                                    <th class="th-utec">Descripción</th>
                                    <th class="th-utec">Casos asociados</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($causas as $causa)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec font-semibold">
                                        {{ $causa->nombre }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $causa->descripcion ?: 'Sin descripción' }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $causa->casos_seguimiento_count }}
                                    </td>

                                    <td class="td-utec">
                                        @if($causa->activo)
                                        <span class="badge-success">Activa</span>
                                        @else
                                        <span class="badge-muted">Inactiva</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('causas.edit', $causa) }}" class="link-utec">
                                                Editar
                                            </a>

                                            @if($causa->activo)
                                            <form method="POST" action="{{ route('causas.destroy', $causa) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-700 hover:text-red-900"
                                                    onclick="return confirm('¿Seguro que deseas desactivar esta causa? No se eliminará el historial asociado.')">
                                                    Desactivar
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay causas registradas.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $causas->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>