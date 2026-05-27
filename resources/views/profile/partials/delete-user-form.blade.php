<section class="space-y-5">

    <p class="text-sm text-gray-500">
        Una vez eliminada tu cuenta, todos los datos serán borrados permanentemente.
        Descarga cualquier información que desees conservar antes de continuar.
    </p>

    <button
        type="button"
        class="inline-flex items-center gap-2 rounded-md border border-red-300 bg-white px-4 py-2 text-sm font-semibold text-red-700 transition hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >
        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
        </svg>
        Eliminar cuenta
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="mb-5 flex items-start gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-red-100 text-red-600">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-utec-gray-dark">
                        ¿Eliminar tu cuenta?
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">
                        Esta acción es permanente e irreversible. Ingresa tu contraseña para confirmar.
                    </p>
                </div>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                    Contraseña <span class="text-red-500 ml-0.5">*</span>
                </label>
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full rounded-lg border border-utec-gray-medium px-3.5 py-2.5 text-sm
                           focus:border-red-400 focus:outline-none focus:ring-2 focus:ring-red-200"
                    placeholder="••••••••"
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1.5 text-xs text-red-600" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <button
                    type="button"
                    class="btn-secondary"
                    x-on:click="$dispatch('close')"
                >
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                >
                    Sí, eliminar cuenta
                </button>
            </div>
        </form>
    </x-modal>

</section>