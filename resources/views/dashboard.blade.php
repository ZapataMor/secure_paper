<x-layouts::app :title="__('Panel de administracion')">
    <section class="sp-admin-page">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Panel principal</p>
            <h1>Modulos del sistema</h1>
            <p>Selecciona el modulo que deseas gestionar desde la barra superior o desde las tarjetas.</p>
        </header>

        @if(auth()->user()?->isAdmin())
            <div class="sp-admin-modules-grid">
                <article class="sp-admin-module-card">
                    <span class="sp-admin-module-badge">Modulo 01</span>
                    <h2>Gestion de usuarios</h2>
                    <p>Administra la informacion de los usuarios registrados en Secure Papers.</p>
                    <a href="{{ route('admin.users.index') }}" class="sp-admin-module-action" wire:navigate>
                        Abrir modulo
                    </a>
                </article>
            </div>
        @else
            <div class="sp-admin-empty-state">
                <h2>Sin modulos disponibles</h2>
                <p>Tu cuenta no tiene permisos de administrador para visualizar modulos.</p>
            </div>
        @endif
    </section>
</x-layouts::app>
