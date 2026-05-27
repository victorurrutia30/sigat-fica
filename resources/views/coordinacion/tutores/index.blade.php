<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Tutores</h2>
                <p class="text-sm text-gray-400">Catálogo de Docentes de Tiempo Completo asignables como tutores.</p>
            </div>
            <a href="{{ route('tutores.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo tutor
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            <div class="card">

                <div class="card-header">
                    <form method="GET" action="{{ route('tutores.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:items-end">
                        <div class="sm:col-span-2">
                            <label for="busqueda" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                                Buscar tutor
                            </label>
                            <div class="relative">
                                <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-gray-400">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                                    </svg>
                                </span>
                                <input
                                    type="text"
                                    name="busqueda"
                                    id="busqueda"
                                    value="{{ $busqueda }}"
                                    placeholder="Código, nombre, correo o departamento..."
                                    class="block w-full rounded-lg border border-utec-gray-medium bg-white py-2.5 pl-9 pr-3.5 text-sm
                                           focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                                Estado
                            </label>
                            <select
                                name="estado"
                                id="estado"
                                class="block w-full rounded-lg border border-utec-gray-medium bg-white py-2.5 px-3.5 text-sm
                                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                            >
                                <option value="">Todos</option>
                                <option value="activos" @selected($estado === 'activos')>Activos</option>
                                <option value="inactivos" @selected($estado === 'inactivos')>Inactivos</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">Filtrar</button>
                            <a href="{{ route('tutores.index') }}" class="btn-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
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
                                <tr class="transition-colors hover:bg-utec-primary-soft">
                                    <td class="px-6 py-4 text-sm font-semibold text-utec-gray-dark">
                                        {{ $tutor->codigo_empleado }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-utec-gray-dark">{{ $tutor->nombre_completo }}</p>
                                        <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2 py-0.5 text-[10px] font-semibold text-utec-primary">
                                            DTC
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        {{ $tutor->correo_institucional }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        {{ $tutor->departamento ?: '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tutor->usuario)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                Vinculada
                                            </span>
                                            <p class="mt-1 text-xs text-gray-400">{{ $tutor->usuario->correo }}</p>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Sin cuenta
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($tutor->trashed() || ! $tutor->activo)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Inactivo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                Activo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3">
                                            @if(! $tutor->trashed())
                                                <a href="{{ route('tutores.show', $tutor) }}" class="link-utec text-xs">
                                                    Ver
                                                </a>
                                                <a href="{{ route('tutores.edit', $tutor) }}" class="link-utec text-xs">
                                                    Editar
                                                </a>
                                                <form method="POST" action="{{ route('tutores.destroy', $tutor) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="text-xs font-medium text-red-600 transition hover:text-red-800"
                                                        onclick="return confirm('¿Seguro que deseas desactivar este tutor?')">
                                                        Desactivar
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs text-gray-400">Sin acciones</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-400">No hay tutores registrados.</p>
                                            <a href="{{ route('tutores.create') }}" class="link-utec text-xs">
                                                Crear el primero
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($tutores->hasPages())
                        <div class="border-t border-utec-gray-medium px-6 py-4">
                            {{ $tutores->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>