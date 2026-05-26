<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">Nuevo tutor</h2>
            <p class="text-sm text-gray-500">Registra un Docente de Tiempo Completo para el Programa de Tutores.</p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tutores.store') }}">
                        @include('coordinacion.tutores._form', [
                        'textoBoton' => 'Crear tutor'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>