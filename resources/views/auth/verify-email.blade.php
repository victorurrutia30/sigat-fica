<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-bold text-utec-primary">
            Verificación de correo
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Antes de continuar, verifica tu correo mediante el enlace enviado a tu dirección registrada.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
    <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
        Se envió un nuevo enlace de verificación a tu correo.
    </div>
    @endif

    <div class="mt-4 flex items-center justify-between gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button>
                Reenviar verificación
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button
                type="submit"
                class="text-sm font-medium text-utec-primary hover:text-utec-primary-dark">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>