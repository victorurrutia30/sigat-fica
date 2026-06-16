<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-utec-primary">
            Recuperar contraseña
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
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
                autocomplete="username" />
            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between">
            <a
                href="{{ route('login') }}"
                class="text-sm font-medium text-utec-primary hover:text-utec-primary-dark">
                Volver al inicio de sesión
            </a>

            <x-primary-button>
                Enviar enlace
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>