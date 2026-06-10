@csrf

<div class="mb-5 rounded-md border border-utec-gray-medium bg-utec-primary-soft px-4 py-3 text-sm text-utec-gray-dark">
    Administra las cuentas que pueden ingresar a SIGAT-FICA.
    Los usuarios con rol <span class="font-semibold">Tutor</span> pueden vincularse a un tutor registrado cuando necesiten acceso al portal.
</div>

@if(isset($tutorPreseleccionado) && $tutorPreseleccionado)
<div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
    Se usarán los datos del tutor:
    <span class="font-semibold">{{ $tutorPreseleccionado->nombre_completo }}</span>
    —
    {{ $tutorPreseleccionado->correo_institucional }}.
    Define la contraseña inicial antes de crear la cuenta.
</div>
@endif

<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
    <div>
        <label for="nombre" class="form-label">
            Nombre <span class="text-red-500">*</span>
        </label>
        <input
            type="text"
            name="nombre"
            id="nombre"
            value="{{ old('nombre', $usuario->nombre ?? $tutorPreseleccionado?->nombre_completo ?? '') }}"
            class="input-field"
            maxlength="255"
            required>
        @error('nombre')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="correo" class="form-label">
            Correo <span class="text-red-500">*</span>
        </label>
        <input
            type="email"
            name="correo"
            id="correo"
            value="{{ old('correo', $usuario->correo ?? $tutorPreseleccionado?->correo_institucional ?? '') }}"
            class="input-field"
            maxlength="255"
            placeholder="nombre@utec.edu.sv"
            required>
        @error('correo')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="rol" class="form-label">
            Rol <span class="text-red-500">*</span>
        </label>
        <select name="rol" id="rol" class="input-field" required>
            <option value="">Seleccione...</option>

            <option
                value="coordinacion"
                @selected(old('rol', $usuario->rol ?? ($tutorPreseleccionado ? 'tutor' : '')) === 'coordinacion')>
                Coordinación
            </option>

            <option
                value="tutor"
                @selected(old('rol', $usuario->rol ?? ($tutorPreseleccionado ? 'tutor' : '')) === 'tutor')>
                Tutor
            </option>
        </select>
        @error('rol')
        <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tutor_id" class="form-label">
            Tutor vinculado
        </label>
        <select name="tutor_id" id="tutor_id" class="input-field">
            <option value="">Sin tutor vinculado</option>
            @foreach($tutoresDisponibles as $tutor)
            <option value="{{ $tutor->id }}" @selected((int) old('tutor_id', $usuario->tutor?->id ?? $tutorPreseleccionado?->id ?? 0) === $tutor->id)>
                {{ $tutor->nombre_completo }} — {{ $tutor->codigo_empleado }}
            </option>
            @endforeach
        </select>
        @error('tutor_id')
        <p class="form-error">{{ $message }}</p>
        @enderror
        <p class="form-hint">
            Solo aplica para usuarios con rol tutor. El tutor debe estar activo y sin otra cuenta vinculada.
        </p>
    </div>

    <div x-data="{ mostrar: false }">
        <label for="password" class="form-label">
            Contraseña {{ isset($usuario) ? '' : '*' }}
        </label>

        <div class="flex gap-2">
            <input
                :type="mostrar ? 'text' : 'password'"
                name="password"
                id="password"
                value="{{ old('password', '') }}"
                class="input-field"
                minlength="8"
                autocomplete="new-password"
                placeholder="{{ isset($usuario) ? 'Dejar vacío para no cambiar' : 'Ej. Password123' }}"
                @required(! isset($usuario))>

            <button
                type="button"
                class="btn-secondary whitespace-nowrap"
                @click="mostrar = ! mostrar"
                x-text="mostrar ? 'Ocultar' : 'Ver'">
            </button>
        </div>

        @error('password')
        <p class="form-error">{{ $message }}</p>
        @enderror

        <p class="form-hint">
            Para QA/demo puedes usar Password123. En edición, deja este campo vacío si no deseas cambiar la contraseña.
        </p>
    </div>

    <div x-data="{ mostrar: false }">
        <label for="password_confirmation" class="form-label">
            Confirmar contraseña {{ isset($usuario) ? '' : '*' }}
        </label>

        <div class="flex gap-2">
            <input
                :type="mostrar ? 'text' : 'password'"
                name="password_confirmation"
                id="password_confirmation"
                value="{{ old('password_confirmation', '') }}"
                class="input-field"
                minlength="8"
                autocomplete="new-password"
                placeholder="{{ isset($usuario) ? 'Dejar vacío para no cambiar' : 'Ej. Password123' }}"
                @required(! isset($usuario))>

            <button
                type="button"
                class="btn-secondary whitespace-nowrap"
                @click="mostrar = ! mostrar"
                x-text="mostrar ? 'Ocultar' : 'Ver'">
            </button>
        </div>
    </div>

    <div class="flex items-end">
        <label class="inline-flex items-center gap-2">
            <input type="hidden" name="activo" value="0">
            <input
                type="checkbox"
                name="activo"
                value="1"
                class="rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                @checked((bool) old('activo', $usuario->activo ?? true))
            >
            <span class="text-sm font-medium text-utec-gray-dark">Usuario activo</span>
        </label>
    </div>
</div>

<div class="mt-6 flex items-center justify-end gap-3">
    <a href="{{ route('usuarios.index') }}" class="btn-secondary">
        Cancelar
    </a>

    <button type="submit" class="btn-primary">
        {{ $textoBoton ?? 'Guardar' }}
    </button>
</div>