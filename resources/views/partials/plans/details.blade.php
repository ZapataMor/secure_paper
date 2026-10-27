@php
    $plans = $plans ?? config('propaps_plans.plans', []);
@endphp

<section class="relative z-10 mx-auto max-w-7xl px-6 pb-24">
    <div class="mb-12 text-center">
        <span class="mb-3 block text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Lineas de servicio</span>
        <h2 class="mb-4 text-3xl font-bold">Conoce en detalle cada servicio</h2>
        <p class="mx-auto max-w-3xl text-[#AAC8C4]">
            Cada paquete cuenta con Linea Estandar y Linea Premium para adaptar el acompanamiento al alcance, plazo y nivel academico de su proyecto.
        </p>
    </div>

    <div class="space-y-10">
        @foreach ($plans as $index => $plan)
            @php
                $isFeatured = $plan['featured'] ?? false;
            @endphp

            <article id="{{ $plan['id'] }}-detalle" class="overflow-hidden rounded-3xl {{ $isFeatured ? 'border border-[#2CC295]/50 bg-gradient-to-r from-[#031A19]/80 to-[#006A4C]/10 shadow-[0_0_20px_rgba(44,194,149,0.05)]' : 'border border-[#006A4C]/30 bg-[#031A19]/50' }}">
                <div class="border-b {{ $isFeatured ? 'border-[#2CC295]/30 bg-gradient-to-r from-[#006A4C]/40 to-transparent' : 'border-[#006A4C]/30 bg-[#006A4C]/20' }} px-6 py-5">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="h-2 w-2 rounded-full bg-[#2CC295] {{ $isFeatured ? 'shadow-[0_0_8px_#2CC295]' : '' }}"></span>
                        <span class="rounded-full bg-[#2CC295]/20 px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#2CC295]">{{ $plan['tag'] }}</span>
                        @if ($isFeatured)
                            <span class="rounded-full bg-[#2CC295] px-3 py-1 text-xs font-bold uppercase tracking-wide text-[#000F1F]">Recomendado</span>
                        @endif
                    </div>
                    <h3 class="mt-4 text-2xl font-bold leading-tight md:text-3xl">{{ $index + 1 }}. {{ $plan['name'] }}</h3>
                    <p class="mt-3 max-w-5xl text-base leading-7 text-[#AAC8C4]">{{ $plan['short_description'] }}</p>
                </div>

                <div class="grid gap-5 p-6 lg:grid-cols-2">
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
            </article>
        @endforeach
    </div>
</section>
