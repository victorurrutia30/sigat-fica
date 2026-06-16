@php
function nombreDiaDocenteDetectado(int $diaSemana): string {
return match ($diaSemana) {
1 => 'Lun',
2 => 'Mar',
3 => 'Mié',
4 => 'Jue',
5 => 'Vie',
6 => 'Sáb',
7 => 'Dom',
default => 'N/D',
};
}
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Docentes detectados
                </h2>
                <p class="text-sm text-gray-500">
                    Docentes titulares encontrados en la carga académica del ciclo activo.
                </p>
            </div>

            <a href="{{ route('tutores.index') }}" class="btn-secondary">
                Ver tutores
            </a>
        </div>
    </x-slot>

    <div class="py-6">
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

            @if($errors->any())
            <div class="alert-error">
                <p class="font-semibold">Revisa la acción solicitada.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Ciclo activo</p>
                        <p class="mt-1 text-lg font-bold text-utec-primary">{{ $ciclo->nombre }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $ciclo->periodo }} · {{ $ciclo->anio }}
                        </p>
                    </div>
                </div>

                <div class="card md:col-span-2">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Uso</p>
                        <p class="mt-1 text-sm text-gray-600">
                            Esta pantalla no crea docentes nuevos. Agrupa los docentes titulares importados en secciones.
                            Desde aquí Coordinación puede crear un tutor usando el mismo código docente de la carga académica.
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Filtros
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Busca por código, nombre, correo o categoría docente.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <form method="GET" action="{{ route('docentes-detectados.index') }}" class="grid gap-4 md:grid-cols-4">
                        <div class="md:col-span-2">
                            <label for="busqueda" class="form-label">Búsqueda</label>
                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Código, nombre, correo o categoría">
                        </div>

                        <div>
                            <label for="categoria" class="form-label">Categoría</label>
                            <select name="categoria" id="categoria" class="input-field">
                                <option value="">Todas</option>
                                @foreach($categorias as $categoriaDisponible)
                                <option value="{{ $categoriaDisponible }}" @selected($categoria===$categoriaDisponible)>
                                    {{ $categoriaDisponible }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="estado" class="form-label">Estado</label>
                            <select name="estado" id="estado" class="input-field">
                                <option value="">Todos</option>
                                <option value="registrados" @selected($estado==='registrados' )>
                                    Ya registrados como tutor
                                </option>
                                <option value="no_registrados" @selected($estado==='no_registrados' )>
                                    No registrados
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2 md:col-span-4">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('docentes-detectados.index') }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Docentes encontrados
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Se muestran docentes con código titular registrado en secciones del ciclo activo.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    @if($docentes->isEmpty())
                    <div class="rounded-md bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                        No hay docentes detectados con los filtros aplicados.
                    </div>
                    @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Código</th>
                                    <th class="th-utec">Docente</th>
                                    <th class="th-utec">Carga</th>
                                    <th class="th-utec">Tutor</th>
                                    <th class="th-utec">Acción</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @foreach($docentes as $docente)
                                @php
                                $tutor = $tutoresPorCodigo->get($docente->codigo_docente_titular);
                                @endphp

                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec align-top">
                                        <span class="font-semibold text-utec-gray-dark">
                                            {{ $docente->codigo_docente_titular }}
                                        </span>
                                    </td>

                                    <td class="td-utec align-top">
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $docente->nombre_titular ?: 'Nombre no disponible' }}
                                        </div>

                                        @if($docente->correo_titular)
                                        <div class="text-xs text-gray-500">
                                            {{ $docente->correo_titular }}
                                        </div>
                                        @endif

                                        <div class="mt-2">
                                            @if($docente->categoria_docente_titular === 'DTC')
                                            <span class="badge-success">DTC</span>
                                            @elseif($docente->categoria_docente_titular)
                                            <span class="badge-muted">{{ $docente->categoria_docente_titular }}</span>
                                            @else
                                            <span class="badge-muted">Sin categoría</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="td-utec align-top">
                                        @php
                                        $seccionesDocente = $seccionesPorDocente->get($docente->codigo_docente_titular, collect());
                                        @endphp

                                        <div class="text-sm text-utec-gray-dark">
                                            {{ $docente->total_secciones }} secciones
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $docente->total_materias }} materias
                                        </div>

                                        @if($seccionesDocente->isNotEmpty())
                                        <details class="mt-2 rounded-md border border-gray-200 bg-white px-3 py-2">
                                            <summary class="cursor-pointer text-xs font-semibold text-utec-primary">
                                                Ver secciones
                                            </summary>

                                            <div class="mt-3 space-y-3">
                                                @foreach($seccionesDocente as $seccionDocente)
                                                <div class="rounded-md bg-gray-50 px-3 py-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="text-sm font-semibold text-utec-gray-dark">
                                                            {{ $seccionDocente->materia?->codigo }}
                                                            —
                                                            {{ $seccionDocente->materia?->nombre }}
                                                        </span>

                                                        <span class="badge-muted">
                                                            Sec. {{ $seccionDocente->numero_seccion }}
                                                        </span>


                                                    </div>

                                                    <div class="mt-1 text-xs text-gray-500">
                                                        Modalidad: {{ str_replace('_', ' ', $seccionDocente->modalidad) }}
                                                        @if($seccionDocente->aula)
                                                        · Aula: {{ $seccionDocente->aula }}
                                                        @endif
                                                    </div>

                                                    <div class="mt-1 text-xs text-gray-600">
                                                        @if($seccionDocente->horarios->isEmpty())
                                                        Sin horario registrado
                                                        @else
                                                        @foreach($seccionDocente->horarios as $horario)
                                                        <span class="mr-2 inline-block">
                                                            {{ nombreDiaDocenteDetectado((int) $horario->dia_semana) }}
                                                            {{ \Illuminate\Support\Carbon::parse($horario->hora_inicio)->format('H:i') }}
                                                            -
                                                            {{ \Illuminate\Support\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                                        </span>
                                                        @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </details>
                                        @endif
                                    </td>

                                    <td class="td-utec align-top">
                                        @if($tutor)
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $tutor->nombre_completo }}
                                        </div>

                                        <div class="mt-1 flex flex-wrap gap-1">
                                            @if($tutor->trashed() || ! $tutor->activo)
                                            <span class="badge-danger">Inactivo</span>
                                            @else
                                            <span class="badge-success">Registrado</span>
                                            @endif

                                            @if($tutor->habilitado_para_tutorias)
                                            <span class="badge-info">Habilitado</span>
                                            @else
                                            <span class="badge-muted">No habilitado</span>
                                            @endif
                                        </div>
                                        @else
                                        <span class="badge-muted">No registrado</span>
                                        @endif
                                    </td>

                                    <td class="td-utec align-top">
                                        @if($tutor)
                                        @if(! $tutor->trashed())
                                        <a href="{{ route('tutores.edit', $tutor) }}" class="link-utec">
                                            Editar tutor
                                        </a>
                                        @else
                                        <form
                                            method="POST"
                                            action="{{ route('tutores.reactivar', $tutor->id) }}"
                                            onsubmit="return confirm('¿Deseas reactivar este tutor?');">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" class="link-utec">
                                                Reactivar tutor
                                            </button>
                                        </form>
                                        @endif
                                        @else
                                        <form
                                            method="POST"
                                            action="{{ route('docentes-detectados.crear-tutor', $docente->codigo_docente_titular) }}"
                                            onsubmit="return confirm('¿Crear tutor desde este docente detectado?');">
                                            @csrf

                                            <button type="submit" class="btn-primary">
                                                Crear tutor
                                            </button>
                                        </form>

                                        @if($docente->categoria_docente_titular !== 'DTC')
                                        <p class="mt-2 text-xs text-gray-500">
                                            Si no es DTC, se creará sin habilitación para tutorías.
                                        </p>
                                        @endif
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $docentes->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>