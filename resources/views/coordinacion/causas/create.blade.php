<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">
                Nueva causa
            </h2>
            <p class="text-sm text-gray-500">
                Registra una causa para clasificar casos de estudiantes no evaluados.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('causas.store') }}">
                        @include('coordinacion.causas._form', [
                        'textoBoton' => 'Crear causa'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>