<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">Nuevo ciclo académico</h2>
            <p class="text-sm text-gray-500">Registra un ciclo para gestionar asignaciones y periodos.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('ciclos.store') }}">
                        @include('coordinacion.ciclos._form', [
                        'textoBoton' => 'Crear ciclo'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>