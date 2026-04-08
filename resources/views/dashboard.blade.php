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
                <h1 class="mb-6 text-5xl font-bold md:text-6xl">
                    Hola, bienvenido a <br class="hidden md:block">
                    <span class="bg-gradient-to-r from-[#2CC295] to-[#00BF81] bg-clip-text text-transparent">
                        Professional Papers
                    </span>
                </h1>
                <p class="mx-auto max-w-2xl text-xl text-[#AAC8C4]">Si te encuentras en el desarrollo de tu proyecto y necesitas apoyo, ideas, revisiones u orientacion, estas en el lugar indicado.</p>
                <p class="mx-auto max-w-2xl text-xl text-[#AAC8C4]">Para comenzar, dirigete al modulo "Planes" y elige la opcion que mejor se adapte a tus necesidades. Luego, podras cargar tu proyecto en el modulo "Cargue de documentos" y empezar a recibir acompanamiento en cada etapa de tu proceso.</p>
            </article>
        @endif
    </section>
</x-layouts::app>
