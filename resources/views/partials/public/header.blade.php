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
            <img src="{{ asset('assets/images/logo.png') }}" alt="Propaps.com" class="h-12 w-12 flex-none rounded-lg object-contain">
            <div class="min-w-0">
                <h1 class="truncate text-2xl font-bold tracking-tight sm:text-3xl">propaps<span class="block text-[#2CC295] sm:inline">.com</span></h1>
            </div>
        </div>

        <nav class="flex w-full flex-wrap items-center justify-center gap-3 text-base sm:w-auto sm:justify-end sm:gap-6 sm:text-lg">
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
