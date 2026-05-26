@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Las causas se usan para clasificar el motivo identificado en cada caso de seguimiento.
    Si una causa ya no se utiliza, debe desactivarse en lugar de eliminarse.
</div>

<div class="grid grid-cols-1 gap-4">
    <div>
        <label for="nombre" class="form-label">
            Nombre <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $causa->nombre ?? '') }}"
            class="input-field"
            maxlength="120"
            placeholder="Ej. Situación económica"
            required>

        @error('nombre')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="descripcion" class="form-label">
            Descripción
        </label>

        <textarea
            name="descripcion"
            id="descripcion"
            rows="4"
            class="input-field"
            maxlength="1000"
            placeholder="Describe cuándo debe usarse esta causa.">{{ old('descripcion', $causa->descripcion ?? '') }}</textarea>

        @error('descripcion')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <p class="form-hint">
            Campo opcional. Ayuda a que los tutores seleccionen la causa correcta.
        </p>
    </div>

    <div>
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="activo" value="0">

            <input
                type="checkbox"
                name="activo"
                value="1"
                class="rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('activo', $causa->activo ?? true))
            >

            <span class="text-sm font-medium text-utec-gray-dark">
                Causa activa
            </span>
        </label>

        @error('activo')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>
</div>

@if(isset($causa))
<div class="mt-5 rounded-lg border border-utec-gray-medium bg-gray-50 p-4">
    <p class="text-xs text-gray-500">Casos asociados históricamente</p>
    <p class="mt-2 text-2xl font-bold text-utec-primary">
        {{ $causa->casos_seguimiento_count ?? 0 }}
    </p>
</div>
@endif

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('causas.index') }}" class="btn-secondary">
        Cancelar
    </a>

    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>