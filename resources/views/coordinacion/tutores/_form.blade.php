@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Antes de crear un tutor, busca primero por código o nombre en el catálogo.
    Si ya existe, edita el registro existente. Por regla general los tutores deben ser <span class="font-semibold">DTC</span>; si no lo son, debe registrarse una excepción autorizada con motivo.
</div>

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="codigo_empleado" class="form-label">
            Código de empleado / docente <span class="text-red-500">*</span>
        </label>

        <input
            type="text"
            name="codigo_empleado"
            id="codigo_empleado"
            value="{{ ($codigoEmpleadoBloqueado ?? false) ? ($tutor->codigo_empleado ?? '') : old('codigo_empleado', $tutor->codigo_empleado ?? '') }}"
            class="input-field uppercase {{ ($codigoEmpleadoBloqueado ?? false) ? 'cursor-not-allowed bg-gray-100 text-gray-600' : '' }}"
            maxlength="30"
            placeholder="Ej. 12345"
            required
            @readonly($codigoEmpleadoBloqueado ?? false)>

        @error('codigo_empleado')
        <p class="form-error">{{ $message }}</p>
        @enderror

        @if($codigoEmpleadoBloqueado ?? false)
        <p class="form-hint text-orange-700">
            Este código está bloqueado porque ya está vinculado a secciones importadas desde la carga académica.
        </p>
        @else
        <p class="form-hint">
            Si viene de carga académica, usa el mismo código docente para poder validar choques con sus clases.
        </p>
        @endif
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
            Departamento / área
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
        <label for="categoria_docente" class="form-label">
            Categoría docente
        </label>
        <input
            type="text"
            name="categoria_docente"
            id="categoria_docente"
            value="{{ old('categoria_docente', $tutor->categoria_docente ?? '') }}"
            class="input-field uppercase"
            maxlength="30"
            placeholder="Ej. DTC, DHC, COO, TEC">
        @error('categoria_docente')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Dato usado para diferenciar DTC de excepciones autorizadas.
        </p>
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

    <div class="md:col-span-2 rounded-md border border-utec-gray-medium bg-gray-50 p-4">
        <p class="text-sm font-semibold text-utec-gray-dark">
            Habilitación para propuesta
        </p>

        <div class="mt-4 grid gap-4 md:grid-cols-3">
            <label class="inline-flex items-start gap-2">
                <input type="hidden" name="tiempo_completo" value="0">
                <input
                    type="checkbox"
                    name="tiempo_completo"
                    value="1"
                    class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                    @checked((bool) old('tiempo_completo', $tutor->tiempo_completo ?? true))
                >
                <span>
                    <span class="block text-sm font-medium text-utec-gray-dark">Docente de Tiempo Completo</span>
                    <span class="block text-xs text-gray-500">Regla principal para asignación.</span>
                </span>
            </label>

            <label class="inline-flex items-start gap-2">
                <input type="hidden" name="habilitado_para_tutorias" value="0">
                <input
                    type="checkbox"
                    name="habilitado_para_tutorias"
                    value="1"
                    class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                    @checked((bool) old('habilitado_para_tutorias', $tutor->habilitado_para_tutorias ?? true))
                >
                <span>
                    <span class="block text-sm font-medium text-utec-gray-dark">Habilitado para tutorías</span>
                    <span class="block text-xs text-gray-500">Si está desmarcado, no aparecerá en propuesta.</span>
                </span>
            </label>

            <label class="inline-flex items-start gap-2">
                <input type="hidden" name="es_excepcion_tutoria" value="0">
                <input
                    type="checkbox"
                    name="es_excepcion_tutoria"
                    value="1"
                    class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                    @checked((bool) old('es_excepcion_tutoria', $tutor->es_excepcion_tutoria ?? false))
                >
                <span>
                    <span class="block text-sm font-medium text-utec-gray-dark">Excepción autorizada</span>
                    <span class="block text-xs text-gray-500">Usar solo si no es DTC.</span>
                </span>
            </label>
        </div>

        <div class="mt-4">
            <label for="motivo_excepcion_tutoria" class="form-label">
                Motivo de excepción
            </label>
            <textarea
                name="motivo_excepcion_tutoria"
                id="motivo_excepcion_tutoria"
                rows="3"
                class="input-field"
                maxlength="1000"
                placeholder="Ej. Coordinación autorizó apoyo temporal por disponibilidad de especialistas.">{{ old('motivo_excepcion_tutoria', $tutor->motivo_excepcion_tutoria ?? '') }}</textarea>
            @error('motivo_excepcion_tutoria')
            <p class="form-error">{{ $message }}</p>
            @enderror
            <p class="form-hint">
                Obligatorio si el tutor no es DTC y se marca como excepción autorizada.
            </p>
        </div>

        @error('tiempo_completo')
        <p class="form-error mt-2">{{ $message }}</p>
        @enderror

        @error('habilitado_para_tutorias')
        <p class="form-error mt-2">{{ $message }}</p>
        @enderror

        @error('es_excepcion_tutoria')
        <p class="form-error mt-2">{{ $message }}</p>
        @enderror
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