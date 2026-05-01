<x-layouts::app :title="__('Gestion de usuarios')">
    <section class="sp-admin-page">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Modulo administrador</p>
            <h1>Gestion de usuarios</h1>
            <p>
                Consulta la informacion principal de los usuarios registrados en la plataforma.
            </p>
        </header>

        <div class="sp-users-card">
            <div class="sp-users-card-header">
                <h2>Listado general</h2>
                <span class="sp-users-count">{{ $users->count() }} usuarios</span>
            </div>

            <div class="sp-users-table-wrap">
                <table class="sp-users-table">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Plan</th>
                            <th>Documento</th>
                            <th>Estado</th>
                            <th>Registro</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $isClient = $user->role?->name === 'client';
                                $planName = $isClient ? $user->activePaidSubscription?->paymentPlan?->name : null;
                            @endphp

                            <tr>
                                <td data-label="Nombre">
                                    <p class="sp-users-name">{{ $user->fullName() }}</p>
                                    @if($user->phone)
                                        <p class="sp-users-secondary">{{ $user->phone }}</p>
                                    @endif
                                </td>
                                <td data-label="Correo">
                                    <p class="sp-users-secondary">{{ $user->email }}</p>
                                </td>
                                <td data-label="Rol">
                                    <span class="sp-role-pill">{{ ucfirst($user->role?->name ?? 'sin rol') }}</span>
                                </td>
                                <td data-label="Plan">
                                    @if(! $isClient)
                                        <span class="sp-role-pill">No aplica</span>
                                    @elseif($planName)
                                        <span class="sp-role-pill">{{ $planName }}</span>
                                    @else
                                        <span class="sp-status-pill is-inactive">Sin plan</span>
                                    @endif
                                </td>
                                <td data-label="Documento">
                                    @if($user->document_type && $user->document_number)
                                        <p class="sp-users-secondary">{{ $user->document_type }} - {{ $user->document_number }}</p>
                                    @else
                                        <p class="sp-users-secondary">Sin documento</p>
                                    @endif
                                </td>
                                <td data-label="Estado">
                                    <span class="sp-status-pill {{ $user->status === 'active' ? 'is-active' : 'is-inactive' }}">
                                        {{ $user->status === 'active' ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td data-label="Registro">
                                    <p class="sp-users-secondary">{{ $user->created_at->format('d/m/Y') }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="sp-users-empty">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-layouts::app>
