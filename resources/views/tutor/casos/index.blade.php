<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Mis casos de seguimiento</h2>
                <p class="text-sm text-gray-400">Estudiantes no evaluados asignados a tu tutoría en el periodo activo.</p>
            </div>
            <a href="{{ route('casos.create') }}" class="btn-primary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
                Agregar estudiante
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            {{-- Banner resumen --}}
            <div class="overflow-hidden rounded-2xl bg-utec-primary px-6 py-5 shadow-sm">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <p class="text-xs text-white/50">Periodo activo</p>
                        <p class="mt-0.5 text-lg font-bold text-white">{{ $periodoActivo?->nombre ?? '—' }}</p>
                        <p class="text-xs text-white/60">{{ $periodoActivo?->fecha_limite_consolidado ? 'Límite: ' . \Carbon\Carbon::parse($periodoActivo->fecha_limite_consolidado)->format('d/m/Y') : '' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-white/50">Total casos</p>
                        <p class="mt-0.5 text-2xl font-bold text-white">{{ $totalCasos }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-white/50">Cerrados</p>
                        <p class="mt-0.5 text-2xl font-bold text-white">{{ $casosCerrados }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-white/50">Pendientes</p>
                        <p class="mt-0.5 text-2xl font-bold text-white">{{ $totalCasos - $casosCerrados }}</p>
                    </div>
                </div>
            </div>

            {{-- Tabla con filtros --}}
            <div class="card">
                <div class="card-header">
                    <form method="GET" action="{{ route('casos.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-4 sm:items-end">
                        <div class="sm:col-span-2">
                            <label for="busqueda" class="block text-sm font-medium text-utec-gray-dark mb-1.5">Buscar estudiante</label>
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
                                    value="{{ $busqueda ?? '' }}"
                                    placeholder="Carné o nombre del estudiante..."
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
                                <option value="">Todos</option>
                                <option value="abierto" @selected(($estado ?? '') === 'abierto')>Abiertos</option>
                                <option value="cerrado" @selected(($estado ?? '') === 'cerrado')>Cerrados</option>
                            </select>
                        </div>

                        <div>
                            <label for="seccion_id" class="block text-sm font-medium text-utec-gray-dark mb-1.5">Sección</label>
                            <select name="seccion_id" id="seccion_id"
                                class="block w-full rounded-lg border border-utec-gray-medium bg-white py-2.5 px-3.5 text-sm
                                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30">
                                <option value="">Todas</option>
                                @foreach($secciones as $seccion)
                                    <option value="{{ $seccion->id }}" @selected(($seccionId ?? '') == $seccion->id)>
                                        {{ $seccion->materia?->codigo }} — Sec. {{ $seccion->numero_seccion }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2 sm:col-span-4">
                            <button type="submit" class="btn-primary">Filtrar</button>
                            <a href="{{ route('casos.index') }}" class="btn-secondary">Limpiar</a>
                        </div>
                    </form>
                </div>

                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Estudiante</th>
                                    <th class="th-utec">Sección</th>
                                    <th class="th-utec">Causa</th>
                                    <th class="th-utec">Resultado</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec">Cerrado en</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($casos as $caso)
                                    <tr class="transition-colors hover:bg-utec-primary-soft">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-semibold text-utec-gray-dark">{{ $caso->estudiante?->nombre_completo }}</p>
                                            <p class="text-xs text-gray-400">{{ $caso->estudiante?->carne }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-medium text-utec-gray-dark">{{ $caso->seccion?->materia?->codigo }}</p>
                                            <p class="text-xs text-gray-400">Sec. {{ $caso->seccion?->numero_seccion }}</p>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                            {{ $caso->causa?->nombre ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($caso->resultado_final === 'retiro')
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">
                                                    Retiro
                                                </span>
                                            @elseif($caso->resultado_final === 'abandono')
                                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700">
                                                    Abandono
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($caso->cerrado)
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                                    Cerrado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>
                                                    Abierto
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm tabular-nums text-utec-gray-dark">
                                            {{ $caso->cerrado_en ? \Carbon\Carbon::parse($caso->cerrado_en)->format('d/m/Y') : '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('casos.show', $caso) }}" class="link-utec text-xs">
                                                    Ver
                                                </a>
                                                @if(! $caso->cerrado)
                                                    <a href="{{ route('casos.edit', $caso) }}" class="link-utec text-xs">
                                                        Editar
                                                    </a>
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
                                                <p class="text-sm font-medium text-gray-400">No hay casos registrados.</p>
                                                <a href="{{ route('casos.create') }}" class="link-utec text-xs">Agregar el primero</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($casos->hasPages())
                        <div class="border-t border-utec-gray-medium px-6 py-4">
                            {{ $casos->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>