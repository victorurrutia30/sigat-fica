<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Propuesta de asignación
                </h2>
                <p class="text-sm text-gray-500">
                    Asigna tutores DTC a secciones gestionadas por Coordinación para el ciclo activo.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <button type="button"
                    class="btn-secondary cursor-not-allowed opacity-60"
                    disabled
                    title="Pendiente de implementar en el siguiente bloque">
                    Exportar Excel para Decano
                </button>
            </div>
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
                <p class="font-semibold">Revisa los datos de la propuesta.</p>
                <ul class="mt-2 list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid gap-4 md:grid-cols-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Ciclo activo</p>
                        <p class="mt-1 text-lg font-bold text-utec-primary">{{ $ciclo->nombre }}</p>
                        <p class="text-xs text-gray-500">
                            Ciclo {{ $ciclo->periodo }} · {{ $ciclo->anio }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Estado Decano</p>
                        <div class="mt-2">
                            @if($propuesta->estado_aprobacion === 'aprobado')
                            <span class="badge-success">Aprobado</span>
                            @elseif($propuesta->estado_aprobacion === 'requiere_ajustes')
                            <span class="badge-danger">Requiere ajustes</span>
                            @else
                            <span class="badge-warning">Pendiente</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Publicación</p>
                        <div class="mt-2">
                            @if($propuesta->publicado)
                            <span class="badge-success">Publicado</span>
                            @else
                            <span class="badge-muted">No publicado</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs font-semibold uppercase text-gray-500">Asignaciones</p>
                        <p class="mt-1 text-lg font-bold text-utec-primary">
                            {{ $propuesta->items->count() }} / {{ $seccionesCandidatas->count() }}
                        </p>
                        <p class="text-xs text-gray-500">
                            Secciones candidatas con tutor asignado.
                        </p>
                    </div>
                </div>
            </div>

            @if($propuesta->publicado)
            <div class="alert-warning">
                Esta propuesta ya fue publicada. Si modificas asignaciones, el sistema debe volverla a estado pendiente y dejará de estar visible para tutores hasta nueva aprobación.
            </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <div class="card-header">
                        <div>
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Secciones candidatas
                            </h3>
                            <p class="mt-1 text-sm text-gray-500">
                                Se muestran materias gestionadas por Coordinación y secciones que requieren tutor.
                            </p>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($seccionesCandidatas->isEmpty())
                        <div class="rounded-md bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">
                            No hay secciones candidatas para el ciclo activo.
                        </div>
                        @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-utec-gray-medium">
                                <thead>
                                    <tr>
                                        <th class="th-utec">Materia</th>
                                        <th class="th-utec">Sección</th>
                                        <th class="th-utec">Horario</th>
                                        <th class="th-utec">Docente titular</th>
                                        <th class="th-utec">Asignación</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-utec-gray-medium bg-white">
                                    @foreach($seccionesCandidatas as $seccion)
                                    @php
                                    $item = $itemsPorSeccion->get($seccion->id);
                                    $hayErrorFila = (string) old('seccion_id') === (string) $seccion->id;
                                    @endphp

                                    <tr class="hover:bg-utec-primary-soft">
                                        <td class="td-utec align-top">
                                            <div class="font-semibold text-utec-gray-dark">
                                                {{ $seccion->materia?->nombre }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $seccion->materia?->codigo }}
                                            </div>

                                            <div class="mt-2 flex flex-wrap gap-1">
                                                @if($seccion->materia?->ciclo_plan !== null && (int) $seccion->materia?->ciclo_plan <= 2)
                                                    <span class="badge-warning">Prioritaria</span>
                                                    @endif

                                                    @if($seccion->modalidad === 'presencial')
                                                    <span class="badge-muted">Presencial</span>
                                                    @elseif($seccion->modalidad === 'en_linea')
                                                    <span class="badge-info">En línea</span>
                                                    @elseif($seccion->modalidad === 'virtual')
                                                    <span class="badge-muted">Virtual</span>
                                                    @else
                                                    <span class="badge-muted">{{ $seccion->modalidad }}</span>
                                                    @endif
                                            </div>
                                        </td>

                                        <td class="td-utec align-top">
                                            <span class="font-semibold">
                                                {{ $seccion->numero_seccion }}
                                            </span>
                                        </td>

                                        <td class="td-utec align-top">
                                            @if($seccion->horarios->isEmpty())
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
                                            <div class="font-semibold">
                                                {{ $seccion->nombre_titular }}
                                            </div>

                                            @if($seccion->correo_titular)
                                            <div class="text-xs text-gray-500">
                                                {{ $seccion->correo_titular }}
                                            </div>
                                            @endif

                                            @if($seccion->categoria_docente_titular)
                                            <div class="mt-1 text-xs text-gray-500">
                                                CAT DOC: {{ $seccion->categoria_docente_titular }}
                                            </div>
                                            @endif
                                        </td>

                                        <td class="td-utec align-top">
                                            <form method="POST" action="{{ route('propuestas.items.store') }}" class="space-y-2">
                                                @csrf

                                                <input type="hidden" name="seccion_id" value="{{ $seccion->id }}">

                                                <div>
                                                    <label for="tutor_id_{{ $seccion->id }}" class="form-label">
                                                        Tutor <span class="text-red-500">*</span>
                                                    </label>

                                                    <select name="tutor_id"
                                                        id="tutor_id_{{ $seccion->id }}"
                                                        class="input-field"
                                                        required>
                                                        <option value="">Seleccione tutor</option>
                                                        @foreach($tutores as $tutor)
                                                        <option value="{{ $tutor->id }}"
                                                            @selected((string) old('tutor_id', $item?->tutor_id) === (string) $tutor->id)>
                                                            {{ $tutor->nombre_completo }}
                                                        </option>
                                                        @endforeach
                                                    </select>

                                                    @if($hayErrorFila)
                                                    @error('tutor_id')
                                                    <p class="form-error">{{ $message }}</p>
                                                    @enderror
                                                    @endif
                                                </div>

                                                <div>
                                                    <label for="observaciones_{{ $seccion->id }}" class="form-label">
                                                        Observaciones
                                                    </label>

                                                    <textarea name="observaciones"
                                                        id="observaciones_{{ $seccion->id }}"
                                                        rows="2"
                                                        class="input-field">{{ old('observaciones', $item?->observaciones) }}</textarea>

                                                    @if($hayErrorFila)
                                                    @error('observaciones')
                                                    <p class="form-error">{{ $message }}</p>
                                                    @enderror
                                                    @endif
                                                </div>

                                                <div class="flex flex-wrap items-center gap-2">
                                                    <button type="submit" class="btn-primary">
                                                        {{ $item ? 'Actualizar' : 'Asignar' }}
                                                    </button>

                                                    @if($item)
                                                    <span class="badge-success">Asignada</span>
                                                    @else
                                                    <span class="badge-muted">Sin asignar</span>
                                                    @endif
                                                </div>
                                            </form>

                                            @if($item)
                                            <form method="POST"
                                                action="{{ route('propuestas.items.destroy', $item) }}"
                                                class="mt-2"
                                                onsubmit="return confirm('¿Seguro que deseas quitar esta asignación?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn-danger">
                                                    Quitar tutor
                                                </button>
                                            </form>
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

                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Respuesta del Decano
                            </h3>
                        </div>

                        <div class="card-body">
                            <form method="POST"
                                action="{{ route('propuestas.respuesta-decano', $propuesta) }}"
                                class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label for="estado_aprobacion" class="form-label">
                                        Respuesta <span class="text-red-500">*</span>
                                    </label>

                                    <select name="estado_aprobacion"
                                        id="estado_aprobacion"
                                        class="input-field"
                                        required>
                                        <option value="">Seleccione respuesta</option>
                                        <option value="aprobado" @selected(old('estado_aprobacion', $propuesta->estado_aprobacion) === 'aprobado')>
                                            Aprobado
                                        </option>
                                        <option value="requiere_ajustes" @selected(old('estado_aprobacion', $propuesta->estado_aprobacion) === 'requiere_ajustes')>
                                            Requiere ajustes
                                        </option>
                                    </select>

                                    @error('estado_aprobacion')
                                    <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="fecha_respuesta_decano" class="form-label">
                                        Fecha de respuesta <span class="text-red-500">*</span>
                                    </label>

                                    <input type="date"
                                        name="fecha_respuesta_decano"
                                        id="fecha_respuesta_decano"
                                        value="{{ old('fecha_respuesta_decano', $propuesta->fecha_respuesta_decano ? \Illuminate\Support\Carbon::parse($propuesta->fecha_respuesta_decano)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                        class="input-field"
                                        required>

                                    @error('fecha_respuesta_decano')
                                    <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="observaciones_decano" class="form-label">
                                        Observaciones
                                    </label>

                                    <textarea name="observaciones_decano"
                                        id="observaciones_decano"
                                        rows="4"
                                        class="input-field">{{ old('observaciones_decano', $propuesta->observaciones_decano) }}</textarea>

                                    <p class="form-hint">
                                        Obligatorio si el Decano requiere ajustes.
                                    </p>

                                    @error('observaciones_decano')
                                    <p class="form-error">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="btn-primary">
                                    Registrar respuesta
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Publicación
                            </h3>
                        </div>

                        <div class="card-body space-y-4">
                            <p class="text-sm text-gray-600">
                                La propuesta solo puede publicarse si el Decano la aprobó.
                                Al publicar, los tutores podrán ver sus asignaciones.
                            </p>

                            <form method="POST"
                                action="{{ route('propuestas.publicar', $propuesta) }}"
                                onsubmit="return confirm('¿Seguro que deseas publicar la propuesta? Los tutores podrán ver sus asignaciones.')">
                                @csrf
                                @method('PATCH')

                                @php
                                $puedePublicar = $propuesta->estado_aprobacion === 'aprobado' && ! $propuesta->publicado;
                                @endphp

                                <button type="submit"
                                    class="btn-primary {{ ! $puedePublicar ? 'cursor-not-allowed opacity-60' : '' }}"
                                    @disabled(! $puedePublicar)>
                                    {{ $propuesta->publicado ? 'Propuesta publicada' : 'Publicar propuesta' }}
                                </button>
                            </form>

                            @if($propuesta->publicado)
                            <p class="form-hint">
                                La propuesta ya fue publicada. Si modificas asignaciones, volverá a estado pendiente y deberá aprobarse nuevamente.
                            </p>
                            @elseif($propuesta->estado_aprobacion !== 'aprobado')
                            <p class="form-hint">
                                Debes registrar aprobación del Decano antes de publicar.
                            </p>
                            @endif
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">
                                Historial reciente
                            </h3>
                        </div>

                        <div class="card-body">
                            @if($propuesta->historialCambios->isEmpty())
                            <p class="text-sm text-gray-500">
                                Aún no hay cambios registrados.
                            </p>
                            @else
                            <div class="space-y-3">
                                @foreach($propuesta->historialCambios->sortByDesc('created_at')->take(8) as $cambio)
                                <div class="border-b border-gray-200 pb-3 last:border-0 last:pb-0">
                                    <p class="text-sm font-semibold text-utec-gray-dark">
                                        {{ str_replace('_', ' ', ucfirst($cambio->tipo_cambio)) }}
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        {{ $cambio->descripcion }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500">
                                        {{ $cambio->created_at?->format('d/m/Y H:i') }}
                                        ·
                                        {{ $cambio->modificadoPor?->nombre ?? 'Usuario no disponible' }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>