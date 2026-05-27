<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Ciclos académicos</h2>
                <p class="text-sm text-gray-400">Gestión de ciclos activos e históricos.</p>
            </div>
            <a href="{{ route('ciclos.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nuevo ciclo
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

                {{-- Filtro --}}
                <div class="card-header">
                    <form method="GET" action="{{ route('ciclos.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label for="busqueda" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                                Buscar ciclo
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
                                    placeholder="Buscar por nombre o año..."
                                    class="block w-full rounded-lg border border-utec-gray-medium bg-white py-2.5 pl-9 pr-3.5 text-sm
                                           focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                                />
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">Filtrar</button>
                            <a href="{{ route('ciclos.index') }}" class="btn-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec rounded-none">Nombre</th>
                                    <th class="th-utec">Año</th>
                                    <th class="th-utec">Periodo</th>
                                    <th class="th-utec">Fechas</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($ciclos as $ciclo)
                                <tr class="transition-colors hover:bg-utec-primary-soft">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-utec-gray-dark">
                                            {{ $ciclo->nombre }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        {{ $ciclo->anio }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2.5 py-0.5 text-xs font-medium text-utec-primary">
                                            @switch($ciclo->periodo)
                                                @case(1) Ciclo I @break
                                                @case(2) Ciclo II @break
                                                @case(3) Ciclo III @break
                                                @default No definido
                                            @endswitch
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        <span class="tabular-nums">
                                            {{ $ciclo->fecha_inicio->format('d/m/Y') }}
                                            <span class="text-gray-400">–</span>
                                            {{ $ciclo->fecha_fin->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($ciclo->activo)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                Activo
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('ciclos.show', $ciclo) }}" class="link-utec text-xs">
                                                Ver
                                            </a>
                                            <a href="{{ route('ciclos.edit', $ciclo) }}" class="link-utec text-xs">
                                                Editar
                                            </a>
                                            @if($ciclo->activo)
                                                <form method="POST" action="{{ route('ciclos.destroy', $ciclo) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="text-xs font-medium text-red-600 transition hover:text-red-800"
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
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-400">No hay ciclos académicos registrados.</p>
                                            <a href="{{ route('ciclos.create') }}" class="link-utec text-xs">
                                                Crear el primero
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($ciclos->hasPages())
                        <div class="border-t border-utec-gray-medium px-6 py-4">
                            {{ $ciclos->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>