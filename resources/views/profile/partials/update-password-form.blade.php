<section>

<p class="text-sm text-gray-500 mb-5">
    Usa una contraseña larga y aleatoria para mantener tu cuenta segura.
</p>
    <form method="post" action="{{ route('password.update') }}" class="space-y-5">
        @csrf
        @method('put')

        <div>
            <label for="update_password_current_password"
                   class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Contraseña actual <span class="text-red-500 ml-0.5">*</span>
            </label>
            <x-text-input
                id="update_password_current_password"
                name="current_password"
                type="password"
                class="block w-full rounded-lg border border-utec-gray-medium px-3.5 py-2.5 text-sm
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1.5 text-xs text-red-600" />
        </div>

        <div>
            <label for="update_password_password"
                   class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Nueva contraseña <span class="text-red-500 ml-0.5">*</span>
            </label>
            <x-text-input
                id="update_password_password"
                name="password"
                type="password"
                class="block w-full rounded-lg border border-utec-gray-medium px-3.5 py-2.5 text-sm
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1.5 text-xs text-red-600" />
        </div>

        <div>
            <label for="update_password_password_confirmation"
                   class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Confirmar contraseña <span class="text-red-500 ml-0.5">*</span>
            </label>
            <x-text-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                type="password"
                class="block w-full rounded-lg border border-utec-gray-medium px-3.5 py-2.5 text-sm
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1.5 text-xs text-red-600" />
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary">
                Guardar contraseña
            </button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="flex items-center gap-1.5 text-sm text-emerald-600"
                >
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Guardado correctamente
                </p>
            @endif
        </div>
    </form>

</section>