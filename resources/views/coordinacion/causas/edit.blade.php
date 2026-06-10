<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">
                Editar causa
            </h2>
            <p class="text-sm text-gray-500">
                Actualiza el nombre, descripción o estado de la causa.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('causas.update', $causa) }}">
                        @method('PUT')

                        @include('coordinacion.causas._form', [
                        'textoBoton' => 'Actualizar causa'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>