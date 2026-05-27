<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Materias</h2>
                <p class="text-sm text-gray-400">Catálogo de materias usadas para carga académica y propuesta de asignación.</p>
            </div>
            <a href="{{ route('materias.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Nueva materia
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
                    <form method="GET" action="{{ route('materias.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:items-end">
                        <div class="sm:col-span-2">
                            <label for="busqueda" class="block text-sm font-medium text-utec-gray-dark mb-1.5">Buscar materia</label>
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
                                    placeholder="Código, nombre o departamento..."
                                    class="block w-full rounded-lg border border-utec-gray-medium bg-white py-2.5 pl-9 pr-3.5 text-sm
                                           focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="estado" class="block text-sm font-medium text-utec-gray-dark mb-1.5">Estado</label>
                            <select name="estado" id="estado"
                                class="block w-full rounded-lg border border-utec-gray-medium bg-white py-2.5 px-3.5 text-sm
                                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30">
                                <option value="">Todas</option>
                                <option value="activas" @selected($estado === 'activas')>Activas</option>
                                <option value="inactivas" @selected($estado === 'inactivas')>Inactivas</option>
                            </select>
                        </div>

                        <div class="flex gap-2 sm:items-end">
                            <button type="submit" class="btn-primary">Filtrar</button>
                            <a href="{{ route('materias.index') }}" class="btn-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
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
                                <tr class="transition-colors hover:bg-utec-primary-soft">
                                    <td class="px-6 py-4 text-sm font-semibold text-utec-gray-dark">
                                        {{ $materia->codigo }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <p class="text-sm font-medium text-utec-gray-dark">{{ $materia->nombre }}</p>
                                        <div class="mt-1.5 flex flex-wrap gap-1">
                                            @if($materia->gestionada_por_coordinacion)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[10px] font-semibold text-green-700">Gestionada</span>
                                            @else
                                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500">No gestionada</span>
                                            @endif

                                            @if($materia->esPrioritaria())
                                                <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-orange-700">Prioritaria</span>
                                            @endif

                                            @if($materia->requiere_revision)
                                                <span class="inline-flex items-center gap-1 rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-orange-700">Pendiente revisión</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        {{ $materia->creditos }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        @if($materia->ciclo_plan)
                                            <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2.5 py-0.5 text-xs font-medium text-utec-primary">
                                                Ciclo {{ $materia->ciclo_plan }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                        {{ $materia->departamento ?: '—' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        @if($materia->activo)
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                Activa
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>
                                                Inactiva
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-3">
                                            <a href="{{ route('materias.edit', $materia) }}" class="link-utec text-xs">
                                                Editar
                                            </a>

                                            @if($materia->activo)
                                                <form method="POST" action="{{ route('materias.destroy', $materia) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button
                                                        type="submit"
                                                        class="text-xs font-medium text-red-600 transition hover:text-red-800"
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
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center gap-2">
                                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                            </svg>
                                            <p class="text-sm font-medium text-gray-400">No hay materias registradas.</p>
                                            <a href="{{ route('materias.create') }}" class="link-utec text-xs">
                                                Crear la primera
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($materias->hasPages())
                        <div class="border-t border-utec-gray-medium px-6 py-4">
                            {{ $materias->links() }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>