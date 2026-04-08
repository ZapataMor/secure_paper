@php
    $loginUrl = Route::has('login') ? route('login') : '#';
    $servicesUrl = Route::has('services') ? route('services') : url('/servicios');
    $aboutUrl = Route::has('about') ? route('about') : url('/nosotros');
@endphp

<header class="bg-[#000F1F] px-6 py-4 text-[#F1F7F6] shadow-lg">
    <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4">
        <a href="{{ route('home') }}" class="flex items-center gap-3">
            <div class="rounded-lg bg-[#2CC295] p-2">
                <svg class="h-8 w-8 text-[#000F1F]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold tracking-tight">Secure Papers</h1>
                <p class="text-sm text-[#AAC8C4]">Academic Excellence & Trust</p>
            </div>
        </a>

        <nav class="flex flex-wrap items-center gap-3 sm:gap-6">
            <a href="{{ $servicesUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Servicios</a>
            <a href="{{ $aboutUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Nosotros</a>
            <a href="{{ $loginUrl }}" class="rounded-lg bg-[#006A4C] px-6 py-2 font-semibold text-[#F1F7F6] transition-colors hover:bg-[#00BF81]">
                Iniciar Sesion
            </a>
        </nav>
    </div>
</header>
