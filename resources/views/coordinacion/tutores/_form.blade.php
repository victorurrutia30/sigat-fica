@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Solo se registran tutores que sean <span class="font-semibold">Docentes de Tiempo Completo</span>.
    La cuenta de usuario es opcional y puede vincularse después.
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="codigo_empleado" class="form-label">
            Código de empleado <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="codigo_empleado"
            id="codigo_empleado"
            value="{{ old('codigo_empleado', $tutor->codigo_empleado ?? '') }}"
            class="input-field uppercase"
            maxlength="30"
            placeholder="Ej. DTC-006"
            required>
        @error('codigo_empleado')
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
            value="{{ old('nombre_completo', $tutor->nombre_completo ?? '') }}"
            class="input-field"
            maxlength="200"
            placeholder="Ej. Juan Pérez"
            required>
        @error('nombre_completo')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="correo_institucional" class="form-label">
            Correo institucional <span class="text-red-500">*</span>
        </label>
        <input
            type="email"
            name="correo_institucional"
            id="correo_institucional"
            value="{{ old('correo_institucional', $tutor->correo_institucional ?? '') }}"
            class="input-field"
            maxlength="191"
            placeholder="nombre@utec.edu.sv"
            required>
        @error('correo_institucional')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="departamento" class="form-label">
            Departamento
        </label>
        <input
            type="text"
            name="departamento"
            id="departamento"
            value="{{ old('departamento', $tutor->departamento ?? '') }}"
            class="input-field"
            maxlength="100"
            placeholder="Ej. Programación">
        @error('departamento')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="fecha_contratacion" class="form-label">
            Fecha de contratación
        </label>
        <input
            type="date"
            name="fecha_contratacion"
            id="fecha_contratacion"
            value="{{ old('fecha_contratacion', isset($tutor) && $tutor->fecha_contratacion ? $tutor->fecha_contratacion->format('Y-m-d') : '') }}"
            class="input-field">
        @error('fecha_contratacion')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="usuario_id" class="form-label">
            Cuenta de usuario vinculada
        </label>
        <select name="usuario_id" id="usuario_id" class="input-field">
            <option value="">Sin cuenta vinculada</option>
            @foreach($usuariosDisponibles as $usuario)
            <option value="{{ $usuario->id }}" @selected((int) old('usuario_id', $tutor->usuario_id ?? 0) === $usuario->id)>
                {{ $usuario->nombre }} — {{ $usuario->correo }}
            </option>
            @endforeach
        </select>
        @error('usuario_id')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Solo aparecen usuarios activos con rol tutor que aún no están vinculados a otro tutor.
        </p>
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="activo" value="0">
            <input
                type="checkbox"
                name="activo"
                value="1"
                class="rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('activo', $tutor->activo ?? true))
            >
            <span class="text-sm font-medium text-utec-gray-dark">Tutor activo</span>
        </label>
    </div>

    <div class="flex items-end">
        <div>
            <input type="hidden" name="tiempo_completo" value="1">
            <span class="badge-success">DTC confirmado</span>
            <p class="form-hint mt-2">
                RN-01: solo Docentes de Tiempo Completo pueden ser tutores.
            </p>
            @error('tiempo_completo')
            <p class="form-error">{{ $message }}</p>
            @enderror
        </div>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('tutores.index') }}" class="btn-secondary">
        Cancelar
    </a>

    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>