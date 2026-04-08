<x-layouts::app :title="__('Inicio')">
    <section class="sp-admin-page">
        @if(auth()->user()?->isAdmin())
            <header class="sp-admin-page-header">
                <p class="sp-admin-page-kicker">Panel principal</p>
                <h1>Modulos del sistema</h1>
                <p>Selecciona el modulo que deseas gestionar desde la barra superior o desde las tarjetas.</p>
            </header>

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
            <article class="sp-user-home-card animate__animated animate__fadeInDownBig">
                <h1>Hola, bienvenido a Professional Papers.</h1>
                <p>Si te encuentras en el desarrollo de tu proyecto y necesitas apoyo, ideas, revisiones u orientación, estás en el lugar indicado.</p>
                <p>Para comenzar, dirígete al módulo "Planes" y elige la opción que mejor se adapte a tus necesidades. Luego, podrás cargar tu proyecto en el módulo "Cargue de documentos" y empezar a recibir acompañamiento en cada etapa de tu proceso.</p>
            </article>
        @endif
    </section>
</x-layouts::app>
