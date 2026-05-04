<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="correo" value="Correo" />
            <x-text-input id="correo" class="block mt-1 w-full" type="email" name="correo" :value="old('correo')" required autofocus />
            <x-input-error :messages="$errors->get('correo')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                Enviar enlace de recuperación
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>