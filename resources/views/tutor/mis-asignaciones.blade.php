<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Mis asignaciones
            </h2>
            <p class="text-sm text-gray-500">
                Consulta visual de las secciones asignadas al tutor.
            </p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 rounded-lg border border-green-100 bg-green-50 p-4 text-sm text-green-800">
                Bienvenido, {{ auth()->user()->nombre }}. Aquí se mostrarán tus asignaciones cuando la propuesta esté publicada.
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Asignaciones publicadas</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Esta vista no consulta datos todavía; queda preparada para el módulo de asignaciones.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Materia</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Sección</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Modalidad</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Horario</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Docente titular</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    No hay asignaciones publicadas para mostrar en este momento.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-6 grid gap-4 md:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">Casos de seguimiento</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Acceso visual preparado para registrar estudiantes no evaluados.
                    </p>
                    <a href="#" class="mt-4 inline-flex text-sm font-medium text-blue-600 hover:text-blue-800">
                        Ver casos
                    </a>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900">Consolidado</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Acceso visual preparado para la entrega del consolidado del periodo.
                    </p>
                    <a href="#" class="mt-4 inline-flex text-sm font-medium text-blue-600 hover:text-blue-800">
                        Ver consolidado
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>