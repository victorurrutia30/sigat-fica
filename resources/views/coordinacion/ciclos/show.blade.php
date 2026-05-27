<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Detalle del ciclo</h2>
                <p class="text-sm text-gray-400">Información general del ciclo académico.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('ciclos.edit', $ciclo) }}" class="btn-primary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('ciclos.index') }}" class="btn-secondary flex items-center gap-2">
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

            <div class="overflow-hidden rounded-2xl bg-utec-primary px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-white/50">Ciclo académico</p>
                        <p class="mt-0.5 text-2xl font-bold text-white">{{ $ciclo->nombre }}</p>
                    </div>
                    @if($ciclo->activo)
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                            Activo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/50">
                            <span class="h-1.5 w-1.5 rounded-full bg-white/30"></span>
                            Inactivo
                        </span>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-utec-gray-dark">Información general</p>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Año</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">{{ $ciclo->anio }}</dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Periodo</dt>
                            <dd class="mt-1.5">
                                <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2.5 py-0.5 text-xs font-medium text-utec-primary">
                                    @switch($ciclo->periodo)
                                        @case(1) Ciclo I @break
                                        @case(2) Ciclo II @break
                                        @case(3) Ciclo III @break
                                        @default No definido
                                    @endswitch
                                </span>
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Fecha de inicio</dt>
                            <dd class="mt-1.5 text-sm font-semibold tabular-nums text-utec-gray-dark">
                                {{ $ciclo->fecha_inicio->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Fecha de fin</dt>
                            <dd class="mt-1.5 text-sm font-semibold tabular-nums text-utec-gray-dark">
                                {{ $ciclo->fecha_fin->format('d/m/Y') }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-utec-gray-dark">Estadísticas del ciclo</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                            <p class="text-2xl font-bold text-utec-primary">{{ $ciclo->secciones_count }}</p>
                            <p class="mt-1 text-xs text-gray-400">Secciones asociadas</p>
                        </div>
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                            <p class="text-2xl font-bold text-utec-primary">{{ $ciclo->periodos_evaluacion_count }}</p>
                            <p class="mt-1 text-xs text-gray-400">Periodos de evaluación</p>
                        </div>
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                            <p class="text-2xl font-bold text-utec-primary">{{ $ciclo->propuestas_asignacion_count }}</p>
                            <p class="mt-1 text-xs text-gray-400">Propuestas de asignación</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>