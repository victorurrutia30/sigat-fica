<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">Nuevo usuario</h2>
            <p class="text-sm text-gray-500">
                Crea una cuenta de acceso para Coordinación o Tutor.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            @if(session('error'))
            <div class="mb-4 rounded-md border-l-4 border-red-600 bg-red-50 p-4 text-sm text-red-800">
                {{ session('error') }}
            </div>
            @endif

            <div class="card mb-5">
                <div class="card-header">
                    <div>
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Crear desde tutor habilitado
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Selecciona un tutor activo, habilitado y sin cuenta vinculada para rellenar nombre, correo, rol y vínculo.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    @if($tutoresDisponibles->isEmpty())
                    <p class="text-sm text-gray-500">
                        No hay tutores habilitados sin cuenta vinculada disponibles.
                    </p>
                    @else
                    <form method="GET" action="{{ route('usuarios.create') }}" class="grid gap-4 md:grid-cols-3 md:items-end">
                        <div class="md:col-span-2">
                            <label for="tutor_id_preseleccion" class="form-label">
                                Tutor habilitado
                            </label>
                            <select name="tutor_id" id="tutor_id_preseleccion" class="input-field">
                                <option value="">Seleccione...</option>
                                @foreach($tutoresDisponibles as $tutorDisponible)
                                <option value="{{ $tutorDisponible->id }}" @selected((int) request('tutor_id')===$tutorDisponible->id)>
                                    {{ $tutorDisponible->nombre_completo }} — {{ $tutorDisponible->codigo_empleado }} — {{ $tutorDisponible->correo_institucional }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <button type="submit" class="btn-secondary">
                                Usar datos del tutor
                            </button>
                        </div>
                    </form>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('usuarios.store') }}">
                        @include('coordinacion.usuarios._form', [
                        'textoBoton' => 'Crear usuario y enviar invitación',
                        ])
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>