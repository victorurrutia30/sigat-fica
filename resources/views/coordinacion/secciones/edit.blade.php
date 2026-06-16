<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Editar sección {{ $seccion->numero_seccion }}
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $seccion->materia?->codigo }} — {{ $seccion->materia?->nombre }}
                </p>
            </div>

            <a href="{{ route('materias.secciones.index', $seccion->materia) }}" class="btn-secondary">
                Volver a secciones
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            @if($errors->any())
            <div class="mb-4 rounded-md border-l-4 border-red-600 bg-red-50 p-4 text-sm text-red-800">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('secciones.update', $seccion) }}">
                @method('PUT')

                @include('coordinacion.secciones._form')
            </form>
        </div>
    </div>
</x-app-layout>