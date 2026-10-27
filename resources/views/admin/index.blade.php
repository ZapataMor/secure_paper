<x-layouts::app :title="__('Proyectos')">
    <style>
    .sp-project-list{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
    .sp-project-list>a{display:flex;flex-direction:column;align-items:flex-start;gap:8px;min-width:0;text-align:left;background:linear-gradient(145deg,#04352a,#001f1b)}
    .sp-project-list>a>*{max-width:100%;overflow-wrap:anywhere}.sp-project-list>a:focus-visible{outline:2px solid #2cc295;outline-offset:4px}
    .sp-project-list>a .sp-project-service{margin-top:auto;padding-top:14px}.sp-project-list .sp-project-open{font-size:13px;font-weight:650;color:#2cc295;margin-top:8px}
    @media(max-width:1000px){.sp-project-list{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:620px){.sp-project-list{grid-template-columns:1fr}}
    </style>
    <section class="sp-admin-page">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">{{ auth()->user()->isAdmin() ? 'Administración' : 'Panel del asesor' }}</p>
            <h1>Proyectos</h1>
            <p>{{ auth()->user()->isAdmin() ? 'Consulta los expedientes, organiza la asignación y revisa el seguimiento de cada proyecto.' : 'Aquí encontrarás los proyectos que administración te ha asignado.' }}</p>
        </header>

        <div class="sp-project-list">
            @forelse($users as $user)
                @php
                    $project = ($assignmentReady ?? false) ? $user->projectAssignment : null;
                    $planName = $user->activePaidSubscription?->paymentPlan?->name;
                    $hasMembership = filled($planName);
                @endphp

                <a
                    href="{{ route('admin.works.show', $user) }}"
                    class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 px-5 py-6 text-center shadow-[10px_10px_24px_rgba(0,15,31,0.24)] transition hover:border-[#2CC295]/70 hover:bg-[#032221]"
                >
                    <p class="text-base font-semibold text-[#F1F7F6]" style="overflow-wrap:anywhere">
                        {{ $project?->project_title ?? $user->fullName() }}
                    </p>
                    @if(filled($project?->project_title) && $project->project_title !== $user->fullName())
                        <p class="mt-2 text-xs text-[#AAC8C4]">Cliente: {{ $user->fullName() }}</p>
                    @endif
                    <p class="mt-2 text-sm text-[#2CC295]">{{ $project ? (\App\Models\ProjectAssignment::STATUSES[$project->progress_status] ?? 'Seguimiento por revisar') : 'Sin seguimiento registrado' }}</p>
                    <p class="text-sm text-[#AAC8C4]">Asesor: {{ $project?->advisor?->fullName() ?? 'Pendiente de asignación' }}</p>
                    <p class="sp-project-service mt-2 text-xs uppercase tracking-wide {{ $hasMembership ? 'text-[#2CC295]' : 'text-[#F9BC60]' }}">
                        {{ $hasMembership ? 'Servicio vigente' : 'Sin servicio vigente' }}
                    </p>
                    @if($hasMembership)
                    <span class="mt-2 inline-flex rounded-full border px-2.5 py-1 text-xs font-semibold border-[#2CC295]/50 bg-[#2CC295]/20 text-[#2CC295]">
                        {{ $planName }}
                    </span>
                    @endif
                    <span class="sp-project-open">Abrir expediente <span aria-hidden="true">→</span></span>
                </a>
            @empty
                <p class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 px-5 py-6 text-sm text-[#AAC8C4] sm:col-span-2 lg:col-span-3 xl:col-span-4">
                    {{ auth()->user()->isAdmin() ? 'Aún no hay expedientes de clientes para mostrar.' : 'Todavía no tienes proyectos asignados. Cuando administración te asigne uno, aparecerá aquí.' }}
                </p>
            @endforelse
        </div>
    </section>
</x-layouts::app>
