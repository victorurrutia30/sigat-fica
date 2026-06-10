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
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <div class="alert-sigat">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->nombre }}</span>.
                Desde este panel puedes acceder a los módulos principales de Coordinación.
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Resumen de cumplimiento
                            </h3>
                            <p class="text-sm text-gray-500">
                                Estado general de consolidados del periodo activo.
                            </p>
                        </div>

                        <a href="{{ route('tablero.index') }}" class="btn-secondary">
                            Ver tablero completo
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($periodoActivo)
                    <div class="mb-4 rounded-lg border border-utec-gray-medium bg-white px-4 py-3 text-sm text-gray-600">
                        Periodo activo:
                        <span class="font-semibold text-utec-primary">
                            {{ $periodoActivo->ciclo?->nombre ?? 'Sin ciclo' }} — {{ $periodoActivo->nombre }}
                        </span>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                        <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Total
                            </p>
                            <p class="mt-2 text-3xl font-bold text-utec-primary">
                                {{ $metricasCumplimiento['total'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Pendientes
                            </p>
                            <p class="mt-2 text-3xl font-bold text-orange-700">
                                {{ $metricasCumplimiento['pendientes'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                En progreso
                            </p>
                            <p class="mt-2 text-3xl font-bold text-blue-700">
                                {{ $metricasCumplimiento['en_progreso'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Entregados
                            </p>
                            <p class="mt-2 text-3xl font-bold text-green-700">
                                {{ $metricasCumplimiento['entregados'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Observaciones
                            </p>
                            <p class="mt-2 text-3xl font-bold text-orange-700">
                                {{ $metricasCumplimiento['con_observaciones'] }}
                            </p>
                        </div>

                        <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Atrasados
                            </p>
                            <p class="mt-2 text-3xl font-bold text-red-700">
                                {{ $metricasCumplimiento['atrasados'] }}
                            </p>
                        </div>
                    </div>
                    @else
                    <div class="alert-warning">
                        No hay periodo de evaluación activo. El resumen de cumplimiento estará disponible cuando se active un periodo.
                    </div>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Ciclo activo</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">
                            {{ $periodoActivo?->ciclo?->nombre ?? 'No definido' }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Ciclo asociado al periodo de evaluación activo.
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
                            Periodo usado para casos, gestiones y consolidados.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Consolidados</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">
                            {{ $metricasCumplimiento['total'] ?? 0 }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Total de consolidados generados para el periodo activo.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Alertas</p>
                        <p class="mt-3 text-2xl font-bold text-red-700">
                            {{ $metricasCumplimiento['atrasados'] ?? 0 }}
                        </p>
                        <p class="mt-2 text-sm text-gray-500">
                            Consolidados atrasados según fecha límite del periodo.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-utec-gray-dark">
                        Módulos de Coordinación
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Accesos principales para administrar ciclos, catálogos, carga académica, propuesta y seguimiento.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <a href="{{ route('tablero.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Tablero de cumplimiento
                            <span class="mt-1 block text-xs text-gray-500">
                                Ver avance, atrasos y estado general por tutor.
                            </span>
                        </a>

                        <a href="{{ route('propuestas.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Propuesta de asignación
                            <span class="mt-1 block text-xs text-gray-500">
                                Asignar tutores, registrar respuesta del Decano y publicar.
                            </span>
                        </a>

                        <a href="{{ route('consolidados.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Consolidados
                            <span class="mt-1 block text-xs text-gray-500">
                                Revisar entregas, observaciones y cumplimiento por tutor.
                            </span>
                        </a>

                        <a href="{{ route('ciclos.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Ciclos
                            <span class="mt-1 block text-xs text-gray-500">
                                Administrar ciclos académicos del sistema.
                            </span>
                        </a>

                        <a href="{{ route('tutores.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Tutores
                            <span class="mt-1 block text-xs text-gray-500">
                                Gestionar tutores DTC activos e inactivos.
                            </span>
                        </a>

                        <a href="{{ route('materias.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Materias
                            <span class="mt-1 block text-xs text-gray-500">
                                Gestionar materias, prioridad y revisión.
                            </span>
                        </a>

                        <a href="{{ route('carga-academica.create') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Carga académica
                            <span class="mt-1 block text-xs text-gray-500">
                                Importar secciones, titulares y horarios desde Excel.
                            </span>
                        </a>

                        <a href="{{ route('periodos.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Periodos de evaluación
                            <span class="mt-1 block text-xs text-gray-500">
                                Gestionar periodos y fechas límite de consolidado.
                            </span>
                        </a>

                        <a href="{{ route('causas.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Catálogo de causas
                            <span class="mt-1 block text-xs text-gray-500">
                                Gestionar causas para casos de seguimiento.
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>