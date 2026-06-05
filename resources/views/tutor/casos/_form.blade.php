@csrf

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">

    <div class="md:col-span-2">
        <label for="seccion_id" class="form-label">
            Sección <span class="text-red-500">*</span>
        </label>
        <select name="seccion_id" id="seccion_id" class="input-field" required>
            <option value="">Seleccione una sección...</option>
            @foreach($secciones as $seccion)
                <option value="{{ $seccion->id }}" @selected(old('seccion_id', $caso->seccion_id ?? '') == $seccion->id)>
                    {{ $seccion->materia?->codigo }} — {{ $seccion->materia?->nombre }} · Sec. {{ $seccion->numero_seccion }}
                </option>
            @endforeach
        </select>
        @error('seccion_id')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="carne" class="form-label">
            Carné del estudiante <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="carne"
            id="carne"
            value="{{ old('carne', $caso->estudiante?->carne ?? '') }}"
            class="input-field"
            placeholder="Ej. 22001234"
            required>
        @error('carne')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nombre_completo" class="form-label">
            Nombre completo <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre_completo"
            id="nombre_completo"
            value="{{ old('nombre_completo', $caso->estudiante?->nombre_completo ?? '') }}"
            class="input-field"
            placeholder="Nombre completo del estudiante"
            required>
        @error('nombre_completo')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="correo" class="form-label">Correo</label>
        <input
            type="email"
            name="correo"
            id="correo"
            value="{{ old('correo', $caso->estudiante?->correo ?? '') }}"
            class="input-field"
            placeholder="correo@universidad.edu.sv">
        @error('correo')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="causa_id" class="form-label">Causa</label>
        <select name="causa_id" id="causa_id" class="input-field">
            <option value="">Sin causa asignada</option>
            @foreach($causas as $causa)
                <option value="{{ $causa->id }}" @selected(old('causa_id', $caso->causa_id ?? '') == $causa->id)>
                    {{ $causa->nombre }}
                </option>
            @endforeach
        </select>
        @error('causa_id')
            <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">Obligatorio al cerrar el caso.</p>
    </div>

    <div>
        <label for="resultado_final" class="form-label">Resultado final</label>
        <select name="resultado_final" id="resultado_final" class="input-field">
            <option value="">Sin resultado</option>
            <option value="retiro" @selected(old('resultado_final', $caso->resultado_final ?? '') === 'retiro')>Retiro</option>
            <option value="abandono" @selected(old('resultado_final', $caso->resultado_final ?? '') === 'abandono')>Abandono</option>
        </select>
        @error('resultado_final')
            <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">Obligatorio al cerrar el caso.</p>
    </div>

</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('casos.index') }}" class="btn-secondary">
        Cancelar
    </a>
    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>