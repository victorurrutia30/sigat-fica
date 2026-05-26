@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Marca como <span class="font-semibold">gestionada por Coordinación</span> solo las materias que entran al Programa de Tutores.
    Las materias creadas desde carga académica pueden quedar pendientes de revisión hasta completar sus datos.
</div>

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
            placeholder="Ej. MAT101"
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
            placeholder="Ej. MATEMÁTICA I"
            required>
        @error('nombre')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="creditos" class="form-label">
            Créditos
        </label>
        <input
            type="number"
            name="creditos"
            id="creditos"
            value="{{ old('creditos', $materia->creditos ?? 3) }}"
            class="input-field"
            min="1"
            max="10">
        @error('creditos')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Este dato no se usa para asignar tutores. Si se deja vacío, se guarda como 3.
        </p>
    </div>

    <div>
        <label for="ciclo_plan" class="form-label">
            Ciclo del plan
        </label>
        <select name="ciclo_plan" id="ciclo_plan" class="input-field">
            <option value="">Pendiente de definir</option>
            @for($i = 1; $i <= 10; $i++)
                <option value="{{ $i }}" @selected((int) old('ciclo_plan', $materia->ciclo_plan ?? 0) === $i)>
                Ciclo {{ $i }}
                </option>
                @endfor
        </select>
        @error('ciclo_plan')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Solo las materias de ciclo 1 y 2 se marcarán como prioritarias en la propuesta.
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
            placeholder="Ej. Ciencias Aplicadas">
        @error('departamento')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="space-y-4 rounded-md border border-utec-gray-medium bg-gray-50 p-4">
        <label class="flex items-start gap-2">
            <input type="hidden" name="gestionada_por_coordinacion" value="0">
            <input
                type="checkbox"
                name="gestionada_por_coordinacion"
                value="1"
                class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('gestionada_por_coordinacion', $materia->gestionada_por_coordinacion ?? false))
            >
            <span>
                <span class="block text-sm font-medium text-utec-gray-dark">
                    Gestionada por Coordinación
                </span>
                <span class="block text-xs text-gray-500">
                    Si está marcada, sus secciones podrán entrar a la propuesta de asignación.
                </span>
            </span>
        </label>

        <label class="flex items-start gap-2">
            <input type="hidden" name="requiere_revision" value="0">
            <input
                type="checkbox"
                name="requiere_revision"
                value="1"
                class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('requiere_revision', $materia->requiere_revision ?? false))
            >
            <span>
                <span class="block text-sm font-medium text-utec-gray-dark">
                    Pendiente de revisión
                </span>
                <span class="block text-xs text-gray-500">
                    Úsalo para materias creadas desde Excel que necesitan completar ciclo del plan u otros datos.
                </span>
            </span>
        </label>

        <label class="flex items-start gap-2">
            <input type="hidden" name="activo" value="0">
            <input
                type="checkbox"
                name="activo"
                value="1"
                class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('activo', $materia->activo ?? true))
            >
            <span>
                <span class="block text-sm font-medium text-utec-gray-dark">
                    Materia activa
                </span>
                <span class="block text-xs text-gray-500">
                    Las materias inactivas no deben usarse en nuevas importaciones o propuestas.
                </span>
            </span>
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