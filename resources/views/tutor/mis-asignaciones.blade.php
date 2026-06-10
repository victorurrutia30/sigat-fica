<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold text-utec-primary">
                Mis asignaciones
            </h2>
            <p class="text-sm text-gray-500">
                Secciones asignadas en la propuesta publicada del ciclo activo.
            </p>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error">
                {{ session('error') }}
            </div>
            @endif

            @if(! $tutor)
            <div class="alert-warning">
                Tu cuenta de usuario todavía no está vinculada a un tutor. Contacta a Coordinación para completar la vinculación.
            </div>
            @else
            <div class="grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Tutor</p>
                        <p class="mt-1 text-lg font-bold text-utec-primary">
                            {{ $tutor->nombre_completo }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $tutor->correo_institucional }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Departamento</p>
                        <p class="mt-1 text-lg font-bold text-utec-primary">
                            {{ $tutor->departamento ?? 'No definido' }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Asignaciones publicadas</p>
                        <p class="mt-1 text-lg font-bold text-utec-primary">
                            {{ $asignaciones->count() }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Secciones asignadas
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Solo se muestran asignaciones publicadas del ciclo activo.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    @if($asignaciones->isEmpty())
                    <div class="rounded-md bg-gray-50 px-4 py-8 text-center">
                        <p class="text-sm font-medium text-utec-gray-dark">
                            No tienes asignaciones publicadas en el ciclo activo.
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Las asignaciones aparecerán aquí cuando Coordinación publique una propuesta aprobada.
                        </p>
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Materia</th>
                                    <th class="th-utec">Sección</th>
                                    <th class="th-utec">Modalidad</th>
                                    <th class="th-utec">Horario</th>
                                    <th class="th-utec">Aula</th>
                                    <th class="th-utec">Docente titular</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @foreach($asignaciones as $asignacion)
                                @php
                                $seccion = $asignacion->seccion;
                                $materia = $seccion?->materia;
                                @endphp

                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec align-top">
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $materia?->nombre ?? 'Sin materia' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $materia?->codigo ?? 'Sin código' }}
                                        </div>


                                    </td>

                                    <td class="td-utec align-top">
                                        <span class="font-semibold">
                                            {{ $seccion?->numero_seccion ?? 'Sin sección' }}
                                        </span>
                                    </td>

                                    <td class="td-utec align-top">
                                        @if($seccion?->modalidad === 'presencial')
                                        <span class="badge-muted">Presencial</span>
                                        @elseif($seccion?->modalidad === 'en_linea')
                                        <span class="badge-info">En línea</span>
                                        @elseif($seccion?->modalidad === 'virtual')
                                        <span class="badge-muted">Virtual</span>
                                        @else
                                        <span class="badge-muted">{{ $seccion?->modalidad ?? 'No definida' }}</span>
                                        @endif
                                    </td>

                                    <td class="td-utec align-top">
                                        @if(! $seccion)
                                        <span class="text-sm text-gray-500">Sin sección</span>
                                        @elseif($seccion->modalidad === 'virtual')
                                        <span class="text-sm text-gray-500">Virtual asincrónica</span>
                                        @elseif($seccion->horarios->isEmpty())
                                        <span class="text-sm text-gray-500">Sin horario registrado</span>
                                        @else
                                        <div class="space-y-1">
                                            @foreach($seccion->horarios as $horario)
                                            <div class="text-sm">
                                                <span class="font-semibold">
                                                    @switch((int) $horario->dia_semana)
                                                    @case(1) Lunes @break
                                                    @case(2) Martes @break
                                                    @case(3) Miércoles @break
                                                    @case(4) Jueves @break
                                                    @case(5) Viernes @break
                                                    @case(6) Sábado @break
                                                    @case(7) Domingo @break
                                                    @default Día no definido
                                                    @endswitch
                                                </span>
                                                {{ \Illuminate\Support\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                                                -
                                                {{ \Illuminate\Support\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </td>

                                    <td class="td-utec align-top">
                                        @if($seccion?->modalidad === 'virtual')
                                        VIRTUAL
                                        @elseif($seccion?->modalidad === 'en_linea')
                                        {{ $seccion->aula ?: 'EN LÍNEA' }}
                                        @else
                                        {{ $seccion?->aula ?: 'No definida' }}
                                        @endif
                                    </td>

                                    <td class="td-utec align-top">
                                        <div class="font-semibold">
                                            {{ $seccion?->nombre_titular ?? 'No definido' }}
                                        </div>

                                        @if($seccion?->correo_titular)
                                        <div class="text-xs text-gray-500">
                                            {{ $seccion->correo_titular }}
                                        </div>
                                        @endif
                                    </td>


                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>