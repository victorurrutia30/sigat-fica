<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Detalle del caso</h2>
                <p class="text-sm text-gray-400">Información del estudiante y gestiones realizadas.</p>
            </div>
            <div class="flex gap-2">
                @if(! $caso->cerrado)
                    <a href="{{ route('casos.edit', $caso) }}" class="btn-primary flex items-center gap-2">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                        </svg>
                        Editar
                    </a>
                @endif
                <a href="{{ route('casos.index') }}" class="btn-secondary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Banner --}}
            <div class="overflow-hidden rounded-2xl bg-utec-primary px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-white/50">Estudiante</p>
                        <p class="mt-0.5 text-2xl font-bold text-white">{{ $caso->estudiante?->nombre_completo }}</p>
                        <p class="text-xs text-white/60">{{ $caso->estudiante?->carne }}</p>
                    </div>
                    @if($caso->cerrado)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Cerrado
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/60">
                            <span class="h-1.5 w-1.5 rounded-full bg-orange-400"></span>
                            Abierto
                        </span>
                    @endif
                </div>
            </div>

            {{-- Información del caso --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-utec-gray-dark">Información del caso</p>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Sección</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">
                                {{ $caso->seccion?->materia?->codigo }} — {{ $caso->seccion?->materia?->nombre }}
                                <span class="font-normal text-gray-400">· Sec. {{ $caso->seccion?->numero_seccion }}</span>
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Correo</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">
                                {{ $caso->estudiante?->correo ?? '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Causa</dt>
                            <dd class="mt-1.5">
                                @if($caso->causa)
                                    <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2.5 py-0.5 text-xs font-medium text-utec-primary">
                                        {{ $caso->causa->nombre }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Resultado final</dt>
                            <dd class="mt-1.5">
                                @if($caso->resultado_final === 'retiro')
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-700">Retiro</span>
                                @elseif($caso->resultado_final === 'abandono')
                                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700">Abandono</span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Cerrado en</dt>
                            <dd class="mt-1.5 text-sm font-semibold tabular-nums text-utec-gray-dark">
                                {{ $caso->cerrado_en ? $caso->cerrado_en->format('d/m/Y H:i') : '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Registrado por</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">
                                {{ $caso->registradoPor?->nombre ?? '—' }}
                            </dd>
                        </div>

                    </dl>
                </div>
            </div>

            {{-- Gestiones --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-utec-gray-dark">Gestiones realizadas</p>
                        </div>
                        @if(! $caso->cerrado)
                            <a href="{{ route('gestiones.create', $caso) }}" class="btn-primary flex items-center gap-2">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                </svg>
                                Nueva gestión
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($caso->gestiones->isEmpty())
                        <div class="flex flex-col items-center gap-2 px-6 py-12 text-center">
                            <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155"/>
                            </svg>
                            <p class="text-sm font-medium text-gray-400">Aún no hay gestiones registradas.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-utec-gray-medium">
                                <thead>
                                    <tr>
                                        <th class="th-utec">Fecha</th>
                                        <th class="th-utec">Medio</th>
                                        <th class="th-utec">Acción realizada</th>
                                        <th class="th-utec">Resultado</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-utec-gray-medium bg-white">
                                    @foreach($caso->gestiones->sortByDesc('fecha_gestion') as $gestion)
                                        <tr class="transition-colors hover:bg-utec-primary-soft">
                                            <td class="px-6 py-4 text-sm tabular-nums text-utec-gray-dark">
                                                {{ \Carbon\Carbon::parse($gestion->fecha_gestion)->format('d/m/Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2.5 py-0.5 text-xs font-medium text-utec-primary">
                                                    {{ ucfirst($gestion->medio_contacto) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                                {{ $gestion->accion_realizada }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-utec-gray-dark">
                                                {{ $gestion->resultado ?? '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>