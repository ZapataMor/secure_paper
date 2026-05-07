@php
    $plans = $plans ?? config('propaps_plans.plans', []);
    $terms = config('propaps_plans.terms', []);
@endphp

<section class="relative z-10 mx-auto max-w-6xl px-6 pb-24">
    <div class="mb-12 text-center">
        <span class="mb-3 block text-sm font-semibold uppercase tracking-wider text-[#2CC295]">Condiciones claras</span>
        <h2 class="mb-4 text-3xl font-bold">Conoce en detalle cada servicio</h2>
        <p class="mx-auto max-w-3xl text-[#AAC8C4]">
            Precios en USD con valor base, descuento, precio con descuento sin IVA y precio final con IVA cuando aplica.
        </p>
    </div>

    <div class="mb-12 grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach ($terms as $term)
            <div class="rounded-2xl border border-[#006A4C]/30 bg-[#031A19]/50 p-4 text-sm leading-6 text-[#AAC8C4]">
                <span class="mr-2 text-[#2CC295]">&#10003;</span>{{ $term }}
            </div>
        @endforeach
    </div>

    <div class="space-y-12">
        @foreach ($plans as $index => $plan)
            @php
                $isFeatured = $plan['featured'] ?? false;
            @endphp

            <article id="{{ $plan['id'] }}-detalle" class="overflow-hidden rounded-2xl {{ $isFeatured ? 'border border-[#2CC295]/50 bg-gradient-to-r from-[#031A19]/80 to-[#006A4C]/10 shadow-[0_0_20px_rgba(44,194,149,0.05)]' : 'border border-[#006A4C]/30 bg-[#031A19]/40' }}">
                <div class="flex flex-wrap items-center gap-3 border-b {{ $isFeatured ? 'border-[#2CC295]/30 bg-gradient-to-r from-[#006A4C]/40 to-transparent' : 'border-[#006A4C]/30 bg-[#006A4C]/20' }} px-6 py-4">
                    <div class="h-2 w-2 rounded-full bg-[#2CC295] {{ $isFeatured ? 'shadow-[0_0_8px_#2CC295]' : '' }}"></div>
                    <h3 class="text-lg font-bold">
                        {{ $index + 1 }}. {{ $plan['name'] }}
                        @if ($isFeatured)
                            <span class="ml-2 text-sm font-normal text-[#2CC295]">(Recomendado para publicacion)</span>
                        @endif
                    </h3>
                </div>

                <div class="space-y-6 p-6">
                    <p class="max-w-4xl text-sm leading-6 text-[#AAC8C4]">{{ $plan['description'] }}</p>

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
                        <div class="rounded-xl border border-white/10 bg-black/10 p-4">
                            <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Precio</h4>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#AAC8C4]">Base sin IVA</dt>
                                    <dd class="font-semibold">${{ $plan['base_price'] }} USD</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#AAC8C4]">Descuento</dt>
                                    <dd class="font-semibold text-[#2CC295]">{{ $plan['discount'] }}</dd>
                                </div>
                                <div class="flex justify-between gap-4">
                                    <dt class="text-[#AAC8C4]">Con descuento sin IVA</dt>
                                    <dd class="font-semibold">${{ $plan['discounted_price'] }} USD</dd>
                                </div>
                                <div class="flex justify-between gap-4 border-t border-white/10 pt-2">
                                    <dt class="text-[#AAC8C4]">Final con IVA</dt>
                                    <dd class="font-bold text-[#2CC295]">{{ filled($plan['headline_prefix']) ? $plan['headline_prefix'].' ' : '' }}${{ $plan['final_price'] }} USD</dd>
                                </div>
                                @if (! empty($plan['renewal']))
                                    <div class="flex justify-between gap-4">
                                    <dt class="text-[#AAC8C4]">Renovación</dt>
                                        <dd class="text-right font-semibold">{{ $plan['renewal'] }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>

                        @if (! empty($plan['pricing_rows']))
                            <div class="overflow-x-auto rounded-xl border border-white/10">
                                <table class="w-full text-left text-sm">
                                    <thead class="bg-[#006A4C]/20 text-[#AAC8C4]">
                                        <tr>
                                            <th class="px-4 py-3 font-medium">Opción</th>
                                            <th class="px-4 py-3 text-right font-medium">Base</th>
                                            <th class="px-4 py-3 text-right font-medium">Desc.</th>
                                            <th class="px-4 py-3 text-right font-medium">Final IVA</th>
                                            @if (isset($plan['pricing_rows'][0]['monthly']))
                                                <th class="px-4 py-3 text-right font-medium">Mensual aprox.</th>
                                                <th class="px-4 py-3 text-right font-medium">Renovación</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-white/5">
                                        @foreach ($plan['pricing_rows'] as $row)
                                            <tr>
                                                <td class="px-4 py-3">{{ $row['label'] }}</td>
                                                <td class="px-4 py-3 text-right text-[#AAC8C4]">${{ $row['base'] }}</td>
                                                <td class="px-4 py-3 text-right text-[#AAC8C4]">${{ $row['discounted'] }}</td>
                                                <td class="px-4 py-3 text-right font-semibold text-[#2CC295]">${{ $row['final'] }}</td>
                                                @if (isset($row['monthly']))
                                                    <td class="px-4 py-3 text-right text-[#AAC8C4]">${{ $row['monthly'] }}</td>
                                                    <td class="px-4 py-3 text-right text-[#AAC8C4]">${{ $row['renewal'] }}</td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="rounded-xl border border-white/10 bg-black/10 p-4">
                                <h4 class="mb-3 text-sm font-semibold uppercase tracking-wider text-[#AAC8C4]">Entregables principales</h4>
                                <ul class="grid grid-cols-1 gap-3 text-sm md:grid-cols-2">
                                    @foreach ($plan['benefits'] as $benefit)
                                        <li class="flex gap-3">
                                            <span class="mt-0.5 text-[#2CC295]">&#10003;</span>
                                            <span>{{ $benefit }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="border-b border-white/10 text-[#AAC8C4]">
                                    <th class="w-32 pb-3 font-medium">Etapa</th>
                                    <th class="w-52 pb-3 font-medium">Momento</th>
                                    <th class="pb-3 font-medium">Actividades principales</th>
                                    <th class="w-1/3 pb-3 font-medium">Entregables</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-white/5">
                                @foreach ($plan['detail_rows'] as $row)
                                    <tr>
                                        <td class="py-4 pr-4 font-semibold text-[#2CC295]">{{ $row['stage'] }}</td>
                                        <td class="py-4 pr-4">{{ $row['timing'] }}</td>
                                        <td class="py-4 pr-4">{{ $row['activities'] }}</td>
                                        <td class="py-4 text-[#AAC8C4]">{{ $row['deliverables'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
