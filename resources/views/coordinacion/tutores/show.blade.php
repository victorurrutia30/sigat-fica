<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Detalle del tutor</h2>
                <p class="text-sm text-gray-400">Información general del Docente de Tiempo Completo.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tutores.edit', $tutor) }}" class="btn-primary flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('tutores.index') }}" class="btn-secondary flex items-center gap-2">
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

            {{-- Banner superior --}}
            <div class="overflow-hidden rounded-2xl bg-utec-primary px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-white/50">Tutor DTC</p>
                        <p class="mt-0.5 text-2xl font-bold text-white">{{ $tutor->nombre_completo }}</p>
                        <p class="mt-0.5 text-sm text-white/60">{{ $tutor->codigo_empleado }}</p>
                    </div>
                    @if($tutor->activo)
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

            {{-- Información personal --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-utec-gray-dark">Información personal</p>
                    </div>
                </div>
                <div class="card-body">
                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Correo institucional</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">{{ $tutor->correo_institucional }}</dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Departamento</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">{{ $tutor->departamento ?: '—' }}</dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Fecha de contratación</dt>
                            <dd class="mt-1.5 text-sm font-semibold tabular-nums text-utec-gray-dark">
                                {{ $tutor->fecha_contratacion ? $tutor->fecha_contratacion->format('d/m/Y') : '—' }}
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Tipo de docente</dt>
                            <dd class="mt-1.5">
                                @if($tutor->tiempo_completo)
                                    <span class="inline-flex items-center rounded-full bg-utec-primary-soft px-2.5 py-0.5 text-xs font-medium text-utec-primary">
                                        Tiempo Completo
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                        No DTC
                                    </span>
                                @endif
                            </dd>
                        </div>

                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 sm:col-span-2">
                            <dt class="text-xs font-medium uppercase tracking-wide text-gray-400">Cuenta vinculada</dt>
                            <dd class="mt-1.5 text-sm font-semibold text-utec-gray-dark">
                                @if($tutor->usuario)
                                    {{ $tutor->usuario->nombre }}
                                    <span class="ml-1 font-normal text-gray-400">· {{ $tutor->usuario->correo }}</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-amber-600">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                                        </svg>
                                        Sin cuenta vinculada
                                    </span>
                                @endif
                            </dd>
                        </div>

                    </dl>
                </div>
            </div>

            {{-- Estadísticas --}}
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-utec-gray-dark">Actividad en el sistema</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                            <p class="text-2xl font-bold text-utec-primary">{{ $tutor->items_propuesta_count }}</p>
                            <p class="mt-1 text-xs text-gray-400">Asignaciones registradas</p>
                        </div>
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                            <p class="text-2xl font-bold text-utec-primary">{{ $tutor->casos_seguimiento_count }}</p>
                            <p class="mt-1 text-xs text-gray-400">Casos de seguimiento</p>
                        </div>
                        <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                            <p class="text-2xl font-bold text-utec-primary">{{ $tutor->consolidados_count }}</p>
                            <p class="mt-1 text-xs text-gray-400">Consolidados</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>