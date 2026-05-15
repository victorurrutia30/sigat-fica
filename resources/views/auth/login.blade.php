<x-guest-layout>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-5">
        <p class="mb-4 text-[10px] font-semibold uppercase tracking-widest text-utec-gray-medium">
            Acceso al sistema
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <label for="correo" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Correo institucional
            </label>
            <input
                id="correo"
                type="email"
                name="correo"
                value="{{ old('correo') }}"
                placeholder="correo@utec.edu.sv"
                required
                autofocus
                autocomplete="username"
                class="block w-full rounded-lg border border-utec-gray-medium bg-white px-3.5 py-2.5
                       text-sm text-utec-gray-dark placeholder-gray-400
                       transition-colors
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30
                       @error('correo') border-red-400 focus:border-red-400 focus:ring-red-200 @enderror"
            />
            @error('correo')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Contraseña
            </label>
            <input
                id="password"
                type="password"
                name="password"
                placeholder="••••••••"
                required
                autocomplete="current-password"
                class="block w-full rounded-lg border border-utec-gray-medium bg-white px-3.5 py-2.5
                       text-sm text-utec-gray-dark placeholder-gray-400
                       transition-colors
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30
                       @error('password') border-red-400 focus:border-red-400 focus:ring-red-200 @enderror"
            />
            @error('password')
                <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mt-4 flex items-center gap-2">
            <input
                id="remember_me"
                type="checkbox"
                name="remember"
                class="h-4 w-4 rounded border-utec-gray-medium text-utec-primary
                       focus:ring-utec-primary-light focus:ring-offset-0 cursor-pointer"
            />
            <label for="remember_me" class="text-xs text-utec-gray-medium cursor-pointer select-none">
                Recordarme
            </label>
        </div>

        <div class="mt-6">
            <button type="submit" class="btn-primary w-full py-2.5 text-sm">
                Iniciar sesión
            </button>
        </div>

        @if (Route::has('password.request'))
            <p class="mt-5 text-center text-[12px] text-utec-gray-medium">
                <a href="{{ route('password.request') }}" class="link-utec text-[12px]">
                     ¿Olvidaste tu contraseña?
                </a>
            </p>
        @endif

    </form>

</x-guest-layout>
