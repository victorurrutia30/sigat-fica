@csrf

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="codigo" class="form-label">
            Código <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="codigo"
            id="codigo"
            value="{{ old('codigo', $materia->codigo ?? '') }}"
            class="input-field uppercase"
            maxlength="20"
            placeholder="Ej. FICA-PRG01"
            required>
        @error('codigo')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="nombre" class="form-label">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $materia->nombre ?? '') }}"
            class="input-field"
            maxlength="150"
            placeholder="Ej. Programación I"
            required>
        @error('nombre')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="creditos" class="form-label">
            Créditos <span class="text-red-500">*</span>
        </label>
        <input
            type="number"
            name="creditos"
            id="creditos"
            value="{{ old('creditos', $materia->creditos ?? 3) }}"
            class="input-field"
            min="1"
            max="10"
            required>
        @error('creditos')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="ciclo_plan" class="form-label">
            Ciclo del plan <span class="text-red-500">*</span>
        </label>
        <select name="ciclo_plan" id="ciclo_plan" class="input-field" required>
            <option value="">Seleccione...</option>
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" @selected((int) old('ciclo_plan', $materia->ciclo_plan ?? '') === $i)>
                Ciclo {{ $i }}
                </option>
                @endfor
        </select>
        @error('ciclo_plan')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Las materias de ciclo 1 y 2 se marcarán como prioritarias en la propuesta.
        </p>
    </div>

    <div>
        <label for="departamento" class="form-label">
            Departamento
        </label>
        <input
            type="text"
            name="departamento"
            id="departamento"
            value="{{ old('departamento', $materia->departamento ?? '') }}"
            class="input-field"
            maxlength="100"
            placeholder="Ej. Programación">
        @error('departamento')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="activo" value="0">
            <input
                type="checkbox"
                name="activo"
                value="1"
                class="rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('activo', $materia->activo ?? true))
            >
            <span class="text-sm font-medium text-utec-gray-dark">Materia activa</span>
        </label>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('materias.index') }}" class="btn-secondary">
        Cancelar
    </a>

    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>