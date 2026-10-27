<style>
.sp-project-card{border:1px solid #17614b;border-radius:18px;padding:22px;background:linear-gradient(135deg,#04352a,#001f1b);color:#f1f7f6;margin:20px 0}.sp-project-card h2{font-size:21px;margin:6px 0 12px}.sp-project-kicker{font-size:11px;letter-spacing:.12em;color:#2cc295;text-transform:uppercase}.sp-project-meta{display:flex;flex-wrap:wrap;gap:12px;font-size:13px;color:#bcd7ce}.sp-project-badge{background:#064c39;border:1px solid #278869;border-radius:20px;padding:4px 12px;color:#6ce2ba}.sp-project-form{display:grid;grid-template-columns:2fr 1fr 1fr;gap:16px;margin-top:20px}.sp-project-form label{display:block;font-size:13px;margin-bottom:7px;color:#bcd7ce}.sp-project-form input,.sp-project-form select{width:100%;min-width:0;background:#001e1a;border:1px solid #27614f;border-radius:9px;padding:11px 12px;color:#f1f7f6}.sp-project-form button{grid-column:1/-1;justify-self:start;border-radius:10px;padding:11px 18px;background:#2cc295;font-weight:700;color:#001f1b}.sp-project-card p{line-height:1.7}.sp-project-help{font-size:13px;color:#aac8c4;margin-top:14px}.sp-project-card input:focus-visible,.sp-project-card select:focus-visible,.sp-project-card button:focus-visible{outline:2px solid #2cc295;outline-offset:3px}@media(max-width:700px){.sp-project-form{grid-template-columns:1fr}.sp-project-card{padding:20px 16px}.sp-project-meta{gap:8px}}
</style>
<style>.sp-project-card h2{overflow-wrap:anywhere}</style>
<article class="sp-project-card">
<p class="sp-project-kicker">Seguimiento del proyecto</p>
<h2>{{ $projectAssignment?->project_title ?? 'Expediente del proyecto' }}</h2>
<div class="sp-project-meta"><span class="sp-project-badge">{{ \App\Models\ProjectAssignment::STATUSES[$projectAssignment?->progress_status ?? 'received'] ?? 'Recibido' }}</span>
<span>Asesor: {{ $projectAssignment?->advisor?->fullName() ?? 'Pendiente de asignación' }}</span></div>
@if(($projectEditable ?? false) && ($assignmentReady ?? false))
    @if(auth()->user()->isAdmin())
    <form class="sp-project-form" method="POST" action="{{ route('admin.works.assignment',$targetUser) }}">@csrf
    <div><label for="project-title">Nombre del proyecto</label><input id="project-title" name="project_title" maxlength="160" required value="{{ old('project_title',$projectAssignment?->project_title ?? 'Proyecto de '.$targetUser->fullName()) }}"></div>
    <div><label for="project-advisor">Asesor responsable</label><select id="project-advisor" name="advisor_id"><option value="">Sin asignar</option>@foreach($advisors as $advisor)<option value="{{ $advisor->id }}" @selected((string)old('advisor_id',$projectAssignment?->advisor_id)===(string)$advisor->id)>{{ $advisor->fullName() }}</option>@endforeach</select></div>
    <div><label for="project-state">Estado del proyecto</label><select id="project-state" name="progress_status">@foreach(\App\Models\ProjectAssignment::STATUSES as $key=>$label)<option value="{{ $key }}" @selected(old('progress_status',$projectAssignment?->progress_status ?? 'received')===$key)>{{ $label }}</option>@endforeach</select></div>
    <button type="submit">Guardar asignación y estado</button></form>
    <p class="sp-project-help">La asignación abarca todos los documentos e indicaciones de este expediente. Al cambiar de asesor, el anterior pierde el acceso. No modifica el pago ni las fechas del servicio.</p>
    @elseif($projectAssignment)
    <form class="sp-project-form" method="POST" action="{{ route('admin.works.progress',$targetUser) }}">@csrf
    <div><label for="project-state">Actualizar seguimiento</label><select id="project-state" name="progress_status">@foreach(\App\Models\ProjectAssignment::STATUSES as $key=>$label)<option value="{{ $key }}" @selected(old('progress_status',$projectAssignment->progress_status)===$key)>{{ $label }}</option>@endforeach</select></div><button type="submit">Guardar estado</button></form>
    @endif
@elseif(($projectEditable ?? false) && !($assignmentReady ?? false))
<p class="sp-project-help">La asignación estará disponible al terminar la instalación de este bloque.</p>
@endif
</article>
