<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Detalle del tutor</h2>
                <p class="text-sm text-gray-500">Información general del Docente de Tiempo Completo.</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('tutores.edit', $tutor) }}" class="btn-primary">
                    Editar
                </a>

                <a href="{{ route('tutores.index') }}" class="btn-secondary">
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
                            <dt class="text-sm font-medium text-gray-500">Código de empleado</dt>
                            <dd class="mt-1 text-sm font-semibold text-utec-gray-dark">{{ $tutor->codigo_empleado }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                @if($tutor->activo)
                                <span class="badge-success">Activo</span>
                                @else
                                <span class="badge-muted">Inactivo</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $tutor->nombre_completo }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Correo institucional</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $tutor->correo_institucional }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Departamento</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $tutor->departamento ?: 'No definido' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Fecha de contratación</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                {{ $tutor->fecha_contratacion ? $tutor->fecha_contratacion->format('d/m/Y') : 'No definida' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Tipo de docente</dt>
                            <dd class="mt-1">
                                @if($tutor->tiempo_completo)
                                <span class="badge-success">Docente de Tiempo Completo</span>
                                @else
                                <span class="badge-muted">No DTC</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Cuenta vinculada</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">
                                @if($tutor->usuario)
                                {{ $tutor->usuario->nombre }} — {{ $tutor->usuario->correo }}
                                @else
                                Sin cuenta vinculada
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Asignaciones registradas</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $tutor->items_propuesta_count }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Casos de seguimiento</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $tutor->casos_seguimiento_count }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Consolidados</dt>
                            <dd class="mt-1 text-sm text-utec-gray-dark">{{ $tutor->consolidados_count }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>