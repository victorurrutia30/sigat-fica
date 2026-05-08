<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-utec-gray-dark">
                Mis asignaciones
            </h2>
            <p class="text-sm text-gray-500">
                Consulta visual de las secciones asignadas al tutor.
            </p>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="alert-sigat mb-6">
                Bienvenido, <span class="font-semibold">{{ auth()->user()->nombre }}</span>.
                Aquí se mostrarán tus asignaciones cuando la propuesta esté publicada.
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-lg font-semibold text-utec-gray-dark">Asignaciones publicadas</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Esta vista no consulta datos todavía; queda preparada para el módulo de asignaciones.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead>
                            <tr>
                                <th class="th-utec">Materia</th>
                                <th class="th-utec">Sección</th>
                                <th class="th-utec">Modalidad</th>
                                <th class="th-utec">Horario</th>
                                <th class="th-utec">Docente titular</th>
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
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-base font-semibold text-utec-gray-dark">Casos de seguimiento</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Acceso visual preparado para registrar estudiantes no evaluados.
                        </p>

                        {{-- Ruta pendiente de implementar --}}
                        <a href="#" class="link-utec mt-4 inline-flex">
                            Ver casos
                        </a>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 class="text-base font-semibold text-utec-gray-dark">Consolidado</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Acceso visual preparado para la entrega del consolidado del periodo.
                        </p>

                        {{-- Ruta pendiente de implementar --}}
                        <a href="#" class="link-utec mt-4 inline-flex">
                            Ver consolidado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>