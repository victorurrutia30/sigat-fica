<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Detalle del ciclo</h2>
                <p class="text-sm text-gray-500">Información general del ciclo académico.</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('ciclos.edit', $ciclo) }}" class="btn-primary">
                    Editar
                </a>

                <a href="{{ route('ciclos.index') }}" class="btn-secondary">
                    Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <dl class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd class="mt-1 text-sm font-semibold text-utec-gray-dark">{{ $ciclo->nombre }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                @if($ciclo->activo)
                                <span class="badge-success">Activo</span>
                                @else
                                <span class="badge-muted">Inactivo</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Año</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $ciclo->anio }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Periodo</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                @switch($ciclo->periodo)
                                @case(1)
                                Ciclo 1
                                @break
                                @case(2)
                                Ciclo 2
                                @break
                                @case(3)
                                Ciclo 3
                                @break
                                @default
                                No definido
                                @endswitch
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de inicio</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $ciclo->fecha_inicio->format('d/m/Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de fin</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $ciclo->fecha_fin->format('d/m/Y') }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Secciones asociadas</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $ciclo->secciones_count }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Periodos de evaluación</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $ciclo->periodos_evaluacion_count }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Propuestas de asignación</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $ciclo->propuestas_asignacion_count }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>