<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="sp-auth-page">
        <a href="{{ route('home', ['sp_navbar_anim' => 1]) }}" class="sp-auth-back-link" wire:navigate>
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M14.5 5.5L8 12l6.5 6.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" />
            </svg>
            <span>Volver al inicio</span>
        </a>

        <main class="sp-auth-main">
            {{ $slot }}
        </main>

        @vite('resources/js/layouts/auth/secure-papers.js')
        @fluxScripts
    </body>
</html>
