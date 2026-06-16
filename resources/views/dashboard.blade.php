<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-utec-gray-dark">
                Dashboard de Coordinación
            </h2>
            <p class="text-sm text-gray-500">
                Resumen general del sistema SIGAT-FICA.
            </p>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-10">

        @php
        $periodoActivoId = $periodoActivo?->id;

        $urlConsolidados = function (array $filtros = []) use ($periodoActivoId) {
        return route('consolidados.index', array_filter(
        array_merge(['periodo_id' => $periodoActivoId], $filtros),
        fn ($valor) => $valor !== null && $valor !== ''
        ));
        };
        @endphp

        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="alert-sigat">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->nombre }}</span>.
                Desde este panel puedes acceder a los módulos principales de Coordinación.
            </div>


            <div class="grid gap-6 md:grid-cols-2">
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Ciclo activo</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">
                            {{ $periodoActivo?->ciclo?->nombre ?? 'No definido' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Ciclo académico usado para propuestas, seguimiento y consolidados.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Periodo activo</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">
                            {{ $periodoActivo?->nombre ?? 'No definido' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Periodo usado para casos, gestiones y entrega de consolidados.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Estado de consolidados del periodo activo
                            </h3>
                            <p class="text-sm text-gray-500">
                                Usa estas tarjetas para ir directamente al listado formal filtrado.
                            </p>
                        </div>

                        <a href="{{ $urlConsolidados() }}" class="btn-secondary">
                            Ver todos
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($periodoActivo)
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                        <a href="{{ $urlConsolidados() }}"
                            class="rounded-lg border border-utec-gray-medium bg-white p-4 transition hover:border-utec-primary-light hover:bg-utec-primary-soft">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Total</p>
                            <p class="mt-2 text-3xl font-bold text-utec-primary">
                                {{ $metricasCumplimiento['total'] ?? 0 }}
                            </p>
                            <p class="mt-2 text-xs text-gray-500">Ver consolidados del periodo.</p>
                        </a>

                        <a href="{{ $urlConsolidados(['estado' => 'pendiente']) }}"
                            class="rounded-lg border border-utec-gray-medium bg-white p-4 transition hover:border-utec-primary-light hover:bg-utec-primary-soft">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Pendientes</p>
                            <p class="mt-2 text-3xl font-bold text-orange-700">
                                {{ $metricasCumplimiento['pendientes'] ?? 0 }}
                            </p>
                            <p class="mt-2 text-xs text-gray-500">Revisar entregas pendientes.</p>
                        </a>

                        <a href="{{ $urlConsolidados(['estado' => 'entregado']) }}"
                            class="rounded-lg border border-utec-gray-medium bg-white p-4 transition hover:border-utec-primary-light hover:bg-utec-primary-soft">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Entregados</p>
                            <p class="mt-2 text-3xl font-bold text-green-700">
                                {{ $metricasCumplimiento['entregados'] ?? 0 }}
                            </p>
                            <p class="mt-2 text-xs text-gray-500">Revisar entregas recibidas.</p>
                        </a>

                        <a href="{{ $urlConsolidados(['estado' => 'con_observaciones']) }}"
                            class="rounded-lg border border-utec-gray-medium bg-white p-4 transition hover:border-utec-primary-light hover:bg-utec-primary-soft">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">Observaciones</p>
                            <p class="mt-2 text-3xl font-bold text-blue-700">
                                {{ $metricasCumplimiento['con_observaciones'] ?? 0 }}
                            </p>
                            <p class="mt-2 text-xs text-gray-500">Seguimiento a devoluciones.</p>
                        </a>

                        <a href="{{ $urlConsolidados(['atraso' => 1]) }}"
                            class="rounded-lg border border-red-200 bg-red-50 p-4 transition hover:border-red-400 hover:bg-red-100">
                            <p class="text-xs font-medium uppercase tracking-wide text-red-700">Atrasados</p>
                            <p class="mt-2 text-3xl font-bold text-red-700">
                                {{ $metricasCumplimiento['atrasados'] ?? 0 }}
                            </p>
                            <p class="mt-2 text-xs text-red-700">Con fecha límite vencida.</p>
                        </a>
                    </div>
                    @else
                    <div class="alert-warning">
                        No hay periodo activo. Las métricas del dashboard estarán disponibles al activar un periodo de evaluación.
                    </div>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-utec-gray-dark">
                        Accesos de Coordinación
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Accesos organizados según el uso operativo del ciclo: monitoreo, consolidados, periodos y administración.
                    </p>

                    <div class="mt-6">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Operación y seguimiento
                        </h4>

                        <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">


                            <a href="{{ route('consolidados.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Consolidados
                                <span class="mt-1 block text-xs text-gray-500">
                                    Revisar entregas, observaciones y cumplimiento por tutor.
                                </span>
                            </a>

                            <a href="{{ route('periodos.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Periodos de evaluación
                                <span class="mt-1 block text-xs text-gray-500">
                                    Gestionar periodos, fechas límite y control de entregas.
                                </span>
                            </a>


                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                            Administración del ciclo
                        </h4>

                        <div class="mt-3 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <a href="{{ route('ciclos.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Ciclos académicos
                                <span class="mt-1 block text-xs text-gray-500">
                                    Crear y activar el ciclo académico correspondiente.
                                </span>
                            </a>

                            <a href="{{ route('carga-academica.create') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Carga académica
                                <span class="mt-1 block text-xs text-gray-500">
                                    Importar secciones, titulares y horarios desde Excel institucional.
                                </span>
                            </a>

                            <a href="{{ route('materias.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Materias
                                <span class="mt-1 block text-xs text-gray-500">
                                    Revisar materias gestionadas, prioridad y observaciones de importación.
                                </span>
                            </a>

                            <a href="{{ route('docentes-detectados.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Docentes detectados
                                <span class="mt-1 block text-xs text-gray-500">
                                    Revisar docentes titulares detectados desde la carga académica.
                                </span>
                            </a>

                            <a href="{{ route('tutores.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Tutores
                                <span class="mt-1 block text-xs text-gray-500">
                                    Gestionar tutores DTC activos, inactivos y habilitación para tutorías.
                                </span>
                            </a>

                            <a href="{{ route('propuestas.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Propuesta de asignación
                                <span class="mt-1 block text-xs text-gray-500">
                                    Generar asignación después de revisar carga académica, materias y tutores.
                                </span>
                            </a>

                            <a href="{{ route('causas.index') }}"
                                class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                                Catálogo de causas
                                <span class="mt-1 block text-xs text-gray-500">
                                    Mantener causas usadas en casos de seguimiento.
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</x-app-layout>