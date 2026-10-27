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
        @include('partials.public.header')
        @include('partials.plans.portfolio-public')

        <div class="border-t border-white/5">
            @include('partials.public.footer')
        </div>
    </body>
</html>
