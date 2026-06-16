<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">Editar materia</h2>
                <p class="text-sm text-gray-500">
                    Actualiza los datos de la materia seleccionada.
                </p>
            </div>

            <a href="{{ route('materias.secciones.index', $materia) }}" class="btn-secondary">
                Ver secciones ({{ $materia->secciones_count }})
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('materias.update', $materia) }}">
                        @method('PUT')

                        @include('coordinacion.materias._form', [
                        'textoBoton' => 'Actualizar materia'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>