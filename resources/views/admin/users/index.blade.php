<x-layouts::app :title="__('Gestion de usuarios')">
    <section class="sp-admin-page">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Modulo administrador</p>
            <h1>Gestion de usuarios</h1>
            <p>
                Consulta la informacion principal de los usuarios registrados en la plataforma.
            </p>
        </header>

        @if(session('status'))
            <p role="status" class="rounded-xl border border-[#2CC295]/50 bg-[#006A4C]/20 p-4 text-[#D7FFF2]">{{ session('status') }}</p>
        @endif
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
                            <th>Registro</th><th>Acceso</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            @php
                                $isClient = $user->role === 'client';
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
                                    <span class="sp-role-pill">{{ ['client' => 'Cliente', 'advisor' => 'Asesor', 'admin' => 'Administrador'][$user->role] ?? 'Sin rol' }}</span>
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
                                <td data-label="Acceso">
                                    @if($user->id === auth()->id())
                                        <span class="sp-users-secondary">Tu cuenta: no puedes desactivarla</span>
                                    @else
                                        <a class="inline-block rounded-lg border border-[#2CC295]/60 px-3 py-2 text-[#D7FFF2] focus-visible:outline focus-visible:outline-2" href="{{ route('admin.users.status.edit', ['user' => $user, 'status' => $user->status === 'active' ? 'inactive' : 'active']) }}">
                                            {{ $user->status === 'active' ? 'Desactivar' : 'Reactivar' }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="sp-users-empty">No hay usuarios registrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <section class="sp-users-card mt-6" aria-labelledby="status-history-title">
            <div class="sp-users-card-header"><h2 id="status-history-title">Últimos cambios de acceso</h2><span class="sp-users-count">Hasta 20 registros · hora de Colombia</span></div>
            <div class="p-4 space-y-4">
                @forelse($statusChanges as $change)
                    <article class="rounded-lg border border-[#006A4C]/50 p-4 text-[#D7FFF2]">
                        <p><strong>{{ $change->target }}</strong> ({{ $change->targetEmail }}): {{ $change->before === 'active' ? 'Activo' : 'Inactivo' }} → {{ $change->after === 'active' ? 'Activo' : 'Inactivo' }}</p>
                        <p>{{ $change->actor }} · {{ $change->date }}</p>
                        <p class="whitespace-pre-wrap break-words">Motivo: {{ $change->reason }}</p>
                    </article>
                @empty
                    <p class="sp-users-secondary">Todavía no hay cambios de acceso registrados.</p>
                @endforelse
            </div>
        </section>
    </section>
</x-layouts::app>
