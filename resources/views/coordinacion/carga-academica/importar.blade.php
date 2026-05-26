<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Importar carga académica</h2>
                <p class="text-sm text-gray-500">
                    Carga secciones, horarios y docentes titulares desde el Excel institucional.
                </p>
            </div>

            <a href="{{ route('materias.index', ['revision' => 'pendientes']) }}" class="btn-secondary">
                Ver materias pendientes
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 rounded-md border-l-4 border-green-600 bg-green-50 p-4 text-sm text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 rounded-md border-l-4 border-red-600 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
            @endif

            @if(session('resumen_importacion'))
            @php($resumen = session('resumen_importacion'))
            @php($contadores = $resumen['contadores'] ?? [])

            <div class="mb-6">
                <div class="card">
                    <div class="card-header">
                        <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-utec-gray-dark">
                                    Resumen de importación
                                </h3>
                                <p class="text-sm text-gray-500">
                                    Estado: {{ str_replace('_', ' ', $resumen['estado'] ?? 'sin estado') }}
                                </p>
                            </div>

                            @if(session('importacion_id'))
                            <span class="badge-muted">
                                Registro #{{ session('importacion_id') }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="grid gap-4 md:grid-cols-5">
                            <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                                <p class="text-xs text-gray-500">Hojas procesadas</p>
                                <p class="mt-2 text-2xl font-bold text-utec-primary">
                                    {{ $contadores['hojas_procesadas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                                <p class="text-xs text-gray-500">Filas leídas</p>
                                <p class="mt-2 text-2xl font-bold text-utec-primary">
                                    {{ $contadores['filas_leidas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                                <p class="text-xs text-gray-500">Filas importadas</p>
                                <p class="mt-2 text-2xl font-bold text-green-700">
                                    {{ $contadores['filas_importadas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                                <p class="text-xs text-gray-500">Filas ignoradas</p>
                                <p class="mt-2 text-2xl font-bold text-orange-700">
                                    {{ $contadores['filas_ignoradas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-white p-4">
                                <p class="text-xs text-gray-500">Filas con error</p>
                                <p class="mt-2 text-2xl font-bold text-red-700">
                                    {{ $contadores['filas_error'] ?? 0 }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 md:grid-cols-5">
                            <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Materias creadas</p>
                                <p class="mt-2 text-xl font-bold text-utec-gray-dark">
                                    {{ $contadores['materias_creadas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Materias actualizadas</p>
                                <p class="mt-2 text-xl font-bold text-utec-gray-dark">
                                    {{ $contadores['materias_actualizadas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Secciones creadas</p>
                                <p class="mt-2 text-xl font-bold text-utec-gray-dark">
                                    {{ $contadores['secciones_creadas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Secciones actualizadas</p>
                                <p class="mt-2 text-xl font-bold text-utec-gray-dark">
                                    {{ $contadores['secciones_actualizadas'] ?? 0 }}
                                </p>
                            </div>

                            <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
                                <p class="text-xs text-gray-500">Horarios creados</p>
                                <p class="mt-2 text-xl font-bold text-utec-gray-dark">
                                    {{ $contadores['horarios_creados'] ?? 0 }}
                                </p>
                            </div>
                        </div>

                        @if(! empty($resumen['hojas']))
                        <div class="mt-4 rounded-md bg-utec-primary-soft p-3 text-sm text-utec-gray-dark">
                            <span class="font-semibold">Hojas procesadas:</span>
                            {{ implode(', ', $resumen['hojas']) }}
                        </div>
                        @endif

                        @if(! empty($resumen['advertencias']))
                        <div class="mt-4 rounded-md bg-orange-50 p-3 text-sm text-orange-800">
                            <p class="font-semibold">Advertencias:</p>
                            <ul class="mt-2 list-disc space-y-1 pl-5">
                                @foreach($resumen['advertencias'] as $advertencia)
                                <li>
                                    Hoja {{ $advertencia['hoja'] ?? '-' }},
                                    fila {{ $advertencia['fila'] ?? '-' }}:
                                    {{ $advertencia['mensaje'] ?? 'Sin detalle' }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            @if(session('errores_importacion') && count(session('errores_importacion')) > 0)
            <div class="card mb-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">Errores detectados</h3>
                    <p class="text-sm text-gray-500">
                        Estas filas no se importaron. Corrige el archivo o revisa el catálogo según corresponda.
                    </p>
                </div>

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
                            <tr>
                                <td class="td-utec font-semibold">{{ $error['hoja'] ?? '-' }}</td>
                                <td class="td-utec">{{ $error['fila'] ?? '-' }}</td>
                                <td class="td-utec">{{ $error['mensaje'] ?? 'Sin detalle' }}</td>
                                <td class="td-utec">
                                    @if(! empty($error['datos']))
                                    <pre class="max-w-md overflow-x-auto rounded bg-gray-50 p-2 text-xs text-gray-700">{{ json_encode($error['datos'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                    @else
                                    <span class="text-gray-400">Sin datos</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">Subir archivo</h3>
                            <p class="text-sm text-gray-500">
                                Selecciona el ciclo al que pertenece la carga académica.
                            </p>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="{{ route('carga-academica.store') }}" enctype="multipart/form-data">
                                @csrf

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div>
                                        <label for="ciclo_id" class="form-label">
                                            Ciclo académico <span class="text-red-500">*</span>
                                        </label>
                                        <select name="ciclo_id" id="ciclo_id" class="input-field" required>
                                            <option value="">Seleccione...</option>
                                            @foreach($ciclos as $ciclo)
                                            <option
                                                value="{{ $ciclo->id }}"
                                                @selected((string) old('ciclo_id', $ciclo->activo ? $ciclo->id : '') === (string) $ciclo->id)
                                                >
                                                {{ $ciclo->nombre }}
                                                {{ $ciclo->activo ? '(Activo)' : '' }}
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
                                        <input
                                            type="file"
                                            name="archivo"
                                            id="archivo"
                                            class="input-field"
                                            accept=".xlsx,.xls"
                                            required>
                                        @error('archivo')
                                        <p class="form-error">{{ $message }}</p>
                                        @enderror
                                        <p class="form-hint">
                                            Formatos permitidos: .xlsx y .xls. Tamaño máximo: 10 MB.
                                        </p>
                                    </div>
                                </div>

                                <div class="mt-6 rounded-md border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                                    <p class="font-semibold">Antes de importar:</p>
                                    <ul class="mt-2 list-disc space-y-1 pl-5">
                                        <li>Verifica que elegiste el ciclo correcto.</li>
                                        <li>Las secciones existentes se actualizarán si vuelven a venir en el archivo.</li>
                                        <li>Los horarios de las secciones importadas se reemplazarán por los del Excel.</li>
                                        <li>Las secciones que ya existían pero no aparecen en el nuevo Excel no se eliminarán.</li>
                                    </ul>
                                </div>

                                <div class="mt-6 flex justify-end">
                                    <button
                                        type="submit"
                                        class="btn-primary"
                                        onclick="return confirm('¿Seguro que deseas importar esta carga académica? Esta acción actualizará secciones y horarios del ciclo seleccionado.')">
                                        Importar carga académica
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">Reglas de importación</h3>
                        </div>

                        <div class="card-body">
                            <ul class="list-disc space-y-2 pl-5 text-sm text-utec-gray-dark">
                                <li>Se procesan las hojas <span class="font-semibold">VIRTUALES Y SETACO</span>, <span class="font-semibold">INFO</span> y <span class="font-semibold">CCAA</span>.</li>
                                <li>Las filas con tipo <span class="font-semibold">Par</span> se ignoran con advertencia.</li>
                                <li><span class="font-semibold">P</span> se importa como presencial.</li>
                                <li><span class="font-semibold">EL</span> y <span class="font-semibold">E.L.</span> se importan como en línea.</li>
                                <li><span class="font-semibold">V</span> se importa como virtual, sin horario y sin requerir tutor por defecto.</li>
                                <li>Si una materia no existe, se crea como pendiente de revisión.</li>
                                <li>El orden de columnas puede cambiar, pero no deben eliminarse los encabezados obligatorios.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="text-base font-semibold text-utec-gray-dark">Columnas obligatorias</h3>
                        </div>

                        <div class="card-body">
                            <ul class="list-disc space-y-1 pl-5 text-sm text-utec-gray-dark">
                                <li>COD ASIG</li>
                                <li>ASIGNATURA</li>
                                <li>SEC</li>
                                <li>TIPO</li>
                                <li>DOCENTE</li>
                            </ul>

                            <p class="mt-4 text-sm text-gray-500">
                                Para presencial y en línea también se requieren columnas de días y horas.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-6">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">Últimas importaciones</h3>
                </div>

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
                            <tr>
                                <td class="td-utec">
                                    {{ $importacion->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="td-utec">
                                    {{ $importacion->ciclo?->nombre ?? 'Sin ciclo' }}
                                </td>
                                <td class="td-utec">
                                    {{ $importacion->nombre_archivo }}
                                </td>
                                <td class="td-utec">
                                    @if($importacion->estado === 'procesado')
                                    <span class="badge-success">Procesado</span>
                                    @elseif($importacion->estado === 'procesado_con_observaciones')
                                    <span class="badge-warning">Con observaciones</span>
                                    @else
                                    <span class="badge-muted">Fallido</span>
                                    @endif
                                </td>
                                <td class="td-utec">
                                    {{ $importacion->filas_importadas }} importadas /
                                    {{ $importacion->filas_error }} error
                                </td>
                                <td class="td-utec">
                                    {{ $importacion->secciones_creadas }} creadas /
                                    {{ $importacion->secciones_actualizadas }} actualizadas
                                </td>
                                <td class="td-utec">
                                    {{ $importacion->usuario?->nombre ?? 'No disponible' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                    Aún no hay importaciones registradas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>