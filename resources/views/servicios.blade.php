<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Servicios - Professional Papers S.A.S.</title>

        <link rel="icon" href="{{ asset('assets/images/logo.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('assets/images/logo.png') }}">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen overflow-x-hidden bg-[#000F1F] font-sans text-[#F1F7F6] selection:bg-[#2CC295] selection:text-[#000F1F]">
        @php
            $portfolio = config('propaps_plans');
            $plans = $portfolio['plans'] ?? [];
        @endphp

        @include('partials.public.header')

        <section class="sp-home-hero-grid-bg relative z-0 bg-gradient-to-br from-[#000F1F] via-[#032221] to-[#006A4C] px-6 pb-20 pt-32 text-[#F1F7F6] md:pb-24 md:pt-36">
            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <span class="mb-4 block text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Servicios oficiales</span>
                <h2 class="mx-auto mb-6 max-w-full break-words text-3xl font-bold leading-tight md:text-5xl">
                    <span class="block">Professional Papers</span>
                    <span class="block">S.A.S.</span>
                    <span class="block text-3xl md:text-4xl">(PROPAPS S.A.S.)</span>
                </h2>
                <p class="mx-auto max-w-3xl text-lg leading-8 text-[#AAC8C4] md:text-xl">
                    Portafolio orientado a {{ $portfolio['focus'] }} bajo el enfoque de {{ $portfolio['concept'] }}.
                </p>
            </div>
        </section>

        <section class="relative z-10 mx-auto max-w-7xl overflow-hidden px-6 py-16">
            @include('partials.plans.cards', ['plans' => $plans, 'mode' => 'public'])
        </section>

        @include('partials.plans.details', ['plans' => $plans])

        <div class="border-t border-white/5">
            @include('partials.public.footer')
        </div>
    </body>
</html>
