<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-semibold leading-tight text-utec-gray-dark">
                Detalle del caso
            </h2>
            <p class="text-sm text-gray-500">
                Información del estudiante, seguimiento, gestiones y cierre.
            </p>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-6">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-6">
                {{ session('error') }}
            </div>
            @endif

            <div class="mb-6 flex flex-wrap justify-between gap-3">
                <a href="{{ route('casos.index') }}" class="btn-secondary">
                    Volver a casos
                </a>

                <div class="flex flex-wrap gap-2">
                    @if($puedeModificarCaso ?? true)
                    @if(! $caso->cerrado)
                    <a href="{{ route('gestiones.create', $caso) }}" class="btn-secondary">
                        Agregar gestión
                    </a>

                    <a href="{{ route('casos.cierre', $caso) }}" class="btn-primary">
                        Cerrar caso
                    </a>
                    @else
                    <form
                        method="POST"
                        action="{{ route('casos.reabrir', $caso) }}"
                        onsubmit="return confirm('¿Seguro que deseas reabrir este caso para corrección? Deberás registrar una gestión correctiva y cerrarlo nuevamente.')">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="btn-secondary">
                            Reabrir para corrección
                        </button>
                    </form>
                    @endif
                    @else
                    <span class="badge-muted">
                        Solo lectura: consolidado entregado
                    </span>
                    @endif
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Información del estudiante
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Carné</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->estudiante?->carne }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->estudiante?->nombre_completo }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Correo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->estudiante?->correo ?: 'No registrado' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Carrera</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->estudiante?->carrera ?: 'No registrada' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Estado
                        </h3>
                    </div>

                    <div class="card-body">
                        @if($caso->cerrado)
                        <span class="badge-success">
                            Cerrado
                        </span>
                        <p class="mt-3 text-sm text-gray-500">
                            Cerrado el {{ $caso->cerrado_en?->format('d/m/Y H:i') }}.
                        </p>
                        @else
                        <span class="badge-warning">
                            Abierto
                        </span>
                        <p class="mt-3 text-sm text-gray-500">
                            Registra gestiones y completa el cierre antes de entregar el consolidado.
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-2">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Información académica
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Periodo</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->periodoEvaluacion?->nombre }}
                                    <span class="block text-xs text-gray-500">
                                        Ciclo {{ $caso->periodoEvaluacion?->ciclo?->nombre }}
                                    </span>
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tutor</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->tutor?->nombre_completo }}
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Materia</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->seccion?->materia?->codigo }} —
                                    {{ $caso->seccion?->materia?->nombre }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Sección</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->seccion?->numero_seccion }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Modalidad</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ ucfirst(str_replace('_', ' ', $caso->seccion?->modalidad ?? '')) }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Resultado del caso
                        </h3>
                    </div>

                    <div class="card-body">
                        <dl class="grid gap-4 md:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Causa</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->causa?->nombre ?: 'Pendiente' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Resultado interno</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->resultadoFinalTexto() }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Resultado institucional</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->resultadoConsolidadoTexto() }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Marca institucional</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->resultadoConsolidadoMarca() ?: 'Pendiente' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Matrícula</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    @if(is_null($caso->matricula))
                                    Pendiente
                                    @else
                                    {{ $caso->matricula ? 'Sí' : 'No' }}
                                    @endif
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm font-medium text-gray-500">Nº cuota cancelada</dt>
                                <dd class="mt-1 text-sm text-utec-gray-dark">
                                    {{ $caso->cuota_cancelada ?: 'No registrada' }}
                                </dd>
                            </div>

                            <div class="md:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Detalle de inasistencia</dt>
                                <dd class="mt-1 whitespace-pre-line text-sm text-utec-gray-dark">
                                    {{ $caso->detalle_inasistencia ?: 'Pendiente' }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="card mt-6">
                <div class="card-header">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Gestiones registradas
                        </h3>

                        @if(! $caso->cerrado)
                        <a href="{{ route('gestiones.create', $caso) }}" class="btn-secondary">
                            Agregar gestión
                        </a>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    @if($caso->gestiones->isEmpty())
                    <p class="text-sm text-gray-500">
                        No hay gestiones registradas para este caso.
                    </p>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Fecha</th>
                                    <th class="th-utec">Medio</th>
                                    <th class="th-utec">Acción realizada</th>
                                    <th class="th-utec">Resultado</th>
                                    <th class="th-utec">Registrado por</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @foreach($caso->gestiones as $gestion)
                                <tr>
                                    <td class="td-utec">
                                        {{ $gestion->fecha_gestion?->format('d/m/Y') }}
                                    </td>
                                    <td class="td-utec">
                                        {{ ucfirst(str_replace('_', ' ', $gestion->medio_contacto)) }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $gestion->accion_realizada }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $gestion->resultado ?: 'Sin resultado registrado' }}
                                    </td>
                                    <td class="td-utec">
                                        {{ $gestion->registradoPor?->nombre ?: 'No disponible' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>