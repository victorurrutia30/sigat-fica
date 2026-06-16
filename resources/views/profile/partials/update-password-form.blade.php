<section>
    <header>
        <h2 class="text-lg font-semibold text-utec-gray-dark">
            Cambiar contraseña
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Usa una contraseña segura y no la compartas con otras personas.
        </p>
    </header>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Contraseña actual" />
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nueva contraseña" />
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            <p class="mt-2 text-xs text-gray-500">
                Debe tener al menos 8 caracteres.
            </p>
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar nueva contraseña" />
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                Guardar contraseña
            </x-primary-button>

            @if (session('status') === 'password-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2500)"
                class="text-sm font-medium text-green-700">
                Contraseña actualizada correctamente.
            </p>
            @endif
        </div>
    </form>
</section>