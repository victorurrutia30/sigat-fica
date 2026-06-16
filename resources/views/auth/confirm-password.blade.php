<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-utec-primary">
            Confirmar contraseña
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Esta es un área segura. Confirma tu contraseña antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Contraseña" />

            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-6 flex justify-end">
            <x-primary-button>
                Confirmar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>