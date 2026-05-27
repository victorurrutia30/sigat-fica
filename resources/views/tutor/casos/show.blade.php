<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Detalle del caso
                </h2>
                <p class="text-sm text-gray-500">
                    Información del estudiante, sección y gestiones registradas.
                </p>
            </div>

            <a href="{{ route('casos.index') }}" class="btn-secondary">
                Volver
            </a>
        </div>
    </x-slot>

    @php
    $dias = [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo',
    ];

    $horarioTexto = $caso->seccion?->horarios?->isNotEmpty()
    ? $caso->seccion->horarios
    ->map(fn ($horario) => ($dias[(int) $horario->dia_semana] ?? 'Día no definido') . ' ' . substr($horario->hora_inicio, 0, 5) . '-' . substr($horario->hora_fin, 0, 5))
    ->implode(', ')
    : 'Sin horario';
    @endphp

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-base font-semibold text-utec-gray-dark">
                                    Estado del caso
                                </h3>

                                @if($caso->cerrado)
                                <span class="badge-success">Cerrado</span>
                                @else
                                <span class="badge-warning">Abierto</span>
                                @endif
                            </div>
                        </div>

                        <div class="card-body">
                            <dl class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Causa</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->causa?->nombre ?? 'Pendiente' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Resultado final</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->resultado_final ? ucfirst($caso->resultado_final) : 'Pendiente' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de registro</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->created_at->format('d/m/Y H:i') }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Fecha de cierre</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->cerrado_en ? $caso->cerrado_en->format('d/m/Y H:i') : 'Pendiente' }}
                                    </dd>
                                </div>
                            </dl>

                            <div class="mt-5 rounded-md border border-utec-gray-medium bg-gray-50 p-4 text-sm text-gray-600">
                                La edición de causa, resultado final y cierre se implementará en el bloque de gestiones.
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Gestiones registradas
                            </h3>
                        </div>

                        <div class="card-body">
                            @if($caso->gestiones->isEmpty())
                            <p class="text-sm text-gray-500">
                                Este caso aún no tiene gestiones registradas.
                            </p>
                            @else
                            <div class="space-y-4">
                                @foreach($caso->gestiones as $gestion)
                                <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                                    <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                                        <p class="text-sm font-semibold text-utec-primary">
                                            {{ ucfirst(str_replace('_', ' ', $gestion->medio_contacto)) }}
                                        </p>

                                        <p class="text-xs text-gray-500">
                                            {{ $gestion->fecha_gestion->format('d/m/Y') }}
                                        </p>
                                    </div>

                                    <p class="mt-3 text-sm text-utec-gray-dark">
                                        {{ $gestion->accion_realizada }}
                                    </p>

                                    @if($gestion->resultado)
                                    <p class="mt-2 text-sm text-gray-600">
                                        <span class="font-semibold">Resultado:</span>
                                        {{ $gestion->resultado }}
                                    </p>
                                    @endif

                                    <p class="mt-2 text-xs text-gray-500">
                                        Registrado por:
                                        {{ $gestion->registradoPor?->nombre ?? 'No disponible' }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <div class="mt-5">
                                <button type="button" class="btn-secondary opacity-60" disabled>
                                    Agregar gestión — pendiente
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Estudiante
                            </h3>
                        </div>

                        <div class="card-body">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Carné</dt>
                                    <dd class="mt-1 text-sm font-semibold text-utec-gray-dark">
                                        {{ $caso->estudiante?->carne }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->estudiante?->nombre_completo }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Correo</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->estudiante?->correo ?? 'No registrado' }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Carrera</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->estudiante?->carrera ?? 'No registrada' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Sección
                            </h3>
                        </div>

                        <div class="card-body">
                            <dl class="space-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Materia</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->seccion?->materia?->codigo }}
                                        —
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

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Horario</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $horarioTexto }}
                                    </dd>
                                </div>

                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Docente titular</dt>
                                    <dd class="mt-1 text-sm text-utec-gray-dark">
                                        {{ $caso->seccion?->nombre_titular }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Periodo
                            </h3>
                        </div>

                        <div class="card-body">
                            <p class="text-sm font-semibold text-utec-primary">
                                {{ $caso->periodoEvaluacion?->nombre }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Ciclo {{ $caso->periodoEvaluacion?->ciclo?->nombre }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Límite consolidado:
                                {{ $caso->periodoEvaluacion?->fecha_limite_consolidado?->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>