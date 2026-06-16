<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-utec-primary">
                Mi cuenta
            </h2>
            <p class="text-sm text-gray-500">
                Consulta tus datos de acceso y administra tu contraseña.
            </p>
        </div>
    </x-slot>

    <div class="bg-utec-bg-light py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Información de la cuenta
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Estos datos son administrados por Coordinación.
                        </p>
                    </div>
                </div>

                <div class="card-body">
                    <dl class="grid gap-4 md:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Nombre</dt>
                            <dd class="mt-1 rounded-md border border-utec-gray-medium bg-gray-50 px-3 py-2 text-sm font-semibold text-utec-gray-dark">
                                {{ $user->nombre }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Correo</dt>
                            <dd class="mt-1 rounded-md border border-utec-gray-medium bg-gray-50 px-3 py-2 text-sm font-semibold text-utec-gray-dark">
                                {{ $user->correo }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Rol</dt>
                            <dd class="mt-1">
                                @if($user->rol === 'coordinacion')
                                <span class="badge-success">Coordinación</span>
                                @elseif($user->rol === 'tutor')
                                <span class="badge-info">Tutor</span>
                                @else
                                <span class="badge-muted">{{ $user->rol }}</span>
                                @endif
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium text-gray-500">Estado</dt>
                            <dd class="mt-1">
                                @if($user->activo)
                                <span class="badge-success">Activo</span>
                                @else
                                <span class="badge-muted">Inactivo</span>
                                @endif
                            </dd>
                        </div>
                    </dl>

                    <div class="mt-5 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                        @if($user->rol === 'coordinacion')
                        Los datos de cuenta se administran desde el módulo Usuarios. Esta pantalla es solo de consulta y cambio de contraseña.
                        @else
                        Si necesitas corregir tu nombre, correo o rol, solicita el cambio a Coordinación.
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>