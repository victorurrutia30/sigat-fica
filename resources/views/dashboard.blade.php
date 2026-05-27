<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <p class="mt-0.5 text-xl font-bold text-utec-gray-dark">
                    Coordinación · SIGAT-FICA
                </p>
                <h2 class="text-sm text-gray-400">
                    Panel de control
                </h2>
            </div>
            <div class="hidden items-center gap-2 sm:flex">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                <span class="text-xs text-gray-500">Sistema activo</span>
            </div>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-8 overflow-hidden rounded-2xl bg-utec-primary px-6 py-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-white/60">Bienvenida de vuelta</p>
                        <p class="mt-0.5 text-lg font-bold text-white">
                            {{ auth()->user()->nombre }}
                        </p>
                    </div>
                    <div class="hidden flex-col items-end sm:flex">
                        <p class="text-xs text-white/50">Ciclo académico activo</p>
                        <p class="mt-0.5 text-sm font-bold text-white">2026-01</p>
                    </div>
                </div>
            </div>

            <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <div class="card group">
                    <div class="card-body flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                Ciclo activo
                            </p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">2026-01</p>
                            <p class="mt-1 text-xs text-gray-400">Ciclo I · 2026</p>
                        </div>
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-utec-primary-soft text-utec-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card group">
                    <div class="card-body flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                Tutores activos
                            </p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">5</p>
                            <p class="mt-1 text-xs text-gray-400">Docentes DTC asignados</p>
                        </div>
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-utec-primary-soft text-utec-primary">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card group">
                    <div class="card-body flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                Propuesta
                            </p>
                            <p class="mt-2 text-2xl font-bold text-utec-primary">Activa</p>
                            <p class="mt-1 text-xs text-gray-400">Pendiente de aprobación</p>
                        </div>
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="card group">
                    <div class="card-body flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">
                                Consolidado
                            </p>
                            <p class="mt-2 text-2xl font-bold text-gray-300">Pendiente</p>
                            <p class="mt-1 text-xs text-gray-400">Disponible en Fase 2</p>
                        </div>
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <div class="card">
                <div class="card-header flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-utec-gray-dark">Módulos de coordinación</h3>
                        <p class="mt-0.5 text-xs text-gray-400">Accesos rápidos a las secciones principales</p>
                    </div>
                </div>
                <div class="card-body">
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                        <a href="{{ route('ciclos.index') }}"
                           class="group flex items-center gap-4 rounded-xl border border-utec-gray-medium bg-white p-4 transition-all hover:border-utec-primary hover:shadow-sm">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary transition group-hover:bg-utec-primary group-hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-utec-gray-dark group-hover:text-utec-primary">Ciclos</p>
                                <p class="mt-0.5 truncate text-xs text-gray-400">Gestión de ciclos académicos</p>
                            </div>
                        </a>

                        <a href="{{ route('tutores.index') }}"
                           class="group flex items-center gap-4 rounded-xl border border-utec-gray-medium bg-white p-4 transition-all hover:border-utec-primary hover:shadow-sm">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary transition group-hover:bg-utec-primary group-hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-utec-gray-dark group-hover:text-utec-primary">Tutores</p>
                                <p class="mt-0.5 truncate text-xs text-gray-400">Catálogo de tutores DTC</p>
                            </div>
                        </a>

                        <a href="{{ route('materias.index') }}"
                           class="group flex items-center gap-4 rounded-xl border border-utec-gray-medium bg-white p-4 transition-all hover:border-utec-primary hover:shadow-sm">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary transition group-hover:bg-utec-primary group-hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-utec-gray-dark group-hover:text-utec-primary">Materias</p>
                                <p class="mt-0.5 truncate text-xs text-gray-400">Catálogo de materias</p>
                            </div>
                        </a>

                        <a href="{{ route('carga-academica.create') }}"
                           class="group flex items-center gap-4 rounded-xl border border-utec-gray-medium bg-white p-4 transition-all hover:border-utec-primary hover:shadow-sm">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary transition group-hover:bg-utec-primary group-hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-utec-gray-dark group-hover:text-utec-primary">Carga académica</p>
                                <p class="mt-0.5 truncate text-xs text-gray-400">Importar desde Excel</p>
                            </div>
                        </a>

                        <a href="{{ route('propuestas.index') }}"
                           class="group flex items-center gap-4 rounded-xl border border-utec-gray-medium bg-white p-4 transition-all hover:border-utec-primary hover:shadow-sm">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary transition group-hover:bg-utec-primary group-hover:text-white">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-utec-gray-dark group-hover:text-utec-primary">Propuesta de asignación</p>
                                <p class="mt-0.5 truncate text-xs text-gray-400">Asignar tutores a secciones</p>
                            </div>
                        </a>

                        <div class="flex items-center gap-4 rounded-xl border border-dashed border-utec-gray-medium bg-gray-50 p-4 opacity-60">
                            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-400">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-400">Periodos de evaluación</p>
                                <span class="mt-0.5 inline-block rounded-full bg-gray-200 px-2 py-0.5 text-[10px] font-medium text-gray-500">
                                    Próximamente
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>