<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-utec-primary">
            Establecer contraseña
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Define tu contraseña personal para ingresar a SIGAT-FICA.
        </p>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="correo" value="Correo" />
            <x-text-input
                id="correo"
                class="mt-1 block w-full bg-gray-100 text-gray-700"
                type="email"
                name="correo"
                :value="old('correo', $request->query('correo'))"
                required
                readonly
                autocomplete="username" />
            <x-input-error :messages="$errors->get('correo')" class="mt-2" />

            <p class="mt-2 text-xs text-gray-500">
                Este correo proviene del enlace de invitación enviado por SIGAT-FICA.
            </p>
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Nueva contraseña" />
            <x-text-input
                id="password"
                class="mt-1 block w-full"
                type="password"
                name="password"
                required
                autofocus
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />

            <x-text-input
                id="password_confirmation"
                class="mt-1 block w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-end">
            <x-primary-button>
                Guardar contraseña
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>