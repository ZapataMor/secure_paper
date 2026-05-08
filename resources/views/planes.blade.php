<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Planes - Professional Papers S.A.S.</title>

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

        <section class="relative overflow-hidden px-6 pb-16 pt-37 md:pt-36">
            <div class="pointer-events-none absolute left-1/2 top-0 h-[400px] w-[800px] -translate-x-1/2 rounded-full bg-[#006A4C] opacity-20 blur-[120px]"></div>

            <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 3s;">
                <span class="mb-4 block text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Portafolio oficial de servicios</span>
                <h1 class="mx-auto mb-6 max-w-full break-words text-3xl font-bold leading-tight sm:text-5xl md:text-6xl">
                    <span class="block">Professional Papers</span>
                    <span class="block">S.A.S.</span>
                    <span class="block text-3xl sm:text-4xl md:text-5xl">(PROPAPS S.A.S.)</span>
                    <span class="block bg-gradient-to-r from-[#2CC295] to-[#00BF81] bg-clip-text text-transparent">
                        Inteligencia Híbrida
                    </span>
                </h1>
                <p class="mx-auto max-w-xs text-base leading-8 text-[#AAC8C4] sm:max-w-3xl md:text-xl">
                    {{ $portfolio['concept'] }}. Servicios especializados para {{ $portfolio['focus'] }}, con rigor académico, confidencialidad y cumplimiento fiscal colombiano.
                </p>
            </div>
        </section>

        <section class="relative z-10 mx-auto max-w-7xl overflow-hidden px-6 pb-24">
            @include('partials.plans.cards', ['plans' => $plans, 'mode' => 'public'])
        </section>

        @include('partials.plans.details', ['plans' => $plans])

        <div class="border-t border-white/5">
            @include('partials.public.footer')
        </div>
    </body>
</html>
