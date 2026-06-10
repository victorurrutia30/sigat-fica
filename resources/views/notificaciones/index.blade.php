<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Notificaciones
                </h2>
                <p class="text-sm text-gray-500">
                    Recordatorios internos sobre consolidado y cumplimiento.
                </p>
            </div>

            @if($totalNoLeidas > 0)
            <form method="POST" action="{{ route('notificaciones.marcar-todas-leidas') }}">
                @csrf
                @method('PATCH')

                <button type="submit" class="btn-secondary">
                    Marcar todas como leídas
                </button>
            </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            <div class="mb-4 grid gap-4 md:grid-cols-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-sm text-gray-500">Total</p>
                        <p class="mt-1 text-2xl font-bold text-utec-gray-dark">
                            {{ $notificaciones->total() }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm text-gray-500">No leídas</p>
                        <p class="mt-1 text-2xl font-bold text-utec-primary">
                            {{ $totalNoLeidas }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-sm text-gray-500">Estado</p>

                        @if($totalNoLeidas > 0)
                        <span class="badge-warning mt-2">Requiere revisión</span>
                        @else
                        <span class="badge-success mt-2">Sin pendientes</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Listado de notificaciones
                    </h3>
                </div>

                <div class="card-body">
                    <div class="space-y-3">
                        @forelse($notificaciones as $notificacion)
                        <div class="rounded-lg border px-4 py-3 {{ $notificacion->leido ? 'border-gray-200 bg-white' : 'border-orange-200 bg-orange-50' }}">
                            <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        @if($notificacion->leido)
                                        <span class="badge-muted">Leída</span>
                                        @else
                                        <span class="badge-warning">No leída</span>
                                        @endif

                                        @if(str_contains($notificacion->tipo, 'atrasado'))
                                        <span class="badge-danger">Atraso</span>
                                        @elseif(str_contains($notificacion->tipo, 'recordatorio'))
                                        <span class="badge-info">Recordatorio</span>
                                        @else
                                        <span class="badge-muted">Sistema</span>
                                        @endif
                                    </div>

                                    <h4 class="mt-2 text-sm font-semibold text-utec-gray-dark">
                                        {{ $notificacion->titulo }}
                                    </h4>

                                    <p class="mt-1 text-sm text-gray-600">
                                        {{ $notificacion->mensaje }}
                                    </p>

                                    <p class="mt-2 text-xs text-gray-500">
                                        Generada el {{ $notificacion->created_at?->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="flex flex-col gap-2 md:items-end">
                                    @if($notificacion->modelo_tipo === \App\Models\Consolidado::class)
                                    @if(auth()->user()?->rol === 'coordinacion')
                                    <a href="{{ route('consolidados.show', $notificacion->modelo_id) }}" class="link-utec">
                                        Ver consolidado
                                    </a>
                                    @elseif(auth()->user()?->rol === 'tutor')
                                    <a href="{{ route('consolidado.index') }}" class="link-utec">
                                        Ver mi consolidado
                                    </a>
                                    @endif
                                    @endif

                                    @if(! $notificacion->leido)
                                    <form method="POST" action="{{ route('notificaciones.marcar-leida', $notificacion) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="btn-secondary">
                                            Marcar como leída
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center">
                            <p class="text-sm text-gray-500">
                                No hay notificaciones para mostrar.
                            </p>
                        </div>
                        @endforelse
                    </div>

                    <div class="mt-5">
                        {{ $notificaciones->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>