<x-layouts::app :title="__('Planes')">
    @php
        $portfolio = config('propaps_plans');
        $plans = $portfolio['plans'] ?? [];
        $plansByName = $plansByName instanceof \Illuminate\Support\Collection ? $plansByName : collect($plansByName ?? []);
        $hasActiveMembership = $hasActiveMembership ?? (auth()->user()?->hasActiveMembership() ?? false);
        $activePlanName = $activePlanName ?? auth()->user()?->activeMembershipPlanName();
        $pendingSubscription = $pendingSubscription ?? null;
    @endphp

    @if (session('status'))
        <div class="mx-auto mt-6 w-full max-w-7xl rounded-xl border border-[#2CC295]/50 bg-[#2CC295]/15 px-5 py-4 text-sm text-[#D7FFF2]">
            {{ session('status') }}
        </div>
    @endif

    @if (session('membership_error'))
        <div class="mx-auto mt-6 w-full max-w-7xl rounded-xl border border-[#F9BC60]/45 bg-[#F9BC60]/10 px-5 py-4 text-sm text-[#FFE3B8]">
            {{ session('membership_error') }}
        </div>
    @endif

    @if ($pendingSubscription)
        <div class="mx-auto mt-6 w-full max-w-7xl rounded-xl border border-[#F9BC60]/45 bg-[#F9BC60]/10 px-5 py-4 text-sm text-[#FFE3B8]">
            Tienes una solicitud pendiente para el plan {{ $pendingSubscription->paymentPlan?->name ?? 'seleccionado' }}. El modulo de carga se habilitara cuando el pago quede aprobado.
        </div>
    @elseif ($hasActiveMembership)
        <div class="mx-auto mt-6 w-full max-w-7xl rounded-xl border border-[#2CC295]/50 bg-[#2CC295]/15 px-5 py-4 text-sm text-[#D7FFF2]">
            Tu plan activo es {{ $activePlanName ?? 'una membresia vigente' }}. Ya puedes cargar documentos.
        </div>
    @endif

    <section class="relative overflow-hidden px-6 pb-16 pt-20">
        <div class="pointer-events-none absolute left-1/2 top-0 h-[400px] w-[800px] -translate-x-1/2 rounded-full bg-[#006A4C] opacity-20 blur-[120px]"></div>

        <div class="relative z-10 mx-auto max-w-7xl text-center sp-private-plans-hero animate__animated animate__fadeIn" style="--animate-duration: 2s;">
                <span class="mb-4 block text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Portafolio de {{ $portfolio['company'] }}</span>
            <h1 class="mb-6 text-4xl font-bold leading-tight sm:text-5xl md:text-6xl">
                Servicios para <br class="hidden md:block">
                <span class="bg-gradient-to-r from-[#2CC295] to-[#00BF81] bg-clip-text text-transparent">
                    Ciencias Sociales y Educación
                </span>
            </h1>
            <p class="mx-auto max-w-3xl text-xl leading-8 text-[#AAC8C4]">
                {{ $portfolio['concept'] }}. Elige el servicio que mejor se adapte a la etapa de tu investigación.
            </p>
        </div>
    </section>

    <section class="relative z-10 mx-auto max-w-7xl px-6 pb-24">
        @include('partials.plans.cards', [
            'plans' => $plans,
            'mode' => 'private',
            'plansByName' => $plansByName,
            'hasActiveMembership' => $hasActiveMembership,
        ])
    </section>

</x-layouts::app>
