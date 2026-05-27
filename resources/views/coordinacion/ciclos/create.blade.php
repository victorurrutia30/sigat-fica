<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="mt-0.5 text-xl font-bold text-utec-gray-dark">Nuevo ciclo académico</h2>
                <p class="text-sm text-gray-400">Registra un ciclo para gestionar asignaciones y periodos.</p>
            </div>
            <a href="{{ route('ciclos.index') }}" class="btn-secondary flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
                Volver
            </a>
        </div>
    </x-slot>

    <div class="min-h-screen bg-utec-bg-light py-8">
        <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-utec-primary-soft text-utec-primary">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-utec-gray-dark">Datos del ciclo</p>
                            <p class="text-xs text-gray-400">Todos los campos marcados con * son obligatorios</p>
                        </div>
                    </div>
                </div>
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