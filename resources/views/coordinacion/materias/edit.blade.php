<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Editar materia</h2>
                <p class="text-sm text-gray-400">Actualiza los datos de la materia seleccionada.</p>
            </div>
            <a href="{{ route('materias.index') }}" class="btn-secondary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-utec-gray-dark">Datos de la materia</p>
                            <p class="text-xs text-gray-400">Todos los campos marcados con * son obligatorios</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('materias.update', $materia) }}">
                        @csrf
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