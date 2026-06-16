@csrf

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

$horariosFormulario = old('horarios');

if ($horariosFormulario === null) {
$horariosFormulario = $seccion->horarios
->sortBy([
['dia_semana', 'asc'],
['hora_inicio', 'asc'],
])
->map(fn ($horario) => [
'dia_semana' => (string) $horario->dia_semana,
'hora_inicio' => substr((string) $horario->hora_inicio, 0, 5),
'hora_fin' => substr((string) $horario->hora_fin, 0, 5),
])
->values()
->all();
}

while (count($horariosFormulario) < 5) {
    $horariosFormulario[]=[ 'dia_semana'=> '',
    'hora_inicio' => '',
    'hora_fin' => '',
    ];
    }

    $itemPublicado = $seccion->itemsPropuesta
    ->first(fn ($item) => (bool) $item->propuestaAsignacion?->publicado);

    $itemPropuesta = $itemPublicado ?: $seccion->itemsPropuesta->first();
    @endphp

    <div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
        Edita la información de la sección sin perder trazabilidad. Los cambios peligrosos se bloquean si la sección ya tiene propuesta o casos registrados.
    </div>

    @if($seccion->items_propuesta_count > 0 || $seccion->casos_seguimiento_count > 0)
    <div class="mb-5 rounded-md border-l-4 border-yellow-500 bg-yellow-50 p-4 text-sm text-yellow-800">
        <p class="font-semibold">Sección con historial operativo.</p>
        <p class="mt-1">
            Esta sección tiene
            {{ $seccion->items_propuesta_count }} asignación(es) en propuesta y
            {{ $seccion->casos_seguimiento_count }} caso(s) registrado(s).
            Algunos cambios serán bloqueados para proteger la trazabilidad.
        </p>
    </div>
    @endif

    @if($itemPropuesta)
    <div class="mb-5 rounded-md border-l-4 border-blue-500 bg-blue-50 p-4 text-sm text-blue-800">
        <p class="font-semibold">
            Esta sección está relacionada con una propuesta.
        </p>
        <p class="mt-1">
            Estado:
            @if($itemPublicado)
            propuesta publicada.
            @else
            propuesta en edición.
            @endif
            Tutor asignado:
            <span class="font-semibold">
                {{ $itemPropuesta->tutor?->nombre_completo ?? 'No definido' }}
            </span>.
        </p>
    </div>
    @endif

    <div
        x-data="{
        modalidad: @js(old('modalidad', $seccion->modalidad)),
        requiereTutor: @js((bool) old('requiere_tutor', $seccion->requiere_tutor))
    }"
        class="space-y-6">
        <div class="card">
            <div class="card-header">
                Datos académicos
            </div>

            <div class="card-body">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label class="form-label">Ciclo académico</label>
                        <input
                            type="text"
                            value="{{ $seccion->ciclo?->nombre ?? 'Sin ciclo' }}"
                            class="input-field bg-gray-100"
                            disabled>
                        <p class="form-hint">
                            No se permite cambiar el ciclo de una sección ya importada.
                        </p>
                    </div>

                    <div>
                        <label class="form-label">Materia</label>
                        <input
                            type="text"
                            value="{{ $seccion->materia?->codigo }} — {{ $seccion->materia?->nombre }}"
                            class="input-field bg-gray-100"
                            disabled>
                        <p class="form-hint">
                            No se permite cambiar la materia asociada para proteger casos y propuestas.
                        </p>
                    </div>

                    <div>
                        <label for="numero_seccion" class="form-label">
                            Número de sección <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="numero_seccion"
                            id="numero_seccion"
                            value="{{ old('numero_seccion', $seccion->numero_seccion) }}"
                            class="input-field"
                            maxlength="10"
                            required>
                        @error('numero_seccion')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="modalidad" class="form-label">
                            Modalidad <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="modalidad"
                            id="modalidad"
                            class="input-field"
                            x-model="modalidad"
                            required>
                            <option value="presencial">Presencial</option>
                            <option value="en_linea">En línea</option>
                            <option value="virtual">Virtual</option>
                            <option value="mixta">Mixta</option>
                        </select>
                        @error('modalidad')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="aula" class="form-label">
                            Aula
                        </label>
                        <input
                            type="text"
                            name="aula"
                            id="aula"
                            value="{{ old('aula', $seccion->aula) }}"
                            class="input-field"
                            maxlength="60">
                        @error('aula')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-md border border-utec-gray-medium bg-gray-50 p-4">
                        @if(! $seccion->materia?->gestionada_por_coordinacion)
                        <input type="hidden" name="requiere_tutor" value="0">

                        <div class="flex items-start gap-2">
                            <input
                                type="checkbox"
                                class="mt-1 rounded border-utec-gray-medium text-utec-primary disabled:cursor-not-allowed disabled:opacity-60"
                                disabled>
                            <span>
                                <span class="block text-sm font-medium text-utec-gray-dark">
                                    No requiere tutor
                                </span>
                                <span class="block text-xs text-gray-500">
                                    Esta sección no puede requerir tutor porque la materia no está gestionada por Coordinación.
                                </span>
                            </span>
                        </div>
                        @else
                        <label class="flex items-start gap-2">
                            <input type="hidden" name="requiere_tutor" value="0">
                            <input
                                type="checkbox"
                                name="requiere_tutor"
                                value="1"
                                class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                                x-model="requiereTutor">
                            <span>
                                <span class="block text-sm font-medium text-utec-gray-dark">
                                    Requiere tutor
                                </span>
                                <span class="block text-xs text-gray-500">
                                    Coordinación decide si esta sección específica entra a la propuesta de tutorías. Las secciones virtuales pueden requerir tutor, pero no guardan horario.
                                </span>
                            </span>
                        </label>
                        @endif

                        @error('requiere_tutor')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                Docente titular
            </div>

            <div class="card-body">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div>
                        <label for="nombre_titular" class="form-label">
                            Nombre del docente titular <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="nombre_titular"
                            id="nombre_titular"
                            value="{{ old('nombre_titular', $seccion->nombre_titular) }}"
                            class="input-field"
                            maxlength="200"
                            required>
                        @error('nombre_titular')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="correo_titular" class="form-label">
                            Correo del docente titular
                        </label>
                        <input
                            type="email"
                            name="correo_titular"
                            id="correo_titular"
                            value="{{ old('correo_titular', $seccion->correo_titular) }}"
                            class="input-field"
                            maxlength="191">
                        @error('correo_titular')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="codigo_docente_titular" class="form-label">
                            Código del docente titular
                        </label>
                        <input
                            type="text"
                            name="codigo_docente_titular"
                            id="codigo_docente_titular"
                            value="{{ old('codigo_docente_titular', $seccion->codigo_docente_titular) }}"
                            class="input-field uppercase"
                            maxlength="30">
                        @error('codigo_docente_titular')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                        <p class="form-hint">
                            Si este código coincide con el tutor asignado, el sistema bloqueará el cambio.
                        </p>
                    </div>

                    <div>
                        <label for="categoria_docente_titular" class="form-label">
                            Categoría docente
                        </label>
                        <input
                            type="text"
                            name="categoria_docente_titular"
                            id="categoria_docente_titular"
                            value="{{ old('categoria_docente_titular', $seccion->categoria_docente_titular) }}"
                            class="input-field uppercase"
                            maxlength="30">
                        @error('categoria_docente_titular')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="observaciones_carga" class="form-label">
                            Observaciones de carga académica
                        </label>
                        <textarea
                            name="observaciones_carga"
                            id="observaciones_carga"
                            rows="3"
                            class="input-field"
                            maxlength="2000">{{ old('observaciones_carga', $seccion->observaciones_carga) }}</textarea>
                        @error('observaciones_carga')
                        <p class="form-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="card" x-show="modalidad !== 'virtual'">
            <div class="card-header">
                Horarios de la sección
            </div>

            <div class="card-body">
                <p class="mb-4 text-sm text-gray-600">
                    Si la sección ya tiene tutor asignado, el sistema validará que el nuevo horario no choque con otras tutorías ni con clases donde el tutor sea docente titular.
                </p>

                @error('horarios')
                <p class="form-error mb-3">{{ $message }}</p>
                @enderror

                <div class="space-y-3">
                    @foreach($horariosFormulario as $indice => $horario)
                    <div class="grid grid-cols-1 gap-3 rounded-md border border-utec-gray-medium bg-gray-50 p-3 md:grid-cols-3">
                        <div>
                            <label class="form-label">
                                Día
                            </label>
                            <select
                                name="horarios[{{ $indice }}][dia_semana]"
                                class="input-field"
                                :disabled="modalidad === 'virtual'">
                                <option value="">Sin día</option>
                                @foreach($dias as $valor => $nombre)
                                <option value="{{ $valor }}" @selected((string) ($horario['dia_semana'] ?? '' )===(string) $valor)>
                                    {{ $nombre }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="form-label">
                                Hora inicio
                            </label>
                            <input
                                type="time"
                                name="horarios[{{ $indice }}][hora_inicio]"
                                value="{{ $horario['hora_inicio'] ?? '' }}"
                                class="input-field"
                                :disabled="modalidad === 'virtual'">
                        </div>

                        <div>
                            <label class="form-label">
                                Hora fin
                            </label>
                            <input
                                type="time"
                                name="horarios[{{ $indice }}][hora_fin]"
                                value="{{ $horario['hora_fin'] ?? '' }}"
                                class="input-field"
                                :disabled="modalidad === 'virtual'">
                        </div>

                        @error("horarios.{$indice}")
                        <p class="form-error md:col-span-3">{{ $message }}</p>
                        @enderror

                        @error("horarios.{$indice}.hora_fin")
                        <p class="form-error md:col-span-3">{{ $message }}</p>
                        @enderror
                    </div>
                    @endforeach
                </div>

                <p class="form-hint mt-3">
                    Para quitar un horario, deja vacíos sus tres campos. Se guardarán únicamente las filas completas.
                </p>
            </div>
        </div>

        <div class="rounded-md border border-utec-gray-medium bg-white p-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="text-sm text-gray-600">
                    Los cambios se aplicarán solo a esta sección. La materia y el ciclo académico no se modifican.
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('materias.secciones.index', $seccion->materia) }}" class="btn-secondary">
                        Cancelar
                    </a>

                    <button type="submit" class="btn-primary">
                        Guardar sección
                    </button>
                </div>
            </div>
        </div>
    </div>