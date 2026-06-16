<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Usuarios</h2>
                <p class="text-sm text-gray-500">
                    Administración de cuentas de acceso para Coordinación y Tutores.
                </p>
            </div>

            <a href="{{ route('usuarios.create') }}" class="btn-primary">
                Nuevo usuario
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

            @if(session('warning'))
            <div class="mb-4 rounded-md border-l-4 border-yellow-600 bg-yellow-50 p-4 text-sm text-yellow-800">
                {{ session('warning') }}
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('usuarios.index') }}" class="mb-5 grid grid-cols-1 gap-3 md:grid-cols-5 md:items-end">
                        <div class="md:col-span-2">
                            <label for="busqueda" class="form-label">Buscar</label>
                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Nombre o correo">
                        </div>

                        <div>
                            <label for="rol" class="form-label">Rol</label>
                            <select name="rol" id="rol" class="input-field">
                                <option value="">Todos</option>
                                <option value="coordinacion" @selected($rol==='coordinacion' )>Coordinación</option>
                                <option value="tutor" @selected($rol==='tutor' )>Tutor</option>
                            </select>
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
                            <button type="submit" class="btn-primary">Filtrar</button>
                            <a href="{{ route('usuarios.index') }}" class="btn-secondary">Limpiar</a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Usuario</th>
                                    <th class="th-utec">Correo</th>
                                    <th class="th-utec">Rol</th>
                                    <th class="th-utec">Tutor vinculado</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($usuarios as $usuario)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec font-semibold">
                                        {{ $usuario->nombre }}
                                    </td>

                                    <td class="td-utec">
                                        {{ $usuario->correo }}
                                    </td>

                                    <td class="td-utec">
                                        @if($usuario->rol === 'coordinacion')
                                        <span class="badge-success">Coordinación</span>
                                        @else
                                        <span class="badge-info">Tutor</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        @if($usuario->tutor)
                                        <span class="font-semibold">{{ $usuario->tutor->nombre_completo }}</span>
                                        <p class="mt-1 text-xs text-gray-500">{{ $usuario->tutor->codigo_empleado }}</p>
                                        @else
                                        <span class="badge-muted">Sin tutor vinculado</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        @if($usuario->activo)
                                        <span class="badge-success">Activo</span>
                                        @else
                                        <span class="badge-muted">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('usuarios.edit', $usuario) }}" class="link-utec">
                                                Editar
                                            </a>

                                            @if($usuario->activo && auth()->id() !== $usuario->id)
                                            <form method="POST" action="{{ route('usuarios.destroy', $usuario) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="text-sm font-medium text-red-700 hover:text-red-900"
                                                    onclick="return confirm('¿Seguro que deseas desactivar este usuario?')">
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
                                        No hay usuarios registrados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $usuarios->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>