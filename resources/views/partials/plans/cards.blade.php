@php
    $plans = $plans ?? config('propaps_plans.plans', []);
    $mode = $mode ?? 'public';
    $loginUrl = Route::has('login') ? route('login') : '#';
    $rawPlansByName = $plansByName ?? collect();
    $plansByName = $rawPlansByName instanceof \Illuminate\Support\Collection ? $rawPlansByName : collect($rawPlansByName);
    $hasActiveMembership = $hasActiveMembership ?? false;
    $animationClasses = [
        'sp-private-plan-card-left animate__animated animate__fadeInLeft',
        'sp-private-plan-card-center animate__animated animate__fadeInUp',
        'sp-private-plan-card-right animate__animated animate__fadeInRight',
    ];
@endphp

<div class="flex w-full max-w-full flex-wrap items-stretch justify-center gap-8 overflow-hidden">
    @foreach ($plans as $index => $plan)
        @php
            $isFeatured = $plan['featured'] ?? false;
            $paymentPlan = $plansByName->get($plan['db_name'] ?? $plan['name']);
            $buttonClass = $isFeatured
                ? 'bg-[#2CC295] py-3 text-[#000F1F] shadow-[0_0_20px_rgba(44,194,149,0.3)] hover:bg-[#00BF81]'
                : 'bg-white/10 py-3 text-white hover:bg-white/20';
            $cardClass = $isFeatured
                ? 'relative flex h-full min-w-0 max-w-full flex-col overflow-hidden rounded-3xl border-2 border-[#2CC295] bg-gradient-to-b from-[#006A4C]/30 to-[#031A19]/80 px-8 pb-8 pt-14 shadow-[0_0_40px_rgba(44,194,149,0.15)] backdrop-blur-md'
                : 'flex h-full min-w-0 max-w-full flex-col overflow-hidden rounded-3xl border border-[#006A4C]/30 bg-[#031A19]/60 p-8 backdrop-blur-md transition-colors hover:border-[#006A4C]/60';
        @endphp

        <article id="{{ $plan['id'] }}" class="{{ $cardClass }} {{ $animationClasses[$index % 3] }} w-full shrink-0 md:w-[calc((100%_-_2rem)/2)] xl:w-[calc((100%_-_4rem)/3)]">
            @if ($isFeatured)
                <div class="absolute left-1/2 top-4 w-max max-w-[85%] -translate-x-1/2 rounded-full bg-[#2CC295] px-4 py-1.5 text-center text-xs font-bold uppercase tracking-wide text-[#000F1F] shadow-lg">
                    Más completo
                </div>
            @endif

            <div class="mb-5">
                <span class="mb-3 inline-flex rounded-full {{ $isFeatured ? 'bg-[#2CC295] text-[#000F1F]' : 'bg-[#2CC295]/20 text-[#2CC295]' }} px-3 py-1 text-xs font-bold uppercase tracking-wide">
                    {{ $plan['tag'] }}
                </span>
                <h3 class="mb-3 text-2xl font-bold leading-tight">{{ $plan['name'] }}</h3>
                <p class="min-h-16 text-sm leading-6 text-[#AAC8C4]">{{ $plan['short_description'] }}</p>
            </div>

            <div class="mb-6 rounded-2xl border border-white/5 bg-black/10 p-4">
                <div class="flex flex-wrap items-baseline gap-x-2 gap-y-1">
                    @if (filled($plan['headline_prefix']))
                        <span class="text-sm font-semibold uppercase tracking-wide text-[#2CC295]">{{ $plan['headline_prefix'] }}</span>
                    @endif
                    <span class="text-4xl font-bold md:text-5xl">${{ $plan['headline_price'] }}</span>
                    <span class="text-[#AAC8C4]">USD</span>
                </div>
                <p class="mt-1 text-xs text-[#AAC8C4]">{{ $plan['headline_suffix'] }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-3">
                    <span class="text-sm text-[#707D7D] line-through">${{ $plan['base_price'] }} USD sin IVA</span>
                    <span class="rounded-full bg-[#2CC295]/20 px-2 py-1 text-xs font-medium text-[#2CC295]">{{ $plan['discount'] }}</span>
                </div>
                <p class="mt-2 text-xs text-[#AAC8C4]">
                    Precio con descuento: ${{ $plan['discounted_price'] }} USD sin IVA.
                </p>
            </div>

            @if (! empty($plan['pricing_rows']))
                <div class="mb-6 overflow-hidden rounded-xl border border-white/10">
                    <table class="w-full table-fixed text-left text-xs">
                        <thead class="bg-[#006A4C]/20 text-[#AAC8C4]">
                            <tr>
                                <th class="px-3 py-2 font-medium">Opción</th>
                                <th class="w-24 px-3 py-2 text-right font-medium">Final IVA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/5">
                            @foreach ($plan['pricing_rows'] as $row)
                                <tr>
                                    <td class="break-words px-3 py-2 text-[#F1F7F6]">{{ $row['label'] }}</td>
                                    <td class="px-3 py-2 text-right font-semibold text-[#2CC295]">${{ $row['final'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <div class="mb-8">
                @if ($mode === 'private')
                    @if ($hasActiveMembership)
                        <a href="{{ route('private.upload-document') }}" class="block w-full rounded-xl text-center font-bold transition-colors {{ $buttonClass }}" wire:navigate>
                            Ir a cargar
                        </a>
                    @elseif ($paymentPlan)
                        <form method="POST" action="{{ route('private.planes.select', $paymentPlan) }}">
                            @csrf
                            <button type="submit" class="block w-full rounded-xl text-center font-bold transition-colors {{ $buttonClass }}">
                                Solicitar plan
                            </button>
                        </form>
                    @else
                        <button type="button" disabled class="block w-full cursor-not-allowed rounded-xl bg-white/10 py-3 text-center font-semibold text-white/55">
                            No disponible
                        </button>
                    @endif
                @else
                    <a href="{{ $loginUrl }}" class="block w-full rounded-xl text-center font-bold transition-colors {{ $buttonClass }}">
                        Empezar ahora
                    </a>
                @endif
                <a href="#{{ $plan['id'] }}-detalle" class="mt-3 block text-center text-sm font-semibold text-[#2CC295] hover:text-[#00BF81]">
                    Ver detalle
                </a>
            </div>

            <div class="mt-auto space-y-4">
                <p class="text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Beneficios principales</p>
                <ul class="space-y-3 text-sm">
                    @foreach (array_slice($plan['benefits'], 0, 6) as $benefit)
                        <li class="flex gap-3">
                            <span class="mt-0.5 text-[#2CC295]">&#10003;</span>
                            <span>{{ $benefit }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </article>
    @endforeach
</div>
