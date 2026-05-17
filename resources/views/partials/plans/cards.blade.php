@php
    $plans = $plans ?? config('propaps_plans.plans', []);
    $mode = $mode ?? 'public';
    $loginUrl = Route::has('login') ? route('login') : '#';
    $rawPlansByName = $plansByName ?? collect();
    $plansByName = $rawPlansByName instanceof \Illuminate\Support\Collection ? $rawPlansByName : collect($rawPlansByName);
    $hasActiveMembership = $hasActiveMembership ?? false;
@endphp

<div class="grid w-full max-w-full grid-cols-1 gap-8 overflow-hidden">
    @foreach ($plans as $index => $plan)
        @php
            $isFeatured = $plan['featured'] ?? false;
            $paymentPlan = $plansByName->get($plan['db_name'] ?? $plan['name']);
            $buttonClass = $isFeatured
                ? 'bg-[#2CC295] text-[#000F1F] shadow-[0_0_20px_rgba(44,194,149,0.3)] hover:bg-[#00BF81]'
                : 'bg-white/10 text-white hover:bg-white/20';
            $cardClass = $isFeatured
                ? 'border-2 border-[#2CC295] bg-gradient-to-r from-[#006A4C]/30 via-[#031A19]/90 to-[#031A19]/80 shadow-[0_0_40px_rgba(44,194,149,0.15)]'
                : 'border border-[#006A4C]/30 bg-[#031A19]/60 hover:border-[#006A4C]/60';
        @endphp

        <article id="{{ $plan['id'] }}" class="sp-private-plan-card-center animate__animated animate__fadeInUp overflow-hidden rounded-3xl backdrop-blur-md transition-colors {{ $cardClass }}">
            <div class="grid gap-6 p-6 md:grid-cols-[minmax(0,1.05fr)_minmax(18rem,0.8fr)] md:p-8">
                <div class="min-w-0">
                    <div class="mb-5 flex flex-wrap items-center gap-3">
                        <span class="rounded-full {{ $isFeatured ? 'bg-[#2CC295] text-[#000F1F]' : 'bg-[#2CC295]/20 text-[#2CC295]' }} px-3 py-1 text-xs font-bold uppercase tracking-wide">
                            {{ $plan['tag'] }}
                        </span>
                        <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#F1F7F6]">
                            {{ $plan['badge'] }}
                        </span>
                    </div>

                    <h3 class="mb-4 text-2xl font-bold leading-tight md:text-3xl">{{ $plan['name'] }}</h3>
                    <p class="max-w-4xl text-base leading-7 text-[#AAC8C4] md:text-lg md:leading-8">{{ $plan['short_description'] }}</p>

                    <ul class="mt-6 grid gap-3 text-sm leading-6 text-[#F1F7F6] sm:grid-cols-2">
                        @foreach (array_slice($plan['benefits'], 0, 6) as $benefit)
                            <li class="flex gap-3">
                                <span class="mt-0.5 text-[#2CC295]">&#10003;</span>
                                <span>{{ $benefit }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="flex min-w-0 flex-col rounded-2xl border border-white/10 bg-black/10 p-5">
                    <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Precio teaser</p>
                    <p class="mt-3 break-words text-3xl font-bold leading-tight text-[#F1F7F6] md:text-4xl">{{ $plan['headline_price'] }}</p>
                    <p class="mt-3 text-sm leading-6 text-[#AAC8C4]">El precio exacto se define segun linea, nivel academico, extension y plazo requerido.</p>

                    <div class="mt-auto pt-6">
                        @if ($mode === 'private')
                            @if ($hasActiveMembership)
                                <a href="{{ route('private.upload-document') }}" class="block w-full rounded-xl py-3 text-center font-bold transition-colors {{ $buttonClass }}" wire:navigate>
                                    Ir a cargar
                                </a>
                            @elseif ($paymentPlan)
                                <form method="POST" action="{{ route('private.planes.select', $paymentPlan) }}">
                                    @csrf
                                    <button type="submit" class="block w-full rounded-xl py-3 text-center font-bold transition-colors {{ $buttonClass }}">
                                        Solicitar plan
                                    </button>
                                </form>
                            @else
                                <button type="button" disabled class="block w-full cursor-not-allowed rounded-xl bg-white/10 py-3 text-center font-semibold text-white/55">
                                    No disponible
                                </button>
                            @endif
                        @else
                            <a href="{{ $loginUrl }}" class="block w-full rounded-xl py-3 text-center font-bold transition-colors {{ $buttonClass }}">
                                Empezar ahora
                            </a>
                        @endif

                    </div>
                </div>
            </div>

            <details class="group border-t border-white/10">
                <summary class="flex cursor-pointer list-none items-center justify-center gap-2 px-6 py-4 text-sm font-bold text-[#2CC295] transition-colors hover:text-[#00BF81] md:px-8">
                    <span>Ver detalle</span>
                    <span class="transition-transform group-open:rotate-180" aria-hidden="true">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="m6 9 6 6 6-6" />
                        </svg>
                    </span>
                </summary>

                <div class="grid gap-5 px-6 pb-6 md:grid-cols-2 md:px-8 md:pb-8">
                    @foreach ($plan['lines'] as $line)
                        <section class="rounded-2xl border border-white/10 bg-black/10 p-5">
                            <h4 class="text-xl font-bold text-[#2CC295]">{{ $line['name'] }}</h4>
                            <p class="mt-3 text-sm leading-6 text-[#F1F7F6]">{{ $line['intro'] }}</p>

                            @if (! empty($line['deadlines']))
                                <div class="mt-5">
                                    <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Plazos de entrega</p>
                                    <ul class="mt-3 space-y-2 text-sm leading-6 text-[#F1F7F6]">
                                        @foreach ($line['deadlines'] as $deadline)
                                            <li class="flex gap-3">
                                                <span class="mt-0.5 text-[#2CC295]">&#10003;</span>
                                                <span>{{ $deadline }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="mt-5">
                                <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Incluye</p>
                                <ol class="mt-3 list-decimal space-y-2 pl-5 text-sm leading-6 text-[#F1F7F6]">
                                    @foreach ($line['includes'] as $item)
                                        <li>{{ $item }}</li>
                                    @endforeach
                                </ol>
                            </div>

                            <div class="mt-5 rounded-xl border border-[#2CC295]/20 bg-[#2CC295]/10 p-4">
                                <p class="text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Gran ventaja</p>
                                <p class="mt-2 text-sm leading-6 text-[#F1F7F6]">{{ $line['advantage'] }}</p>
                            </div>

                            <div class="mt-5">
                                <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Costo</p>
                                <ul class="mt-3 space-y-2 text-sm leading-6 text-[#F1F7F6]">
                                    @foreach ($line['costs'] as $cost)
                                        <li class="flex gap-3">
                                            <span class="mt-0.5 text-[#2CC295]">$</span>
                                            <span>{{ $cost }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            @if (! empty($line['note']))
                                <p class="mt-5 rounded-xl border border-white/10 bg-white/5 p-4 text-xs leading-5 text-[#AAC8C4]">
                                    <strong class="text-[#F1F7F6]">Nota importante:</strong> {{ $line['note'] }}
                                </p>
                            @endif
                        </section>
                    @endforeach
                </div>
            </details>
        </article>
    @endforeach
</div>
