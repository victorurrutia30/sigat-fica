<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">
                Nuevo periodo de evaluación
            </h2>
            <p class="text-sm text-gray-500">
                Registra un periodo para seguimiento de estudiantes no evaluados y entrega de consolidados.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('info'))
            <div class="alert-info mb-4">
                {{ session('info') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="alert-warning mb-4">
                {{ session('warning') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="alert-error mb-4">
                Revisa los campos marcados antes de continuar.
            </div>
            @endif

            <div class="card">

                <div class="card-body">
                    <form method="POST" action="{{ route('periodos.store') }}">
                        @include('coordinacion.periodos._form', [
                        'textoBoton' => 'Crear periodo'
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>