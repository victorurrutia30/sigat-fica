@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Marca como <span class="font-semibold">gestionada por Coordinación</span> solo las materias que entran al Programa de Tutores.
    Las materias creadas desde carga académica pueden quedar pendientes de revisión hasta completar sus datos.
</div>

<div
    x-data="{
        gestionada: @js((bool) old('gestionada_por_coordinacion', $materia->gestionada_por_coordinacion ?? false)),
        requiereRevision: @js((bool) old('requiere_revision', $materia->requiere_revision ?? false))
    }"
    class="grid grid-cols-1 gap-4 md:grid-cols-2">
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
            placeholder="Ej. MAT1-T"
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
        <label for="ciclo_plan" class="form-label">
            Ubicación en plan de estudios
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
            Indica en qué ciclo del plan de estudios se ubica la materia. No es el ciclo académico de importación.
        </p>
    </div>

    <div>
        <label for="departamento" class="form-label">
            Código de cátedra
        </label>
        <input
            type="text"
            name="departamento"
            id="departamento"
            value="{{ old('departamento', $materia->departamento ?? '') }}"
            class="input-field"
            maxlength="100"
            placeholder="Ej. MAT">
        @error('departamento')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Corresponde al código de cátedra importado desde la carga académica.
        </p>
    </div>

    <div class="space-y-4 rounded-md border border-utec-gray-medium bg-gray-50 p-4 md:col-span-2">
        <label class="flex items-start gap-2">
            <input type="hidden" name="gestionada_por_coordinacion" value="0">
            <input
                type="checkbox"
                name="gestionada_por_coordinacion"
                value="1"
                class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                x-model="gestionada"
                @change="if (gestionada) requiereRevision = false">
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
                class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light disabled:cursor-not-allowed disabled:opacity-60"
                x-model="requiereRevision"
                :disabled="gestionada">
            <span>
                <span class="block text-sm font-medium text-utec-gray-dark">
                    Pendiente de revisión
                </span>
                <span class="block text-xs text-gray-500">
                    Se usa para materias importadas que aún no han sido revisadas. Al marcar la materia como gestionada, este estado se desactiva automáticamente.
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
                    Las materias inactivas no deben usarse en nuevas propuestas.
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