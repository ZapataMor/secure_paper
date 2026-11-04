<x-layouts::app :title="__('Confirmar cambio de acceso')">
    <section class="sp-admin-page mx-auto w-full max-w-3xl">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Administración · Acceso a cuentas</p>
            <h1>{{ $desiredStatus === 'inactive' ? 'Desactivar cuenta' : 'Reactivar cuenta' }}</h1>
            <p>Revisa la cuenta y confirma el cambio.</p>
        </header>
        <article class="sp-users-card p-5 space-y-4 text-[#D7FFF2]">
            <p><strong>{{ $targetUser->fullName() }}</strong><br>{{ $targetUser->email }}</p>
            <p>Rol: {{ ['client' => 'Cliente', 'advisor' => 'Asesor', 'admin' => 'Administrador'][$targetUser->role] ?? 'Sin rol' }} · Estado actual: {{ $targetUser->status === 'active' ? 'Activo' : 'Inactivo' }}</p>
            @if($desiredStatus === 'inactive')
                <p>Se cerrarán todas sus sesiones y se invalidará el acceso de «recordarme». No podrá ingresar hasta que se reactive su cuenta.</p>
            @else
                <p>La cuenta podrá ingresar nuevamente. Sus sesiones anteriores seguirán cerradas.</p>
            @endif
            <p>Se conservan los documentos, mensajes, asignaciones y fechas del servicio. Este cambio no envía correos ni genera cobros.</p>
            @if($errors->any())
                <ul role="alert" class="rounded-lg border border-red-300 p-3 text-red-200">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            @endif
            @if($targetUser->id === auth()->id() && $desiredStatus === 'inactive')
                <p>No puedes desactivar tu propia cuenta.</p>
            @else
                <form method="POST" action="{{ route('admin.users.status.update', $targetUser) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="status" value="{{ $desiredStatus }}">
                    <input type="hidden" name="expected_status" value="{{ $targetUser->status }}">
                    <input type="hidden" name="expected_epoch" value="{{ $targetUser->session_epoch }}">
                    <label class="block" for="status-reason">Motivo del cambio (hasta 500 caracteres)</label>
                    <textarea id="status-reason" name="reason" required maxlength="500" rows="3" class="w-full rounded-lg border border-[#2CC295]/60 bg-[#032221] p-3 text-[#D7FFF2]">{{ old('reason') }}</textarea>
                    <label class="flex items-start gap-3"><input type="checkbox" name="confirmed" value="1" required class="mt-1"><span>Confirmo que quiero {{ $desiredStatus === 'inactive' ? 'desactivar' : 'reactivar' }} la cuenta de {{ $targetUser->email }}.</span></label>
                    <button type="submit" class="rounded-lg bg-[#2CC295] px-5 py-3 font-bold text-[#00251b] focus-visible:outline focus-visible:outline-2">{{ $desiredStatus === 'inactive' ? 'Confirmar desactivación' : 'Confirmar reactivación' }}</button>
                </form>
            @endif
            <a href="{{ route('admin.users.index') }}" class="inline-block rounded-lg border border-[#2CC295]/60 px-5 py-3 focus-visible:outline focus-visible:outline-2">Cancelar y volver al listado</a>
        </article>
    </section>
</x-layouts::app>