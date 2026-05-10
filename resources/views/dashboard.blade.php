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
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="alert-sigat mb-6">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->nombre }}</span>.
                Desde este panel puedes acceder a los módulos principales de Coordinación.
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Ciclo activo</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">2026-01</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Pendiente de cargar dinámicamente desde base de datos.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Tutores activos</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">5</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Datos demo disponibles para pruebas del Sprint 2.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Propuesta de asignación</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">Activa</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Módulo disponible para asignar tutores, registrar respuesta del Decano y publicar.
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm font-medium text-gray-500">Consolidado</p>
                        <p class="mt-3 text-2xl font-bold text-utec-primary">Pendiente</p>
                        <p class="mt-2 text-sm text-gray-500">
                            Seguimiento final del periodo. Pendiente para siguientes módulos.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card mt-8">
                <div class="card-body">
                    <h3 class="text-lg font-semibold text-utec-gray-dark">
                        Módulos de Coordinación
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Accesos principales para administrar ciclos, catálogos, carga académica y propuesta de asignación.
                    </p>

                    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <a href="{{ route('ciclos.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Ciclos
                        </a>

                        <a href="{{ route('tutores.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Tutores
                        </a>

                        <a href="{{ route('materias.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Materias
                        </a>

                        <a href="{{ route('carga-academica.create') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Carga académica
                        </a>

                        <a href="{{ route('propuestas.index') }}"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark transition hover:border-utec-primary-light hover:bg-utec-primary-soft hover:text-utec-primary">
                            Propuesta de asignación
                        </a>

                        <a href="#"
                            class="rounded-lg border border-utec-gray-medium p-4 text-sm font-medium text-utec-gray-dark opacity-60">
                            Periodos de evaluación
                            <span class="mt-1 block text-xs text-gray-500">
                                Pendiente
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>