<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard de Coordinación
            </h2>
            <p class="text-sm text-gray-500">
                Resumen general del sistema SIGAT-FICA.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-lg border border-blue-100 bg-blue-50 p-4 text-sm text-blue-800">
                Bienvenido, {{ auth()->user()->nombre }}. Este panel muestra información base para la Coordinación.
            </div>

            <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Ciclo activo</p>
                    <p class="mt-3 text-2xl font-bold text-gray-900">2026-01</p>
                    <p class="mt-2 text-sm text-gray-500">Pendiente de cargar dinámicamente.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Tutores activos</p>
                    <p class="mt-3 text-2xl font-bold text-gray-900">5</p>
                    <p class="mt-2 text-sm text-gray-500">Datos demo del Sprint 1.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Propuestas pendientes</p>
                    <p class="mt-3 text-2xl font-bold text-gray-900">Pendiente</p>
                    <p class="mt-2 text-sm text-gray-500">Módulo disponible en siguientes sprints.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Consolidado</p>
                    <p class="mt-3 text-2xl font-bold text-gray-900">Pendiente</p>
                    <p class="mt-2 text-sm text-gray-500">Seguimiento final del periodo.</p>
                </div>
            </div>

            <div class="mt-8 rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-900">Módulos de Coordinación</h3>
                <p class="mt-1 text-sm text-gray-500">
                    Accesos visuales preparados para los módulos administrativos. Las rutas se implementarán por bloque.
                </p>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <a href="#" class="rounded-lg border border-gray-200 p-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Ciclos
                    </a>

                    <a href="#" class="rounded-lg border border-gray-200 p-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Tutores
                    </a>

                    <a href="#" class="rounded-lg border border-gray-200 p-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Materias
                    </a>

                    <a href="#" class="rounded-lg border border-gray-200 p-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Secciones
                    </a>

                    <a href="#" class="rounded-lg border border-gray-200 p-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Propuestas
                    </a>

                    <a href="#" class="rounded-lg border border-gray-200 p-4 text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Periodos de evaluación
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>