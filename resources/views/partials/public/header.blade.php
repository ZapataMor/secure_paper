@php
    $homeUrl = Route::has('home') ? route('home') : url('/');
    $loginUrl = Route::has('login') ? route('login') : '#';
    $servicesUrl = Route::has('services') ? route('services') : url('/servicios');
    $aboutUrl = Route::has('about') ? route('about') : url('/nosotros');
    $plansUrl = Route::has('plans') ? route('plans') : url('/planes');
    $publicModuleUrls = json_encode([$homeUrl, $servicesUrl, $aboutUrl, $plansUrl]);
@endphp

<header class="sp-public-navbar" data-public-module-urls="{{ $publicModuleUrls }}">
    <div class="sp-public-navbar-inner mx-auto flex max-w-7xl flex-wrap items-center justify-between gap-4 overflow-hidden text-[#F1F7F6]">
        <div class="flex min-w-0 items-center gap-3">
            <div class="rounded-lg bg-[#2CC295] p-2">
                <svg class="h-8 w-8 text-[#000F1F]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2 4 6v6c0 5 3.2 9.4 8 10 4.8-.6 8-5 8-10V6z" />
                    <path d="m9 12 2 2 4-4" />
                </svg>
            </div>
            <div class="min-w-0">
                <h1 class="truncate text-xl font-bold tracking-tight sm:text-2xl">Professional Papers</h1>
                <p class="text-sm text-[#AAC8C4]">Academic Excellence & Trust</p>
            </div>
        </div>

        <nav class="flex w-full flex-wrap items-center justify-center gap-3 text-sm sm:w-auto sm:justify-end sm:gap-6 sm:text-base">
            <a href="{{ route('home') }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Inicio</a>
            <a href="{{ $plansUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Planes</a>
            {{-- <a href="{{ $servicesUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Servicios</a> --}}
            <a href="{{ $aboutUrl }}" class="font-medium text-[#F1F7F6] transition-colors hover:text-[#2CC295]">Nosotros</a>
            <a href="{{ $loginUrl }}" class="whitespace-nowrap rounded-lg bg-[#006A4C] px-4 py-2 font-semibold text-[#F1F7F6] transition-colors hover:bg-[#00BF81] sm:px-6">
                Iniciar Sesion
            </a>
        </nav>
    </div>
</header>

@vite('resources/js/partials/public/header.js')
