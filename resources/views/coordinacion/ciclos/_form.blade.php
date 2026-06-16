@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    El nombre del ciclo se genera automáticamente con el formato
    <span class="font-semibold">AÑO-CICLO</span>. Ejemplo:
    <span class="font-semibold">2026-01</span>.
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="anio" class="form-label">
            Año <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="anio"
            id="anio"
            value="{{ old('anio', $ciclo->anio ?? now()->year) }}"
            class="input-field"
            min="{{ now()->year - 1 }}"
            max="{{ now()->year + 1 }}"
            required>
        @error('anio')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Solo se permite el año anterior, el año actual o el año siguiente.
        </p>
    </div>

    <div>
        <label for="periodo" class="form-label">
            Ciclo <span class="text-red-500">*</span>
        </label>
        <select name="periodo" id="periodo" class="input-field" required>
            <option value="">Seleccione...</option>
            <option value="1" @selected((string) old('periodo', $ciclo->periodo ?? '') === '1')>
                Ciclo 1
            </option>
            <option value="2" @selected((string) old('periodo', $ciclo->periodo ?? '') === '2')>
                Ciclo 2
            </option>
            <option value="3" @selected((string) old('periodo', $ciclo->periodo ?? '') === '3')>
                Ciclo 3
            </option>
        </select>
        @error('periodo')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="form-label">
            Nombre generado
        </label>
        <div class="rounded-md border border-utec-gray-medium bg-gray-50 px-3 py-2 text-sm font-semibold text-utec-gray-dark">
            @php
            $anioVista = old('anio', $ciclo->anio ?? now()->year);
            $periodoVista = old('periodo', $ciclo->periodo ?? 1);
            $nombreGenerado = $anioVista && $periodoVista
            ? sprintf('%d-%02d', (int) $anioVista, (int) $periodoVista)
            : 'Se generará al guardar';
            @endphp

            {{ $nombreGenerado }}
        </div>
        @error('nombre')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Este valor se guarda automáticamente. No se edita manualmente.
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
                    @checked((bool) old('activo', $ciclo->activo ?? false))
                >

                <span class="text-sm font-medium text-utec-gray-dark">
                    Marcar como ciclo activo
                </span>
            </label>

            @error('activo')
            <p class="form-error">{{ $message }}</p>
            @enderror

            @error('ciclo')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div>
        <label for="fecha_inicio" class="form-label">
            Fecha de inicio <span class="text-red-500">*</span>
        </label>
        <input
            type="date"
            name="fecha_inicio"
            id="fecha_inicio"
            value="{{ old('fecha_inicio', isset($ciclo) ? $ciclo->fecha_inicio->format('Y-m-d') : '') }}"
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
            value="{{ old('fecha_fin', isset($ciclo) ? $ciclo->fecha_fin->format('Y-m-d') : '') }}"
            class="input-field"
            required>

        @error('fecha_fin')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <p class="form-hint">
            La fecha de inicio y fin deben pertenecer al año seleccionado y no traslaparse con otro ciclo.
        </p>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('ciclos.index') }}" class="btn-secondary">
        Cancelar
    </a>

    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>