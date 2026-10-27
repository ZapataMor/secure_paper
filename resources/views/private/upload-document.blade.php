<x-layouts::app :title="__('Cargar documentos')">
    @php
        $documents = $documents ?? collect();
        $adminDocuments = $adminDocuments ?? collect();
        $adminMessages = $adminMessages ?? collect();
        $additionalInformationEntries = collect($additionalInformationEntries ?? []);
        $activePlanName = auth()->user()?->activeMembershipPlanName();
        $formatFileSize = static function (int $bytes): string {
            if ($bytes < 1024) return $bytes.' B';
            if ($bytes < 1024 * 1024) return number_format($bytes / 1024, 1).' KB';
            return number_format($bytes / (1024 * 1024), 2).' MB';
        };
    @endphp
    <style>
        .sp-upload { --up-accent: #2cc295; --up-text: #f1f7f6; --up-muted: #b5cec8; width: min(100%, 1080px); margin: 8px auto 0; color: var(--up-text); }
        .sp-upload *, .sp-upload *::before, .sp-upload *::after { box-sizing: border-box; }
        .sp-upload [x-cloak] { display: none !important; }
        .sp-upload h1, .sp-upload h2, .sp-upload h3, .sp-upload p { margin: 0; }
        .sp-upload ul { list-style: none; padding: 0; margin: 0; }
        .sp-upload a { text-decoration: none; }
        .sp-upload button, .sp-upload input, .sp-upload textarea { font: inherit; }
        .sp-upload a:focus-visible, .sp-upload button:focus-visible, .sp-upload input:focus-visible, .sp-upload textarea:focus-visible { outline: 3px solid var(--up-accent); outline-offset: 4px; }
        .sp-upload-hero { display: flex; justify-content: space-between; align-items: flex-end; gap: 24px; padding: 14px 0 28px; }
        .sp-upload-kicker { color: var(--up-accent); font-size: 11px; font-weight: 700; letter-spacing: .14em; text-transform: uppercase; }
        .sp-upload h1 { margin-top: 14px; font-size: clamp(30px, 3.7vw, 44px); font-weight: 700; line-height: 1.15; letter-spacing: -.035em; }
        .sp-upload-intro { max-width: 660px; margin-top: 15px !important; color: var(--up-muted); font-size: 16px; line-height: 1.7; }
        .sp-upload-link { color: var(--up-accent); font-size: 13px; font-weight: 600; }
        .sp-upload-link:hover { text-decoration: underline; text-underline-offset: 4px; }
        .sp-upload-service { display: flex; align-items: center; flex-wrap: wrap; gap: 8px 14px; margin-bottom: 22px; padding: 13px 17px; border: 1px solid #2cc29530; border-radius: 11px; background: #006a4c20; font-size: 13px; }
        .sp-upload-service strong { color: var(--up-accent); font-weight: 600; }
        .sp-upload-service span { overflow-wrap: anywhere; }
        .sp-upload-service .sp-upload-link { margin-left: auto; }
        .sp-upload-card { min-width: 0; padding: 25px; border: 1px solid #2cc29530; border-radius: 18px; background: #001e1ac2; }
        .sp-upload-form { border: 1px solid #2cc29540; border-radius: 19px; background: #032a1e55; overflow: hidden; }
        .sp-upload-columns { display: grid; grid-template-columns: 1.12fr 1fr; gap: 0; }
        .sp-upload-field { min-width: 0; padding: 26px; }
        .sp-upload-field + .sp-upload-field { border-left: 1px solid #2cc29526; }
        .sp-upload-heading { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
        .sp-upload-number { display: grid; place-items: center; flex: 0 0 auto; width: 32px; height: 32px; border: 1px solid #2cc29555; border-radius: 10px; background: #006a4c2e; color: var(--up-accent); font-size: 11px; font-weight: 700; }
        .sp-upload h2 { font-size: 20px; font-weight: 650; line-height: 1.35; letter-spacing: -.02em; }
        .sp-upload-description { color: var(--up-muted); font-size: 14px; line-height: 1.65; }
        .sp-upload-drop { margin-top: 19px; padding: 22px 18px; border: 1px dashed #2cc29580; border-radius: 13px; background: #001c1870; text-align: center; }
        .sp-upload-drop.is-dragging { background: #006a4c50; border-style: solid; }
        .sp-upload-drop-icon { width: 31px; height: 31px; margin: 0 auto 12px; color: var(--up-accent); }
        .sp-upload-drop label { display: block; font-size: 15px; font-weight: 600; line-height: 1.5; }
        .sp-upload-drop p { margin-top: 7px; color: var(--up-muted); font-size: 12px; line-height: 1.65; }
        .sp-upload-input { display: block; width: 100%; min-width: 0; margin-top: 17px; padding: 10px; border: 1px solid #2cc29535; border-radius: 8px; background: #002820; color: #c8ddd6; font-size: 12px !important; }
        .sp-upload-input::file-selector-button { margin-right: 10px; padding: 8px 10px; border: 0; border-radius: 6px; background: var(--up-accent); color: #00251b; font: inherit; font-weight: 700; cursor: pointer; }
        .sp-upload-selected { margin-top: 15px; }
        .sp-upload-selected-head { margin-bottom: 8px !important; color: var(--up-muted); font-size: 12px; }
        .sp-upload-selected li { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-top: 1px solid #2cc29520; }
        .sp-upload-selected-name { min-width: 0; flex: 1; }
        .sp-upload-selected-name span { display: block; overflow-wrap: anywhere; font-size: 13px; }
        .sp-upload-selected-name small { color: var(--up-muted); font-size: 11px; }
        .sp-upload-remove { flex: 0 0 auto; padding: 7px 10px; border: 1px solid #2cc29530; border-radius: 7px; background: transparent; color: #c8ddd6; font-size: 12px !important; cursor: pointer; }
        .sp-upload-remove:hover { border-color: var(--up-accent); }
        .sp-upload-textarea { display: block; width: 100%; min-height: 196px; margin-top: 19px; padding: 15px; border: 1px solid #2cc29545; border-radius: 12px; background: #001c1890; color: var(--up-text); font-size: 14px !important; line-height: 1.7; resize: vertical; }
        .sp-upload-textarea::placeholder { color: #a2bcb3; }
        .sp-upload-help { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 6px 14px; margin-top: 9px; color: var(--up-muted); font-size: 11px; line-height: 1.6; }
        .sp-upload-hint { margin-top: 17px !important; color: var(--up-muted); font-size: 12px; line-height: 1.65; }
        .sp-upload-send { display: flex; align-items: center; justify-content: space-between; gap: 20px; padding: 20px 26px; border-top: 1px solid #2cc29530; background: #006a4c12; }
        .sp-upload-send h3 { font-size: 14px; font-weight: 650; }
        .sp-upload-send p { margin-top: 5px; color: var(--up-muted); font-size: 12px; line-height: 1.6; }
        .sp-upload-submit { display: inline-flex; align-items: center; justify-content: center; flex: 0 0 auto; gap: 10px; padding: 12px 21px; border: 0; border-radius: 9px; background: var(--up-accent); color: #00251b; font-size: 13px !important; font-weight: 700 !important; cursor: pointer; }
        .sp-upload-submit:hover { background: #51d3ac; }
        .sp-upload-submit:disabled { cursor: wait; opacity: .75; }
        .sp-upload-submit svg { width: 16px; height: 16px; }
        .sp-upload-alert { margin-bottom: 18px; padding: 16px 18px; border: 1px solid #2cc29555; border-radius: 11px; background: #006a4c35; color: #d7fff2; font-size: 13px; line-height: 1.65; }
        .sp-upload-alert.is-error { border-color: #f1a7a755; background: #7e303033; color: #f9caca; }
        .sp-upload-alert h2 { margin-bottom: 5px; font-size: 15px; }
        .sp-upload-form .sp-upload-alert { margin: 0 26px 18px; }
        .sp-upload-section { margin-top: 30px; }
        .sp-upload-section-head { margin-bottom: 17px; }
        .sp-upload-section-head > p { margin-top: 7px; color: var(--up-muted); font-size: 14px; line-height: 1.65; }
        .sp-upload-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .sp-upload-card-head { display: flex; align-items: center; justify-content: space-between; gap: 15px; margin-bottom: 10px; }
        .sp-upload-card h3 { font-size: 17px; font-weight: 650; line-height: 1.35; }
        .sp-upload-count { display: grid; place-items: center; min-width: 27px; height: 27px; padding-inline: 7px; border: 1px solid #2cc29540; border-radius: 8px; color: var(--up-accent); font-size: 12px; }
        .sp-upload-empty { margin-top: 17px; padding: 22px 17px; border: 1px dashed #2cc29530; border-radius: 10px; color: var(--up-muted); font-size: 13px; line-height: 1.7; }
        .sp-upload-file-list { margin-top: 15px !important; }
        .sp-upload-file-list li + li { border-top: 1px solid #2cc29526; }
        .sp-upload-file-link { display: flex; align-items: center; gap: 12px; padding: 14px 0; color: var(--up-text); }
        .sp-upload-file-link:hover .sp-upload-file-name { color: var(--up-accent); }
        .sp-upload-thumb { display: flex; align-items: center; justify-content: center; flex: 0 0 auto; width: 38px; height: 44px; }
        .sp-upload-thumb img { width: 38px; height: 44px; object-fit: cover; border-radius: 6px; }
        .sp-upload-file-icon { width: 34px; height: 40px; }
        .sp-upload-file-info { flex: 1; min-width: 0; }
        .sp-upload-file-name { display: block; overflow-wrap: anywhere; font-size: 13px; font-weight: 600; line-height: 1.55; }
        .sp-upload-file-meta { display: block; margin-top: 4px; color: var(--up-muted); font-size: 11px; line-height: 1.6; }
        .sp-upload-open { flex: 0 0 auto; color: var(--up-accent); font-size: 12px; }
        .sp-upload-history { margin-top: 17px !important; }
        .sp-upload-history li { padding: 16px 0; border-top: 1px solid #2cc29526; }
        .sp-upload-history-meta { display: flex; flex-wrap: wrap; gap: 5px 12px; color: var(--up-muted); font-size: 11px; line-height: 1.6; }
        .sp-upload-history li > p { margin-top: 9px; color: #d4e6df; font-size: 13px; line-height: 1.75; white-space: pre-line; overflow-wrap: anywhere; }
        .sp-upload-footer { margin: 21px 0 9px; color: var(--up-muted); font-size: 12px; line-height: 1.7; }
        @media (max-width: 760px) {
            .sp-upload-columns, .sp-upload-grid { grid-template-columns: 1fr; }
            .sp-upload-field + .sp-upload-field { border-left: 0; border-top: 1px solid #2cc29526; }
            .sp-upload-hero { align-items: flex-start; flex-direction: column; gap: 15px; }
        }
        @media (max-width: 540px) {
            .sp-upload { margin-top: 0; }
            .sp-upload-hero { padding-top: 9px; padding-bottom: 23px; }
            .sp-upload-intro { font-size: 15px; }
            .sp-upload-service { padding: 13px 14px; }
            .sp-upload-service .sp-upload-link { margin-left: 0; }
            .sp-upload-field, .sp-upload-card { padding: 20px 17px; }
            .sp-upload-drop { padding: 20px 12px; }
            .sp-upload-send { align-items: stretch; flex-direction: column; padding: 19px 17px; gap: 15px; }
            .sp-upload-form .sp-upload-alert { margin-inline: 17px; }
            .sp-upload-section { margin-top: 24px; }
        }
    </style>

    <section class="sp-upload" aria-labelledby="sp-upload-title">
        <header class="sp-upload-hero">
            <div>
                <p class="sp-upload-kicker">Tu espacio de trabajo · Cargar documentos</p>
                <h1 id="sp-upload-title">Tu expediente de trabajo</h1>
                <p class="sp-upload-intro">Reúne los archivos de tu proyecto y cuéntanos qué necesitas trabajar. Aquí también encontrarás las devoluciones y los mensajes para continuar el proceso.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="sp-upload-link" wire:navigate>Volver a Inicio →</a>
        </header>
        @include('partials.client-journey')
        @if($projectAssignment ?? null)
            @include('partials.project-card',['projectEditable'=>false])
        @endif
        <div class="sp-upload-service">
            <strong>Servicio activo</strong>
            @if($activePlanName)<span>{{ $activePlanName }}</span>@endif
            <a href="{{ route('private.planes') }}" class="sp-upload-link" wire:navigate>Consultar mi servicio →</a>
        </div>

        @if(session('status'))
            <div class="sp-upload-alert" role="status">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="sp-upload-alert is-error" role="alert">
                <h2>Revisa tu envío</h2>
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                <p>Las indicaciones se conservan. Si adjuntaste archivos, vuelve a seleccionarlos antes de enviar.</p>
            </div>
        @endif

        <form method="POST" action="{{ route('private.upload-document.store') }}" enctype="multipart/form-data" class="sp-upload-form"
            x-data="{
                files: [], notes: '', error: '', dragging: false, submitting: false,
                size(bytes) { return bytes < 1048576 ? (bytes / 1024).toFixed(1) + ' KB' : (bytes / 1048576).toFixed(2) + ' MB'; },
                sync() { const transfer = new DataTransfer(); this.files.forEach(file => transfer.items.add(file)); this.$refs.files.files = transfer.files; },
                add(incoming) {
                    const next = [...this.files];
                    for (const file of Array.from(incoming)) {
                        if (!next.some(item => item.name === file.name &amp;&amp; item.size === file.size &amp;&amp; item.lastModified === file.lastModified)) next.push(file);
                    }
                    let issue = '';
                    if (next.length > 10) issue = 'Puedes adjuntar hasta 10 archivos por envío.';
                    else if (next.some(file => file.size > 20971520)) issue = 'Cada archivo debe pesar como máximo 20 MB.';
                    else if (next.some(file => !/\.(pdf|doc|docx|xls|xlsx|jpg|jpeg|png|gif|webp|bmp)$/i.test(file.name))) issue = 'Selecciona archivos Word, PDF, Excel o imágenes JPG, PNG, GIF, WEBP o BMP.';
                    if (!issue) this.files = next;
                    this.error = issue; this.sync();
                },
                remove(index) { this.files.splice(index, 1); this.error = ''; this.sync(); },
                send(event) {
                    if (this.submitting) { event.preventDefault(); return; }
                    if (!this.files.length &amp;&amp; !this.notes.trim()) {
                        event.preventDefault(); this.error = 'Adjunta al menos un archivo o escribe tus indicaciones.'; this.$refs.notes.focus(); return;
                    }
                    if (this.error) { event.preventDefault(); return; }
                    this.submitting = true;
                }
            }"
            x-init="notes = $refs.notes.value"
            x-on:submit="send($event)"
            x-on:pageshow.window="submitting = false"
            x-bind:aria-busy="submitting"
        >
            @csrf
            <div class="sp-upload-columns">
                <section class="sp-upload-field" aria-labelledby="sp-upload-files-title">
                    <div class="sp-upload-heading"><span class="sp-upload-number" aria-hidden="true">01</span><h2 id="sp-upload-files-title">Archivos de tu proyecto</h2></div>
                    <p class="sp-upload-description">Adjunta el manuscrito y los materiales que ayuden a comprender tu trabajo.</p>
                    <div class="sp-upload-drop" x-bind:class="{ 'is-dragging': dragging }"
                        x-on:dragover.prevent="dragging = true" x-on:dragleave.self="dragging = false"
                        x-on:drop.prevent="dragging = false; add($event.dataTransfer.files)">
                        <svg class="sp-upload-drop-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V3m-5 5 5-5 5 5M4 16v4a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-4" /></svg>
                        <label for="document_files">Selecciona los archivos de tu proyecto</label>
                        <p>Puedes seleccionarlos desde tu equipo o arrastrarlos aquí.</p>
                        <input id="document_files" name="document_files[]" type="file" multiple
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.bmp"
                            class="sp-upload-input" x-ref="files" x-on:change="add($event.target.files)" aria-describedby="sp-upload-limits" />
                        <p id="sp-upload-limits">Word, PDF, Excel e imágenes · Hasta 10 archivos por envío<br>Máximo 20 MB por archivo</p>
                    </div>
                    <div class="sp-upload-selected" x-show="files.length" x-cloak>
                        <p class="sp-upload-selected-head" aria-live="polite" x-text="files.length + (files.length === 1 ? ' archivo preparado' : ' archivos preparados')"></p>
                        <ul>
                            <template x-for="(file, index) in files" x-bind:key="file.name + ':' + file.size + ':' + file.lastModified">
                                <li><div class="sp-upload-selected-name"><span x-text="file.name"></span><small x-text="size(file.size)"></small></div>
                                    <button type="button" class="sp-upload-remove" x-on:click="remove(index)" x-bind:aria-label="'Quitar ' + file.name">Quitar</button>
                                </li>
                            </template>
                        </ul>
                    </div>
                </section>
                <section class="sp-upload-field" aria-labelledby="sp-upload-notes-title">
                    <div class="sp-upload-heading"><span class="sp-upload-number" aria-hidden="true">02</span><h2 id="sp-upload-notes-title"><label for="additional_information">Indicaciones para la revisión</label></h2></div>
                    <p class="sp-upload-description">Explica el objetivo del documento, las prioridades y los requisitos de tu universidad o revista.</p>
                    <textarea id="additional_information" name="additional_information" rows="7" maxlength="8000" class="sp-upload-textarea"
                        x-ref="notes" x-on:input="notes = $event.target.value; error = ''"
                        placeholder="Por ejemplo: revisar la coherencia de los objetivos, ajustar las citas a APA 7 y priorizar el capítulo de metodología. Puedes incluir enlaces de referencia."
                        aria-describedby="sp-upload-notes-help">{{ old('additional_information') }}</textarea>
                    <div id="sp-upload-notes-help" class="sp-upload-help"><span>Hasta 8.000 caracteres</span><span x-cloak x-text="notes.length.toLocaleString('es-CO') + ' / 8.000'"></span></div>
                    <p class="sp-upload-hint">Puedes enviar archivos, indicaciones o ambos. Tus notas quedarán guardadas para el seguimiento del proyecto.</p>
                </section>
            </div>
            <div class="sp-upload-alert is-error" role="alert" x-show="error" x-text="error" x-cloak></div>
            <div class="sp-upload-send">
                <div><h3>Revisa y envía</h3><p>Comprueba los archivos seleccionados y tus indicaciones antes de confirmar.</p></div>
                <button type="submit" class="sp-upload-submit" x-bind:disabled="submitting">
                    <span x-text="submitting ? 'Enviando…' : 'Enviar al equipo'">Enviar al equipo</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14m-6-6 6 6-6 6" /></svg>
                </button>
            </div>
        </form>

        <section class="sp-upload-section" aria-labelledby="sp-upload-library-title">
            <header class="sp-upload-section-head"><h2 id="sp-upload-library-title">Documentos del proyecto</h2><p>Consulta los archivos que has compartido y las versiones que recibes durante el proceso.</p></header>
            <div class="sp-upload-grid">
                @foreach([
                    ['title' => 'Tus archivos enviados', 'files' => $documents, 'description' => 'Manuscritos y materiales que has cargado.', 'empty' => 'Tus archivos aparecerán aquí después de enviarlos.'],
                    ['title' => 'Devoluciones del equipo', 'files' => $adminDocuments, 'description' => 'Documentos compartidos por administración o tu asesor.', 'empty' => 'Cuando el equipo comparta una versión o material, podrás abrirlo desde aquí.']
                ] as $group)
                    <article class="sp-upload-card">
                        <div class="sp-upload-card-head"><h3>{{ $group['title'] }}</h3><span class="sp-upload-count" aria-label="{{ $group['files']->count() }} archivos">{{ $group['files']->count() }}</span></div>
                        <p class="sp-upload-description">{{ $group['description'] }}</p>
                        @if($group['files']->isEmpty())
                            <div class="sp-upload-empty">{{ $group['empty'] }}</div>
                        @else
                            <ul class="sp-upload-file-list">
                                @foreach($group['files'] as $document)
                                    @php
                                        $extension = strtolower(pathinfo((string) $document->original_name, PATHINFO_EXTENSION));
                                        $fileUrl = route('documents.file.show', $document);
                                        $isImage = str_starts_with(strtolower((string) $document->mime_type), 'image/');
                                        $badge = $document->documentType?->name ?? strtoupper($extension ?: 'ARCHIVO');
                                    @endphp
                                    <li><a href="{{ $fileUrl }}" target="_blank" rel="noopener noreferrer" class="sp-upload-file-link" aria-label="Abrir {{ $document->original_name }} en una nueva pestaña">
                                        <span class="sp-upload-thumb" aria-hidden="true">
                                            @if($isImage)<img src="{{ $fileUrl }}" alt="" loading="lazy">@else<x-document-type-icon :extension="$extension" class="sp-upload-file-icon" />@endif
                                        </span>
                                        <span class="sp-upload-file-info"><span class="sp-upload-file-name">{{ $document->original_name }}</span><span class="sp-upload-file-meta">{{ $badge }} · {{ $formatFileSize((int) $document->file_size) }}@if($document->uploaded_at) · {{ $document->uploaded_at->format('d/m/Y') }}@endif</span></span>
                                        <span class="sp-upload-open" aria-hidden="true">Abrir ↗</span>
                                    </a></li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="sp-upload-section" aria-labelledby="sp-upload-followup-title">
            <header class="sp-upload-section-head"><h2 id="sp-upload-followup-title">Seguimiento y comunicación</h2><p>Conserva el contexto de tu trabajo y consulta las respuestas del equipo.</p></header>
            <div class="sp-upload-grid">
                <article class="sp-upload-card">
                    <div class="sp-upload-card-head"><h3>Tus indicaciones guardadas</h3><span class="sp-upload-count" aria-label="{{ $additionalInformationEntries->count() }} indicaciones">{{ $additionalInformationEntries->count() }}</span></div>
                    @if($additionalInformationEntries->isEmpty())
                        <div class="sp-upload-empty">Las indicaciones que envíes quedarán registradas aquí para consultarlas después.</div>
                    @else
                        <ul class="sp-upload-history">
                            @foreach($additionalInformationEntries as $entry)
                                <li><div class="sp-upload-history-meta"><span>{{ $entry['date_label'] ?? 'Sin fecha' }}</span>@if(filled($entry['time_label'] ?? null))<span>{{ $entry['time_label'] }}</span>@endif</div><p>{{ $entry['content'] }}</p></li>
                            @endforeach
                        </ul>
                    @endif
                </article>
                <article class="sp-upload-card">
                    <div class="sp-upload-card-head"><h3>Mensajes del equipo</h3><span class="sp-upload-count" aria-label="{{ $adminMessages->count() }} mensajes">{{ $adminMessages->count() }}</span></div>
                    @if($adminMessages->isEmpty())
                        <div class="sp-upload-empty">Aquí recibirás los mensajes de administración y de tu asesor.</div>
                    @else
                        <ul class="sp-upload-history">
                            @foreach($adminMessages as $adminMessage)
                                <li><div class="sp-upload-history-meta"><span>{{ optional($adminMessage->sent_at)->format('d/m/Y') }}</span><span>{{ optional($adminMessage->sent_at)->format('H:i') }}</span><span>Equipo propaps.com</span></div><p>{{ $adminMessage->message }}</p></li>
                            @endforeach
                        </ul>
                    @endif
                </article>
            </div>
        </section>
        <p class="sp-upload-footer">La participación del autor y los insumos que aporta orientan cada etapa. <a href="{{ route('about') }}" class="sp-upload-link">Conoce nuestro modelo de trabajo →</a></p>
    </section>
</x-layouts::app>
