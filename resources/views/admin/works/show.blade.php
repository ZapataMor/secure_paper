<x-layouts::app :title="__('Expediente del proyecto')">

    <style>
    .sp-work-show,.sp-work-show form,.sp-work-show article,.sp-work-show section,.sp-work-show .grid>*{min-width:0;max-width:100%}
    .sp-work-show h1,.sp-work-show h2,.sp-work-show p,.sp-work-show label,.sp-work-show span{overflow-wrap:anywhere}
    .sp-work-show input,.sp-work-show textarea,.sp-work-show select{min-width:0;max-width:100%;box-sizing:border-box}
    .sp-work-show input[type=file]{width:100%;font-size:13px}
    .sp-work-show a:focus-visible,.sp-work-show button:focus-visible,.sp-work-show input:focus-visible{outline:2px solid #2cc295;outline-offset:3px}
    @media(max-width:440px){.sp-work-show .sp-work-files{grid-template-columns:1fr}.sp-work-show input[type=file]::file-selector-button{display:block;margin-bottom:8px}.sp-work-show button{white-space:normal}}
    </style>
    @php
        $additionalInformationEntries = collect($additionalInformationEntries ?? []);
        $formatFileSize = static function (int $bytes): string {
            if ($bytes < 1024) {
                return $bytes.' B';
            }

            if ($bytes < 1024 * 1024) {
                return number_format($bytes / 1024, 1).' KB';
            }

            return number_format($bytes / (1024 * 1024), 2).' MB';
        };
    @endphp

    <section class="sp-admin-page sp-work-show mx-auto w-full max-w-6xl">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">{{ auth()->user()->isAdmin() ? 'Administración' : 'Panel del asesor' }}</p>
            <h1>Proyecto de {{ $targetUser->fullName() }}</h1>
            <p>Consulta el expediente completo y comparte correcciones y mensajes de seguimiento.</p>
        </header>

        <a
            href="{{ route('admin.works.index') }}"
            class="inline-flex w-fit items-center rounded-lg border border-[#AAC8C4]/35 bg-[#000F1F]/35 px-3 py-2 text-xs font-semibold text-[#F1F7F6] transition hover:border-[#2CC295]/65 hover:bg-[#032221]"
        >
            Volver a proyectos
        </a>

        @if (session('status'))
            <div class="rounded-xl border border-[#2CC295]/50 bg-[#2CC295]/15 px-4 py-3 text-sm text-[#D7FFF2]">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-xl border border-red-300/55 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @include('partials.project-card',['projectEditable'=>true])

        <form
            method="POST"
            action="{{ route('admin.works.store', $targetUser) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

            <div class="grid grid-cols-1 gap-6 xl:grid-cols-12">
                <article class="xl:col-span-7 overflow-hidden rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                    <div class="flex items-center gap-2 border-b border-[#006A4C]/35 bg-[#006A4C]/10 px-5 py-4">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="h-5 w-5 text-[#2CC295]">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <path d="m17 8-5-5-5 5"/>
                            <path d="M12 3v12"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-[#F1F7F6]">Compartir documentos</h2>
                    </div>

                    <div class="p-5">
                        <p class="text-sm text-[#AAC8C4]">
                            Comparte correcciones o materiales del proyecto con el cliente.
                        </p>

                        <div class="mt-4 rounded-xl border-2 border-dashed border-[#2CC295]/45 bg-[#000F1F]/35 px-4 py-6 text-center">
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full border border-[#2CC295]/40 bg-[#006A4C]/18">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="h-7 w-7 text-[#2CC295]">
                                    <path d="M12 16V4"/>
                                    <path d="m7 9 5-5 5 5"/>
                                    <path d="M20 16.5a4.5 4.5 0 0 1-4.5 4.5h-7A4.5 4.5 0 0 1 4 16.5"/>
                                </svg>
                            </div>

                            <p class="text-base font-semibold text-[#F1F7F6]">Selecciona los documentos que deseas compartir</p>
                            <p class="mt-2 text-sm text-[#AAC8C4]">
                                Formatos permitidos: Word, PDF, Excel e imágenes.<br>
                                Tamaño máximo por archivo: 20 MB.
                            </p>

                            <label for="admin_files" class="sr-only">Documentos para compartir con el cliente</label>
                            <input
                                id="admin_files"
                                type="file"
                                name="admin_files[]"
                                multiple
                                accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.bmp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/*"
                                class="mt-4 block w-full cursor-pointer rounded-lg border border-[#AAC8C4]/45 bg-[#032221] px-3 py-2 text-sm text-[#F1F7F6] file:mr-3 file:rounded-md file:border-0 file:bg-[#006A4C] file:px-3 file:py-2 file:font-semibold file:text-[#F1F7F6] hover:file:bg-[#2CC295]/40"
                            >
                        </div>
                    </div>
                </article>

                <article class="xl:col-span-5 overflow-hidden rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                    <div class="flex items-center gap-2 border-b border-[#006A4C]/35 bg-[#006A4C]/10 px-5 py-4">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="h-5 w-5 text-[#2CC295]">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                        <h2 class="text-lg font-semibold text-[#F1F7F6]">Mensaje de seguimiento</h2>
                    </div>

                    <div class="p-5">
                        <p class="text-sm text-[#AAC8C4]">
                            Escribe observaciones o próximos pasos para orientar al cliente.
                        </p>

                        <label for="admin_message" class="sr-only">Mensaje para el cliente</label>
                        <textarea
                            id="admin_message"
                            name="admin_message"
                            maxlength="8000"
                            aria-describedby="message-help"
                            rows="6"
                            placeholder="Escribe tus indicaciones e incluye enlaces si son necesarios."
                            class="mt-4 min-h-[196px] w-full rounded-xl border border-[#006A4C]/55 bg-[#011E15]/95 px-4 py-3 text-sm text-[#F1F7F6] placeholder:text-[#AAC8C4]/55 focus:border-[#2CC295] focus:outline-none"
                        >{{ old('admin_message') }}</textarea>
                        <p id="message-help" class="mt-2 text-xs text-[#AAC8C4]">Hasta 8.000 caracteres, incluidos espacios. Puedes compartir archivos, un mensaje o ambos.</p>

                        <button
                            type="submit"
                            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#2CC295]/60 bg-[#2CC295]/95 px-4 py-3 text-sm font-bold text-[#000F1F] transition hover:bg-[#00BF81]"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                            Compartir con el cliente
                        </button>
                    </div>
                </article>
            </div>
        </form>

        <div class="h-px w-full bg-gradient-to-r from-transparent via-[#006A4C]/55 to-transparent"></div>

        <article class="overflow-hidden rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-[#F1F7F6]">Documentos del cliente</h2>
                <span class="inline-flex items-center rounded-full border border-[#006A4C]/55 bg-[#006A4C]/25 px-2.5 py-1 text-xs font-semibold text-[#2CC295]">
                    {{ $userDocuments->count() }}
                </span>
            </div>

            @if ($userDocuments->isEmpty())
                <p class="mt-3 text-sm text-[#AAC8C4]">Todavía no hay documentos del cliente en este expediente. Aparecerán aquí cuando los cargue.</p>
            @else
                <ul class="sp-work-files mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
                    @foreach ($userDocuments as $document)
                        @php
                            $mimeType = strtolower((string) $document->mime_type);
                            $isImage = str_starts_with($mimeType, 'image/');
                            $extension = strtolower(pathinfo((string) $document->original_name, PATHINFO_EXTENSION));
                            $fileUrl = route('documents.file.show', $document);
                            $badge = $document->documentType?->name ?? ($extension !== '' ? strtoupper($extension) : 'ARCHIVO');
                        @endphp

                        <li class="group overflow-hidden rounded-xl border border-[#006A4C]/35 bg-[#032A1E]/82 transition hover:border-[#2CC295]/60">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="block">
                                <div class="relative h-28 overflow-hidden border-b border-[#006A4C]/35 bg-[#011E15]">
                                    @if ($isImage)
                                        <img
                                            src="{{ $fileUrl }}"
                                            alt="{{ $document->original_name }}"
                                            class="h-full w-full object-cover opacity-70 transition group-hover:opacity-100"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <x-document-type-icon :extension="$extension" class="h-12 w-12" />
                                        </div>
                                    @endif

                                    <span class="absolute right-2 top-2 rounded-full bg-[#006A4C]/75 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-[#F1F7F6]">
                                        {{ $extension !== '' ? $extension : 'file' }}
                                    </span>
                                </div>

                                <div class="p-3">
                                    <p class="truncate text-sm font-medium text-[#F1F7F6]" title="{{ $document->original_name }}">
                                        {{ $document->original_name }}
                                    </p>
                                    <div class="mt-1 flex items-center justify-between text-xs">
                                        <span class="uppercase text-[#AAC8C4]">{{ $badge }}</span>
                                        <span class="text-[#7AA39E]">{{ $formatFileSize((int) $document->file_size) }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <article class="overflow-hidden rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-[#F1F7F6]">Documentos compartidos por el equipo</h2>
                <span class="inline-flex items-center rounded-full border border-[#2CC295]/45 bg-[#2CC295]/18 px-2.5 py-1 text-xs font-semibold text-[#2CC295]">
                    {{ $adminDocuments->count() }}
                </span>
            </div>

            @if ($adminDocuments->isEmpty())
                <p class="mt-3 text-sm text-[#AAC8C4]">El equipo aún no ha compartido documentos. Usa el formulario superior para enviar correcciones o materiales.</p>
            @else
                <ul class="sp-work-files mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
                    @foreach ($adminDocuments as $document)
                        @php
                            $mimeType = strtolower((string) $document->mime_type);
                            $isImage = str_starts_with($mimeType, 'image/');
                            $extension = strtolower(pathinfo((string) $document->original_name, PATHINFO_EXTENSION));
                            $fileUrl = route('documents.file.show', $document);
                            $badge = $document->documentType?->name ?? ($extension !== '' ? strtoupper($extension) : 'ARCHIVO');
                        @endphp

                        <li class="group overflow-hidden rounded-xl border border-[#2CC295]/30 bg-gradient-to-b from-[#006A4C]/18 to-[#032A1E]/80 shadow-[0_0_15px_rgba(44,194,149,0.08)] transition hover:border-[#2CC295]/65">
                            <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="block">
                                <div class="relative h-28 overflow-hidden border-b border-[#2CC295]/28 bg-[#011E15]">
                                    @if ($isImage)
                                        <img
                                            src="{{ $fileUrl }}"
                                            alt="{{ $document->original_name }}"
                                            class="h-full w-full object-cover opacity-75 transition group-hover:opacity-100"
                                            loading="lazy"
                                        >
                                    @else
                                        <div class="flex h-full w-full items-center justify-center">
                                            <x-document-type-icon :extension="$extension" class="h-12 w-12" />
                                        </div>
                                    @endif

                                    <span class="absolute right-2 top-2 rounded-full bg-[#2CC295] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#000F1F]">
                                        Compartido
                                    </span>
                                </div>

                                <div class="p-3">
                                    <p class="truncate text-sm font-medium text-[#F1F7F6]" title="{{ $document->original_name }}">
                                        {{ $document->original_name }}
                                    </p>
                                    <div class="mt-1 flex items-center justify-between text-xs">
                                        <span class="uppercase text-[#2CC295]">{{ $badge }}</span>
                                        <span class="text-[#AAC8C4]">{{ $formatFileSize((int) $document->file_size) }}</span>
                                    </div>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>

        <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <article class="rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                <h2 class="text-lg font-semibold text-[#F1F7F6]">Indicaciones del cliente</h2>

                @if ($additionalInformationEntries->isNotEmpty())
                    <ul class="mt-4 space-y-3">
                        @foreach ($additionalInformationEntries as $entry)
                            <li class="rounded-xl border border-[#006A4C]/30 bg-[#032A1E]/45 px-4 py-4 transition hover:bg-[#032A1E]/65">
                                <div class="flex flex-wrap items-center gap-2 text-xs text-[#AAC8C4]">
                                    <span class="rounded-md bg-[#011E15] px-2 py-1">{{ $entry['date_label'] ?? 'Sin fecha' }}</span>

                                    @if (filled($entry['time_label'] ?? null))
                                        <span class="rounded-md bg-[#011E15] px-2 py-1">{{ $entry['time_label'] }}</span>
                                    @endif
                                </div>

                                <p class="mt-3 whitespace-pre-line break-words text-sm leading-6 text-[#F1F7F6]">
                                    {{ $entry['content'] }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-3 text-sm text-[#AAC8C4]">
                        Todavía no hay indicaciones del cliente. Cuando las agregue, podrás consultarlas aquí con su fecha.
                    </p>
                @endif
            </article>

            <article class="rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                <h2 class="text-lg font-semibold text-[#F1F7F6]">Historial de mensajes del equipo</h2>

                @if ($adminMessages->isEmpty())
                    <p class="mt-3 text-sm text-[#AAC8C4]">Todavía no se han enviado mensajes. Los que compartas aparecerán aquí con su fecha y remitente.</p>
                @else
                    <ul class="mt-4 space-y-3">
                        @foreach ($adminMessages as $adminMessage)
                            <li class="relative overflow-hidden rounded-xl border border-[#2CC295]/30 bg-gradient-to-r from-[#006A4C]/10 to-[#032A1E]/45 px-4 py-4 pl-5 shadow-[0_4px_18px_rgba(44,194,149,0.07)]">
                                <span class="absolute inset-y-0 left-0 w-1 bg-[#2CC295]"></span>
                                <div class="flex flex-wrap items-center gap-2 text-xs">
                                    <span class="rounded-md bg-[#006A4C]/25 px-2 py-1 text-[#2CC295]">
                                        {{ optional($adminMessage->sent_at)->format('d/m/Y') }}
                                    </span>
                                    <span class="rounded-md bg-[#006A4C]/25 px-2 py-1 text-[#2CC295]">
                                        {{ optional($adminMessage->sent_at)->format('H:i') }}
                                    </span>
                                    <span class="ml-auto text-[#AAC8C4]">{{ $adminMessage->sender?->fullName() ?? 'Equipo propaps.com' }}</span>
                                </div>
                                <p class="mt-3 whitespace-pre-line break-words text-sm leading-6 text-[#F1F7F6]">
                                    {{ $adminMessage->message }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </article>
        </section>
    </section>
</x-layouts::app>
