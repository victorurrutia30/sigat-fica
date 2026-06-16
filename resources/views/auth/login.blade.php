<x-guest-layout>
    <div class="mb-6">
        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-utec-primary">
            Acceso seguro
        </p>

        <h1 class="mt-2 text-2xl font-bold text-utec-gray-dark">
            Iniciar sesión
        </h1>

        <p class="mt-2 text-sm leading-6 text-gray-600">
            Ingresa con tu correo institucional para acceder a SIGAT-FICA.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <x-input-label for="correo" value="Correo" />
            <x-text-input
                id="correo"
                class="mt-1 block w-full"
                type="email"
                name="correo"
                :value="old('correo')"
                required
                autofocus
                autocomplete="username"
                placeholder="usuario@utec.edu.sv" />
            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Contraseña" />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Ingresa tu contraseña" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="rounded border-utec-gray-medium text-utec-primary shadow-sm focus:ring-utec-primary-light"
                    name="remember">

                <span class="ms-2 text-sm text-gray-600">
                    Recordarme
                </span>
            </label>

            @if (Route::has('password.request'))
            <a
                class="text-sm font-medium text-utec-primary hover:text-utec-primary-dark"
                href="{{ route('password.request') }}">
                ¿Olvidaste tu contraseña?
            </a>
            @endif
        </div>

        <button type="submit" class="btn-primary w-full justify-center">
            Ingresar al sistema
        </button>
    </form>

    <div class="mt-6 rounded-lg border border-utec-gray-medium bg-gray-50 px-4 py-3 text-xs leading-5 text-gray-600">
        Si no tienes acceso, solicita a Coordinación que cree o active tu cuenta.
    </div>
</x-guest-layout>