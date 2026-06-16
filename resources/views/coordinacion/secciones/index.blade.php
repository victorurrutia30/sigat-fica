<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Secciones de {{ $materia->nombre }}
                </h2>
                <p class="text-sm text-gray-500">
                    Consulta de secciones, docentes titulares y horarios asociados a la materia.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('materias.edit', $materia) }}" class="btn-secondary">
                    Editar materia
                </a>

                <a href="{{ route('materias.index') }}" class="btn-secondary">
                    Volver a materias
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <div class="grid gap-4 md:grid-cols-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Código
                            </p>
                            <p class="mt-1 font-semibold text-utec-gray-dark">
                                {{ $materia->codigo }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Materia
                            </p>
                            <p class="mt-1 font-semibold text-utec-gray-dark">
                                {{ $materia->nombre }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Gestión
                            </p>
                            <p class="mt-1">
                                @if($materia->gestionada_por_coordinacion)
                                <span class="badge-success">Gestionada por Coordinación</span>
                                @else
                                <span class="badge-muted">No gestionada</span>
                                @endif
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                                Plan de estudios
                            </p>
                            <p class="mt-1 font-semibold text-utec-gray-dark">
                                {{ $materia->ciclo_plan ? 'Ciclo ' . $materia->ciclo_plan : 'Pendiente' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('materias.secciones.index', $materia) }}" class="mb-5 grid gap-3 md:grid-cols-5 md:items-end">
                        <div>
                            <label for="ciclo_id" class="form-label">
                                Ciclo académico
                            </label>

                            <select name="ciclo_id" id="ciclo_id" class="input-field">
                                <option value="">Todos</option>

                                @foreach($ciclos as $ciclo)
                                <option value="{{ $ciclo->id }}" @selected((string) $cicloId===(string) $ciclo->id)>
                                    {{ $ciclo->nombre }} {{ $ciclo->activo ? '(Activo)' : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="modalidad" class="form-label">
                                Modalidad
                            </label>

                            <select name="modalidad" id="modalidad" class="input-field">
                                <option value="">Todas</option>
                                <option value="presencial" @selected($modalidad==='presencial' )>Presencial</option>
                                <option value="en_linea" @selected($modalidad==='en_linea' )>En línea</option>
                                <option value="virtual" @selected($modalidad==='virtual' )>Virtual</option>
                                <option value="mixta" @selected($modalidad==='mixta' )>Mixta</option>
                            </select>
                        </div>

                        <div>
                            <label for="requiere_tutor" class="form-label">
                                Requiere tutor
                            </label>

                            <select name="requiere_tutor" id="requiere_tutor" class="input-field">
                                <option value="">Todas</option>
                                <option value="si" @selected($requiereTutor==='si' )>Sí</option>
                                <option value="no" @selected($requiereTutor==='no' )>No</option>
                            </select>
                        </div>

                        <div>
                            <label for="busqueda" class="form-label">
                                Buscar
                            </label>

                            <input
                                type="text"
                                name="busqueda"
                                id="busqueda"
                                value="{{ $busqueda }}"
                                class="input-field"
                                placeholder="Sección, aula o docente">
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary">
                                Filtrar
                            </button>

                            <a href="{{ route('materias.secciones.index', $materia) }}" class="btn-secondary">
                                Limpiar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Sección</th>
                                    <th class="th-utec">Ciclo</th>
                                    <th class="th-utec">Modalidad</th>
                                    <th class="th-utec">Docente titular</th>
                                    <th class="th-utec">Horarios</th>
                                    <th class="th-utec">Propuesta</th>
                                    <th class="th-utec">Casos</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($secciones as $seccion)
                                @php
                                $dias = [
                                1 => 'Lun',
                                2 => 'Mar',
                                3 => 'Mié',
                                4 => 'Jue',
                                5 => 'Vie',
                                6 => 'Sáb',
                                7 => 'Dom',
                                ];

                                $horarios = $seccion->horarios
                                ->sortBy([
                                ['dia_semana', 'asc'],
                                ['hora_inicio', 'asc'],
                                ])
                                ->map(function ($horario) use ($dias) {
                                return ($dias[(int) $horario->dia_semana] ?? 'Día')
                                . ' '
                                . substr((string) $horario->hora_inicio, 0, 5)
                                . '-'
                                . substr((string) $horario->hora_fin, 0, 5);
                                })
                                ->implode(' · ');

                                $itemPublicado = $seccion->itemsPropuesta
                                ->first(fn ($item) => (bool) $item->propuestaAsignacion?->publicado);

                                $itemBorrador = $seccion->itemsPropuesta
                                ->first(fn ($item) => ! (bool) $item->propuestaAsignacion?->publicado);

                                $itemPropuesta = $itemPublicado ?: $itemBorrador;
                                @endphp

                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec">
                                        <div class="font-semibold text-utec-gray-dark">
                                            {{ $seccion->numero_seccion }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Aula: {{ $seccion->aula ?: 'No definida' }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div class="font-medium">
                                            {{ $seccion->ciclo?->nombre ?? 'Sin ciclo' }}
                                        </div>

                                        @if($seccion->ciclo?->activo)
                                        <span class="badge-success mt-1">Activo</span>
                                        @else
                                        <span class="badge-muted mt-1">Inactivo</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div>
                                            {{ ucfirst(str_replace('_', ' ', $seccion->modalidad)) }}
                                        </div>

                                        <div class="mt-1">
                                            @if($seccion->requiere_tutor)
                                            <span class="badge-warning">Requiere tutor</span>
                                            @else
                                            <span class="badge-muted">No requiere tutor</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div class="font-medium text-utec-gray-dark">
                                            {{ $seccion->nombre_titular }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            Código: {{ $seccion->codigo_docente_titular ?: 'No definido' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            {{ $seccion->correo_titular ?: 'Sin correo' }}
                                        </div>

                                        <div class="text-xs text-gray-500">
                                            Categoría: {{ $seccion->categoria_docente_titular ?: 'No definida' }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        @if($horarios)
                                        {{ $horarios }}
                                        @else
                                        <span class="text-sm text-gray-500">
                                            Sin horario registrado
                                        </span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        @if($itemPropuesta)
                                        @if($itemPublicado)
                                        <span class="badge-success">Publicada</span>
                                        @else
                                        <span class="badge-warning">En propuesta</span>
                                        @endif

                                        <div class="mt-1 text-xs text-gray-500">
                                            Tutor: {{ $itemPropuesta->tutor?->nombre_completo ?? 'No definido' }}
                                        </div>
                                        @else
                                        <span class="badge-muted">Sin propuesta</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div class="text-sm">
                                            Casos: {{ $seccion->casos_seguimiento_count }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            Nómina: {{ $seccion->nominas_seccion_count }}
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No hay secciones registradas para esta materia con los filtros seleccionados.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-5">
                        {{ $secciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>