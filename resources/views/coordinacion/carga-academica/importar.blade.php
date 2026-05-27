<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Importar carga académica</h2>
                <p class="text-sm text-gray-400">Carga secciones, horarios y docentes titulares desde el Excel institucional.</p>
            </div>
            <a href="{{ route('materias.index', ['revision' => 'pendientes']) }}" class="btn-secondary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                </svg>
                Ver materias pendientes
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

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

            @if(session('resumen_importacion'))
                @php($resumen = session('resumen_importacion'))
                @php($contadores = $resumen['contadores'] ?? [])
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-utec-gray-dark">Resumen de importación</p>
                                    <p class="text-xs text-gray-400">{{ str_replace('_', ' ', $resumen['estado'] ?? 'sin estado') }}</p>
                                </div>
                            </div>
                            @if(session('importacion_id'))
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                    Registro #{{ session('importacion_id') }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body space-y-4">
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Hojas procesadas</p>
                                <p class="mt-1.5 text-2xl font-bold text-utec-primary">{{ $contadores['hojas_procesadas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Filas leídas</p>
                                <p class="mt-1.5 text-2xl font-bold text-utec-primary">{{ $contadores['filas_leidas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Importadas</p>
                                <p class="mt-1.5 text-2xl font-bold text-green-700">{{ $contadores['filas_importadas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Ignoradas</p>
                                <p class="mt-1.5 text-2xl font-bold text-orange-600">{{ $contadores['filas_ignoradas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Con error</p>
                                <p class="mt-1.5 text-2xl font-bold text-red-600">{{ $contadores['filas_error'] ?? 0 }}</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5">
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Materias creadas</p>
                                <p class="mt-1.5 text-xl font-bold text-utec-gray-dark">{{ $contadores['materias_creadas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Materias actualizadas</p>
                                <p class="mt-1.5 text-xl font-bold text-utec-gray-dark">{{ $contadores['materias_actualizadas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Secciones creadas</p>
                                <p class="mt-1.5 text-xl font-bold text-utec-gray-dark">{{ $contadores['secciones_creadas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Secciones actualizadas</p>
                                <p class="mt-1.5 text-xl font-bold text-utec-gray-dark">{{ $contadores['secciones_actualizadas'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4 text-center">
                                <p class="text-xs text-gray-400">Horarios creados</p>
                                <p class="mt-1.5 text-xl font-bold text-utec-gray-dark">{{ $contadores['horarios_creados'] ?? 0 }}</p>
                            </div>
                        </div>
                        @if(! empty($resumen['hojas']))
                            <div class="flex items-start gap-2 rounded-xl border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
                                <span class="font-semibold">Hojas procesadas:</span>
                                <span>{{ implode(', ', $resumen['hojas']) }}</span>
                            </div>
                        @endif
                        @if(! empty($resumen['advertencias']))
                            <div class="rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                                <p class="font-semibold">Advertencias</p>
                                <ul class="mt-2 list-disc space-y-1 pl-5">
                                    @foreach($resumen['advertencias'] as $advertencia)
                                        <li>Hoja {{ $advertencia['hoja'] ?? '-' }}, fila {{ $advertencia['fila'] ?? '-' }}: {{ $advertencia['mensaje'] ?? 'Sin detalle' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(session('errores_importacion') && count(session('errores_importacion')) > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="flex items-center gap-3">
                            <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-utec-gray-dark">Errores detectados</p>
                                <p class="text-xs text-gray-400">Estas filas no se importaron. Corrige el archivo o revisa el catálogo.</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-utec-gray-medium">
                                <thead>
                                    <tr>
                                        <th class="th-utec">Hoja</th>
                                        <th class="th-utec">Fila</th>
                                        <th class="th-utec">Mensaje</th>
                                        <th class="th-utec">Datos</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-utec-gray-medium bg-white">
                                    @foreach(session('errores_importacion') as $error)
                                        <tr class="transition-colors hover:bg-utec-primary-soft">
                                            <td class="px-6 py-4 text-sm font-semibold text-utec-gray-dark">{{ $error['hoja'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-utec-gray-dark">{{ $error['fila'] ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm text-utec-gray-dark">{{ $error['mensaje'] ?? 'Sin detalle' }}</td>
                                            <td class="px-6 py-4">
                                                @if(! empty($error['datos']))
                                                    <pre class="max-w-md overflow-x-auto rounded-lg bg-gray-50 p-2 text-xs text-gray-700">{{ json_encode($error['datos'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-utec-gray-dark">Subir archivo</p>
                                    <p class="text-xs text-gray-400">Selecciona el ciclo al que pertenece la carga académica.</p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('carga-academica.store') }}" enctype="multipart/form-data">
                                @csrf
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <label for="ciclo_id" class="form-label">
                                            Ciclo académico <span class="text-red-500">*</span>
                                        </label>
                                        <select name="ciclo_id" id="ciclo_id" class="input-field" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($ciclos as $ciclo)
                                                <option value="{{ $ciclo->id }}" @selected((string) old('ciclo_id', $ciclo->activo ? $ciclo->id : '') === (string) $ciclo->id)>
                                                    {{ $ciclo->nombre }}{{ $ciclo->activo ? ' (Activo)' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('ciclo_id')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="archivo" class="form-label">
                                            Archivo Excel <span class="text-red-500">*</span>
                                        </label>
                                        <input type="file" name="archivo" id="archivo" class="input-field" accept=".xlsx,.xls" required>
                                        @error('archivo')
                                            <p class="form-error">{{ $message }}</p>
                                        @enderror
                                        <p class="form-hint">Formatos: .xlsx y .xls. Tamaño máximo: 10 MB.</p>
                                    </div>
                                </div>
                                <div class="mt-5 flex items-start gap-3 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-800">
                                    <svg class="mt-0.5 h-4 w-4 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126z"/>
                                    </svg>
                                    <div>
                                        <p class="font-semibold">Antes de importar</p>
                                        <ul class="mt-1.5 list-disc space-y-1 pl-4">
                                            <li>Verifica que elegiste el ciclo correcto.</li>
                                            <li>Las secciones existentes se actualizarán si vuelven a venir en el archivo.</li>
                                            <li>Los horarios de las secciones importadas se reemplazarán por los del Excel.</li>
                                            <li>Las secciones que ya existían pero no aparecen en el nuevo Excel no se eliminarán.</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="mt-6 flex justify-end">
                                    <button type="submit" class="btn-primary flex items-center gap-2"
                                        onclick="return confirm('¿Seguro que deseas importar esta carga académica? Esta acción actualizará secciones y horarios del ciclo seleccionado.')">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                        </svg>
                                        Importar carga académica
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="space-y-5">
                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-utec-gray-dark">Reglas de importación</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-disc space-y-2 pl-5 text-sm text-utec-gray-dark">
                                <li>Se procesan las hojas <span class="font-semibold">VIRTUALES Y SETACO</span>, <span class="font-semibold">INFO</span> y <span class="font-semibold">CCAA</span>.</li>
                                <li>Las filas con tipo <span class="font-semibold">Par</span> se ignoran con advertencia.</li>
                                <li><span class="font-semibold">P</span> = presencial, <span class="font-semibold">EL / E.L.</span> = en línea, <span class="font-semibold">V</span> = virtual.</li>
                                <li>Si una materia no existe, se crea como pendiente de revisión.</li>
                                <li>El orden de columnas puede cambiar, pero no deben eliminarse los encabezados obligatorios.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/>
                                    </svg>
                                </div>
                                <p class="text-sm font-semibold text-utec-gray-dark">Columnas obligatorias</p>
                            </div>
                        </div>
                        <div class="card-body">
                            <ul class="list-disc space-y-1 pl-5 text-sm text-utec-gray-dark">
                                <li>COD ASIG</li>
                                <li>ASIGNATURA</li>
                                <li>SEC</li>
                                <li>TIPO</li>
                                <li>DOCENTE</li>
                            </ul>
                            <p class="mt-3 text-xs text-gray-400">Para presencial y en línea también se requieren columnas de días y horas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-utec-gray-dark">Últimas importaciones</p>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Fecha</th>
                                    <th class="th-utec">Ciclo</th>
                                    <th class="th-utec">Archivo</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec">Filas</th>
                                    <th class="th-utec">Secciones</th>
                                    <th class="th-utec">Usuario</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($ultimasImportaciones as $importacion)
                                    <tr class="transition-colors hover:bg-utec-primary-soft">
                                        <td class="px-6 py-4 text-sm tabular-nums text-utec-gray-dark">{{ $importacion->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-utec-gray-dark">{{ $importacion->ciclo?->nombre ?? '—' }}</td>
                                        <td class="px-6 py-4 text-sm text-utec-gray-dark">{{ $importacion->nombre_archivo }}</td>
                                        <td class="px-6 py-4">
                                            @if($importacion->estado === 'procesado')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>Procesado
                                                </span>
                                            @elseif($importacion->estado === 'procesado_con_observaciones')
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-700">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-orange-500"></span>Con observaciones
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-500">
                                                    <span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>Fallido
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm tabular-nums text-utec-gray-dark">{{ $importacion->filas_importadas }} / {{ $importacion->filas_error }} err.</td>
                                        <td class="px-6 py-4 text-sm tabular-nums text-utec-gray-dark">{{ $importacion->secciones_creadas }} / {{ $importacion->secciones_actualizadas }} act.</td>
                                        <td class="px-6 py-4 text-sm text-utec-gray-dark">{{ $importacion->usuario?->nombre ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="h-10 w-10 text-gray-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/>
                                                </svg>
                                                <p class="text-sm font-medium text-gray-400">Aún no hay importaciones registradas.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>