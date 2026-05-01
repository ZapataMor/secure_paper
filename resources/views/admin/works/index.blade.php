<x-layouts::app :title="__('Trabajos')">
    <section class="sp-admin-page">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Modulo administrador</p>
            <h1>Trabajos</h1>
            <p>Selecciona un usuario para revisar sus archivos y su informacion adicional.</p>
        </header>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @forelse($users as $user)
                @php
                    $planName = $user->activePaidSubscription?->paymentPlan?->name;
                    $hasMembership = filled($planName);
                @endphp

                <a
                    href="{{ route('admin.works.show', $user) }}"
                    class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 px-5 py-6 text-center shadow-[10px_10px_24px_rgba(0,15,31,0.24)] transition hover:border-[#2CC295]/70 hover:bg-[#032221]"
                >
                    <p class="text-base font-semibold text-[#F1F7F6]">
                        {{ $user->fullName() }}
                    </p>
                    <p class="mt-2 text-xs uppercase tracking-wide {{ $hasMembership ? 'text-[#2CC295]' : 'text-[#F9BC60]' }}">
                        {{ $hasMembership ? 'Plan activo' : 'Sin plan activo' }}
                    </p>
                    <span class="mt-2 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold {{ $hasMembership ? 'border-[#2CC295]/50 bg-[#2CC295]/20 text-[#2CC295]' : 'border-[#F9BC60]/50 bg-[#F9BC60]/20 text-[#FAD7A0]' }}">
                        {{ $planName ?? 'Sin membresia' }}
                    </span>
                </a>
            @empty
                <p class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 px-5 py-6 text-sm text-[#AAC8C4] sm:col-span-2 lg:col-span-3 xl:col-span-4">
                    No hay usuarios disponibles para este modulo.
                </p>
            @endforelse
        </div>
    </section>
</x-layouts::app>
