@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Los periodos de evaluación pertenecen a un ciclo académico. Solo puede existir un periodo activo por ciclo.
    Al guardar, el sistema genera consolidados pendientes para tutores con asignaciones publicadas.
</div>

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
                @selected((string) old('ciclo_id', $periodo->ciclo_id ?? optional($ciclos->firstWhere('activo', true))->id) === (string) $ciclo->id)>
                {{ $ciclo->nombre }} {{ $ciclo->activo ? '(Activo)' : '' }}
            </option>
            @endforeach
        </select>

        @error('ciclo_id')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <p class="form-hint">
            Para este prototipo solo se permite crear o editar periodos del ciclo activo.
        </p>
    </div>

    <div>
        <label for="nombre" class="form-label">
            Nombre del periodo <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $periodo->nombre ?? '') }}"
            class="input-field"
            maxlength="100"
            placeholder="Ej. Primera Evaluación Ordinaria"
            required>

        @error('nombre')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha_inicio" class="form-label">
            Fecha de inicio <span class="text-red-500">*</span>
        </label>

        <input
            type="date"
            name="fecha_inicio"
            id="fecha_inicio"
            value="{{ old('fecha_inicio', isset($periodo) ? $periodo->fecha_inicio->format('Y-m-d') : '') }}"
            class="input-field"
            required>

        @error('fecha_inicio')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha_fin" class="form-label">
            Fecha de fin <span class="text-red-500">*</span>
        </label>

        <input
            type="date"
            name="fecha_fin"
            id="fecha_fin"
            value="{{ old('fecha_fin', isset($periodo) ? $periodo->fecha_fin->format('Y-m-d') : '') }}"
            class="input-field"
            required>

        @error('fecha_fin')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha_limite_consolidado" class="form-label">
            Fecha límite de consolidado <span class="text-red-500">*</span>
        </label>

        <input
            type="date"
            name="fecha_limite_consolidado"
            id="fecha_limite_consolidado"
            value="{{ old('fecha_limite_consolidado', isset($periodo) ? $periodo->fecha_limite_consolidado->format('Y-m-d') : '') }}"
            class="input-field"
            required>

        @error('fecha_limite_consolidado')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <p class="form-hint">
            Debe ser igual o posterior a la fecha de fin del periodo y estar dentro de las fechas del ciclo activo.
        </p>
    </div>

    <div class="flex items-end">
        <div>
            <label class="inline-flex items-center gap-2">
                <input type="hidden" name="activo" value="0">

                <input
                    type="checkbox"
                    name="activo"
                    value="1"
                    class="rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                    @checked((bool) old('activo', $periodo->activo ?? false))
                >

                <span class="text-sm font-medium text-utec-gray-dark">
                    Marcar como periodo activo
                </span>
            </label>

            @error('activo')
            <p class="form-error">{{ $message }}</p>
            @enderror

            @error('periodo')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </div>


</div>

@if(isset($periodo))
<div class="mt-5 grid gap-4 md:grid-cols-2">
    <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
        <p class="text-xs text-gray-500">Casos asociados</p>
        <p class="mt-2 text-2xl font-bold text-utec-primary">
            {{ $periodo->casos_seguimiento_count ?? 0 }}
        </p>
    </div>

    <div class="rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
        <p class="text-xs text-gray-500">Consolidados generados</p>
        <p class="mt-2 text-2xl font-bold text-utec-primary">
            {{ $periodo->consolidados_count ?? 0 }}
        </p>
    </div>
</div>
@endif

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('periodos.index') }}" class="btn-secondary">
        Cancelar
    </a>

    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>