<section>

    <p class="mb-5 text-sm text-gray-500">
        Actualiza la información de tu perfil y correo.
    </p>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
        @csrf
        @method('patch')

        <div>
            <label for="nombre" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Nombre <span class="text-red-500 ml-0.5">*</span>
            </label>
            <x-text-input
                id="nombre"
                name="nombre"
                type="text"
                class="block w-full rounded-lg border border-utec-gray-medium px-3.5 py-2.5 text-sm
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                :value="old('nombre', $user->nombre)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-1.5 text-xs text-red-600" :messages="$errors->get('nombre')" />
        </div>

        <div>
            <label for="correo" class="block text-sm font-medium text-utec-gray-dark mb-1.5">
                Correo institucional <span class="text-red-500 ml-0.5">*</span>
            </label>
            <x-text-input
                id="correo"
                name="correo"
                type="email"
                class="block w-full rounded-lg border border-utec-gray-medium px-3.5 py-2.5 text-sm
                       focus:border-utec-primary-light focus:outline-none focus:ring-2 focus:ring-utec-primary-light/30"
                :value="old('correo', $user->correo)"
                required
                autocomplete="username"
            />
            <x-input-error class="mt-1.5 text-xs text-red-600" :messages="$errors->get('correo')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2 rounded-lg border border-orange-200 bg-orange-50 px-4 py-3">
                    <p class="text-sm text-orange-700">
                        Tu correo no está verificado.
                        <button form="send-verification"
                                class="font-medium underline hover:text-orange-900 focus:outline-none">
                            Reenviar verificación
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-1 text-sm font-medium text-green-600">
                            Se envió un nuevo enlace de verificación a tu correo.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="btn-primary">
                Guardar cambios
            </button>

            @if (session('status') === 'profile-updated')
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