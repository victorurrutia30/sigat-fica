<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Propuesta de asignación</h2>
                <p class="text-sm text-gray-400">Asigna tutores DTC a secciones gestionadas por Coordinación para el ciclo activo.</p>
            </div>
            <a href="{{ route('propuestas.exportar') }}"
                class="btn-secondary flex items-center gap-2 {{ $propuesta->items->isEmpty() ? 'pointer-events-none cursor-not-allowed opacity-60' : '' }}"
                @if($propuesta->items->isEmpty()) aria-disabled="true" @endif>
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M12 3v13.5m0 0l-4.5-4.5M12 16.5l4.5-4.5"/>
                </svg>
                Exportar Excel para Decano
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-green-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <p class="text-sm text-red-800">{{ session('error') }}</p>
                </div>
            @endif

            @if($errors->any())
                <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/>
                    </svg>
                    <div class="text-sm text-red-800">
                        <p class="font-semibold">Revisa los datos de la propuesta.</p>
                        <ul class="mt-1.5 list-disc space-y-1 pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if($propuesta->publicado)
                <div class="flex items-start gap-3 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3">
                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0 text-orange-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                    </svg>
                    <p class="text-sm text-orange-800">Esta propuesta ya fue publicada. Si modificas asignaciones, el sistema la volverá a estado pendiente y dejará de ser visible para los tutores hasta nueva aprobación.</p>
                </div>
            @endif

            {{-- Banner de resumen --}}
            <div class="overflow-hidden rounded-2xl bg-utec-primary px-6 py-5 shadow-sm">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div>
                        <p class="text-xs text-white/50">Ciclo activo</p>
                        <p class="mt-0.5 text-xl font-bold text-white">{{ $ciclo->nombre }}</p>
                        <p class="text-xs text-white/60">Ciclo {{ $ciclo->periodo }} · {{ $ciclo->anio }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-white/50">Estado Decano</p>
                        <div class="mt-1.5">
                            @if($propuesta->estado_aprobacion === 'aprobado')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Aprobado
                                </span>
                            @elseif($propuesta->estado_aprobacion === 'requiere_ajustes')
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-red-400"></span>Requiere ajustes
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white/40"></span>Pendiente
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-white/50">Publicación</p>
                        <div class="mt-1.5">
                            @if($propuesta->publicado)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-semibold text-white">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>Publicado
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/60">
                                    <span class="h-1.5 w-1.5 rounded-full bg-white/40"></span>No publicado
                                </span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-white/50">Asignaciones</p>
                        <p class="mt-0.5 text-xl font-bold text-white">
                            {{ $propuesta->items->count() }} / {{ $seccionesCandidatas->count() }}
                        </p>
                        <p class="text-xs text-white/60">Secciones con tutor asignado</p>
                    </div>
                </div>
            </div>

            {{-- Contenido principal --}}
            <div class="grid gap-6 lg:grid-cols-3">

                {{-- Tabla de secciones --}}
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-utec-gray-dark">Secciones candidatas</p>
                                    <p class="text-xs text-gray-400">Materias gestionadas por Coordinación que requieren tutor.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            @if($seccionesCandidatas->isEmpty())
                                <div class="flex flex-col items-center gap-2 px-6 py-12 text-center">
                                    <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z"/>
                                    </svg>
                                    <p class="text-sm font-medium text-gray-400">No hay secciones candidatas para el ciclo activo.</p>
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
                                                <tr class="transition-colors hover:bg-utec-primary-soft">
                                                    <td class="px-6 py-4 align-top">
                                                        <p class="text-sm font-semibold text-utec-gray-dark">{{ $seccion->materia?->nombre }}</p>
                                                        <p class="text-xs text-gray-400">{{ $seccion->materia?->codigo }}</p>
                                                        <div class="mt-1.5 flex flex-wrap gap-1">
                                                            @if($seccion->materia?->ciclo_plan !== null && (int) $seccion->materia?->ciclo_plan <= 2)
                                                                <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-semibold text-orange-700">Prioritaria</span>
                                                            @endif
                                                            @if($seccion->modalidad === 'presencial')
                                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500">Presencial</span>
                                                            @elseif($seccion->modalidad === 'en_linea')
                                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-[10px] font-semibold text-blue-700">En línea</span>
                                                            @else
                                                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-[10px] font-semibold text-gray-500">{{ $seccion->modalidad }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 align-top text-sm font-semibold text-utec-gray-dark">
                                                        {{ $seccion->numero_seccion }}
                                                    </td>
                                                    <td class="px-6 py-4 align-top">
                                                        @if($seccion->horarios->isEmpty())
                                                            <span class="text-xs text-gray-400">Sin horario</span>
                                                        @else
                                                            <div class="space-y-1">
                                                                @foreach($seccion->horarios as $horario)
                                                                    <div class="text-xs">
                                                                        <span class="font-semibold text-utec-gray-dark">
                                                                            @switch((int) $horario->dia_semana)
                                                                                @case(1) Lun @break
                                                                                @case(2) Mar @break
                                                                                @case(3) Mié @break
                                                                                @case(4) Jue @break
                                                                                @case(5) Vie @break
                                                                                @case(6) Sáb @break
                                                                                @case(7) Dom @break
                                                                            @endswitch
                                                                        </span>
                                                                        <span class="tabular-nums text-gray-500">
                                                                            {{ \Illuminate\Support\Carbon::parse($horario->hora_inicio)->format('H:i') }}–{{ \Illuminate\Support\Carbon::parse($horario->hora_fin)->format('H:i') }}
                                                                        </span>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 align-top">
                                                        <p class="text-sm font-medium text-utec-gray-dark">{{ $seccion->nombre_titular }}</p>
                                                        @if($seccion->correo_titular)
                                                            <p class="text-xs text-gray-400">{{ $seccion->correo_titular }}</p>
                                                        @endif
                                                        @if($seccion->categoria_docente_titular)
                                                            <p class="mt-0.5 text-xs text-gray-400">CAT: {{ $seccion->categoria_docente_titular }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 align-top">
                                                        <form method="POST" action="{{ route('propuestas.items.store') }}" class="space-y-2">
                                                            @csrf
                                                            <input type="hidden" name="seccion_id" value="{{ $seccion->id }}">
                                                            <div>
                                                                <label for="tutor_id_{{ $seccion->id }}" class="form-label">
                                                                    Tutor <span class="text-red-500">*</span>
                                                                </label>
                                                                <select name="tutor_id" id="tutor_id_{{ $seccion->id }}" class="input-field" required>
                                                                    <option value="">Seleccione tutor</option>
                                                                    @foreach($tutores as $tutor)
                                                                        <option value="{{ $tutor->id }}" @selected((string) old('tutor_id', $item?->tutor_id) === (string) $tutor->id)>
                                                                            {{ $tutor->nombre_completo }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                                @if($hayErrorFila)
                                                                    @error('tutor_id') <p class="form-error">{{ $message }}</p> @enderror
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <label for="aula_{{ $seccion->id }}" class="form-label">Aula</label>
                                                                <input type="text" name="aula" id="aula_{{ $seccion->id }}"
                                                                    value="{{ old('aula', $seccion->aula) }}"
                                                                    class="input-field" maxlength="60"
                                                                    placeholder="{{ $seccion->modalidad === 'en_linea' ? 'EN LÍNEA' : 'Ej. A-301' }}">
                                                                @if($hayErrorFila)
                                                                    @error('aula') <p class="form-error">{{ $message }}</p> @enderror
                                                                @endif
                                                            </div>
                                                            <div>
                                                                <label for="observaciones_{{ $seccion->id }}" class="form-label">Observaciones</label>
                                                                <textarea name="observaciones" id="observaciones_{{ $seccion->id }}" rows="2" class="input-field">{{ old('observaciones', $item?->observaciones) }}</textarea>
                                                                @if($hayErrorFila)
                                                                    @error('observaciones') <p class="form-error">{{ $message }}</p> @enderror
                                                                @endif
                                                            </div>
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                <button type="submit" class="btn-primary">
                                                                    {{ $item ? 'Actualizar' : 'Asignar' }}
                                                                </button>
                                                                @if($item)
                                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Asignada
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                                        <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>Sin asignar
                                                                    </span>
                                                                @endif
                                                            </div>
                                                        </form>
                                                        @if($item)
                                                            <form method="POST" action="{{ route('propuestas.items.destroy', $item) }}" class="mt-2"
                                                                onsubmit="return confirm('¿Seguro que deseas quitar esta asignación?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-xs font-medium text-red-600 transition hover:text-red-800">
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
                </div>

                {{-- Columna lateral --}}
                <div class="space-y-5">

                    {{-- Respuesta del Decano --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-utec-gray-dark">Respuesta del Decano</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('propuestas.respuesta-decano', $propuesta) }}" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label for="estado_aprobacion" class="form-label">Respuesta <span class="text-red-500">*</span></label>
                                    <select name="estado_aprobacion" id="estado_aprobacion" class="input-field" required>
                                        <option value="">Seleccione respuesta</option>
                                        <option value="aprobado" @selected(old('estado_aprobacion', $propuesta->estado_aprobacion) === 'aprobado')>Aprobado</option>
                                        <option value="requiere_ajustes" @selected(old('estado_aprobacion', $propuesta->estado_aprobacion) === 'requiere_ajustes')>Requiere ajustes</option>
                                    </select>
                                    @error('estado_aprobacion') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="fecha_respuesta_decano" class="form-label">Fecha de respuesta <span class="text-red-500">*</span></label>
                                    <input type="date" name="fecha_respuesta_decano" id="fecha_respuesta_decano"
                                        value="{{ old('fecha_respuesta_decano', $propuesta->fecha_respuesta_decano ? \Illuminate\Support\Carbon::parse($propuesta->fecha_respuesta_decano)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                        class="input-field" required>
                                    @error('fecha_respuesta_decano') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="observaciones_decano" class="form-label">Observaciones</label>
                                    <textarea name="observaciones_decano" id="observaciones_decano" rows="4" class="input-field">{{ old('observaciones_decano', $propuesta->observaciones_decano) }}</textarea>
                                    <p class="form-hint">Obligatorio si el Decano requiere ajustes.</p>
                                    @error('observaciones_decano') <p class="form-error">{{ $message }}</p> @enderror
                                </div>
                                <button type="submit" class="btn-primary flex items-center gap-2">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Registrar respuesta
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Publicación --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 7.5h1.5m-1.5 3h1.5m-7.5 3h7.5m-7.5 3h7.5m3-9h3.375c.621 0 1.125.504 1.125 1.125V18a2.25 2.25 0 01-2.25 2.25M16.5 7.5V18a2.25 2.25 0 002.25 2.25M16.5 7.5V4.875c0-.621-.504-1.125-1.125-1.125H4.125C3.504 3.75 3 4.254 3 4.875V18a2.25 2.25 0 002.25 2.25h13.5M6 7.5h3v3H6v-3z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-utec-gray-dark">Publicación</p>
                            </div>
                        </div>
                        <div class="card-body space-y-4">
                            <p class="text-sm text-gray-500">La propuesta solo puede publicarse si el Decano la aprobó. Al publicar, los tutores podrán ver sus asignaciones.</p>
                            @php $puedePublicar = $propuesta->estado_aprobacion === 'aprobado' && ! $propuesta->publicado; @endphp
                            <form method="POST" action="{{ route('propuestas.publicar', $propuesta) }}"
                                onsubmit="return confirm('¿Seguro que deseas publicar la propuesta? Los tutores podrán ver sus asignaciones.')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn-primary flex items-center gap-2 {{ ! $puedePublicar ? 'cursor-not-allowed opacity-60' : '' }}"
                                    @disabled(! $puedePublicar)>
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M12 3v13.5m0-13.5l-4.5 4.5M12 3l4.5 4.5"/>
                                    </svg>
                                    {{ $propuesta->publicado ? 'Propuesta publicada' : 'Publicar propuesta' }}
                                </button>
                            </form>
                            @if($propuesta->publicado)
                                <p class="form-hint">La propuesta ya fue publicada. Si modificas asignaciones, volverá a estado pendiente.</p>
                            @elseif($propuesta->estado_aprobacion !== 'aprobado')
                                <p class="form-hint">Debes registrar aprobación del Decano antes de publicar.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Historial reciente --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-utec-gray-dark">Historial reciente</p>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($propuesta->historialCambios->isEmpty())
                                <p class="text-sm text-gray-400">Aún no hay cambios registrados.</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($propuesta->historialCambios->sortByDesc('created_at')->take(8) as $cambio)
                                        <div class="border-b border-utec-gray-medium pb-3 last:border-0 last:pb-0">
                                            <p class="text-sm font-semibold text-utec-gray-dark">
                                                {{ str_replace('_', ' ', ucfirst($cambio->tipo_cambio)) }}
                                            </p>
                                            <p class="text-sm text-gray-500">{{ $cambio->descripcion }}</p>
                                            <p class="mt-1 text-xs text-gray-400">
                                                {{ $cambio->created_at?->format('d/m/Y H:i') }} · {{ $cambio->modificadoPor?->nombre ?? '—' }}
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