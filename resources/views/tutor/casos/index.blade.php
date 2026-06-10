<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Casos de seguimiento
                </h2>
                <p class="text-sm text-gray-500">
                    Seguimiento de estudiantes no evaluados asignados a tus secciones.
                </p>
            </div>

            @if(! $mensajeBloqueo)
            <a href="{{ route('casos.create') }}" class="btn-primary">
                Nuevo caso
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="alert-warning mb-4">
                {{ session('warning') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            @if($mensajeBloqueo)
            <div class="alert-warning">
                {{ $mensajeBloqueo }}
            </div>
            @else
            <div class="mb-6 grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Periodo activo</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $periodo?->nombre }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $periodo?->fecha_inicio?->format('d/m/Y') }}
                            -
                            {{ $periodo?->fecha_fin?->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Tutor</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $tutor?->nombre_completo }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ $tutor?->correo_institucional }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Casos registrados</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $casos->count() }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Periodo activo actual.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Casos del periodo
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-utec-gray-medium">
                        <thead>
                            <tr>
                                <th class="th-utec">Estudiante</th>
                                <th class="th-utec">Sección</th>
                                <th class="th-utec">Causa</th>
                                <th class="th-utec">Resultado</th>
                                <th class="th-utec">Gestiones</th>
                                <th class="th-utec">Estado</th>
                                <th class="th-utec text-right">Acciones</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-utec-gray-medium bg-white">
                            @forelse($casos as $caso)
                            <tr class="hover:bg-utec-primary-soft">
                                <td class="td-utec">
                                    <div class="font-semibold">
                                        {{ $caso->estudiante?->nombre_completo }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Carné: {{ $caso->estudiante?->carne }}
                                    </div>
                                </td>

                                <td class="td-utec">
                                    <div class="font-semibold">
                                        {{ $caso->seccion?->materia?->codigo }}
                                        —
                                        {{ $caso->seccion?->numero_seccion }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $caso->seccion?->materia?->nombre }}
                                    </div>
                                </td>

                                <td class="td-utec">
                                    {{ $caso->causa?->nombre ?? 'Pendiente' }}
                                </td>

                                <td class="td-utec">
                                    @if($caso->resultado_final)
                                    {{ ucfirst($caso->resultado_final) }}
                                    @else
                                    Pendiente
                                    @endif
                                </td>

                                <td class="td-utec">
                                    {{ $caso->gestiones->count() }}
                                </td>

                                <td class="td-utec">
                                    @if($caso->cerrado)
                                    <span class="badge-success">Cerrado</span>
                                    @else
                                    <span class="badge-warning">Abierto</span>
                                    @endif
                                </td>

                                <td class="td-utec">
                                    <div class="flex justify-end">
                                        <a href="{{ route('casos.show', $caso) }}" class="link-utec">
                                            Ver detalle
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                    No tienes casos registrados en el periodo activo.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>