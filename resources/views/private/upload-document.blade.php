<x-layouts::app :title="__('Cargar documento')">
    @php
        $documents = $documents ?? collect();
        $adminDocuments = $adminDocuments ?? collect();
        $adminMessages = $adminMessages ?? collect();
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

    <section class="sp-admin-page mx-auto w-full max-w-5xl">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Modulo</p>
            <h1>Cargar documento</h1>
            <p>Sube tus archivos y deja informacion adicional para el equipo.</p>
        </header>

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

        <form
            method="POST"
            action="{{ route('private.upload-document.store') }}"
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
                        <h2 class="text-lg font-semibold text-[#F1F7F6]">Contenedor de carga de archivos</h2>
                    </div>

                    <div class="px-5 pt-5 pb-1">
                        <p class="text-sm text-[#AAC8C4]">
                            Puedes cargar la cantidad de archivos que necesites. Formatos permitidos: Word, PDF, Excel e imagenes.
                        </p>

                        <div class="mt-4 rounded-xl border-2 border-dashed border-[#2CC295]/45 bg-[#000F1F]/35 px-4 py-6 text-center">
                            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full border border-[#2CC295]/40 bg-[#006A4C]/18">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" class="h-7 w-7 text-[#2CC295]">
                                    <path d="M12 16V4"/>
                                    <path d="m7 9 5-5 5 5"/>
                                    <path d="M20 16.5a4.5 4.5 0 0 1-4.5 4.5h-7A4.5 4.5 0 0 1 4 16.5"/>
                                </svg>
                            </div>

                            <p class="text-base font-semibold text-[#F1F7F6]">Arrastra tus archivos aqui o selecciona desde tu equipo</p>
                            <p class="mt-2 text-sm text-[#AAC8C4]">
                                Formatos permitidos: Word, PDF, Excel e imagenes.<br>
                                Tamano maximo por archivo: 20 MB.
                            </p>

                            <input
                                id="document_files"
                                type="file"
                                name="document_files[]"
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
                        <h2 class="text-lg font-semibold text-[#F1F7F6]">Informacion adicional</h2>
                    </div>

                    <div class="p-5">
                        <p class="text-sm text-[#AAC8C4]">
                            Escribe notas, contexto de tu trabajo o links de apoyo. Esta informacion tambien la vera el administrador.
                        </p>

                        <label for="additional_information" class="sr-only">Informacion adicional</label>
                        <textarea
                            id="additional_information"
                            name="additional_information"
                            rows="6"
                            placeholder="Ejemplo: Priorizar metodologia, revisar citas APA, link de referencia: https://..."
                            class="mt-4 min-h-[196px] w-full rounded-xl border border-[#006A4C]/55 bg-[#011E15]/95 px-4 py-3 text-sm text-[#F1F7F6] placeholder:text-[#AAC8C4]/55 focus:border-[#2CC295] focus:outline-none"
                        >{{ old('additional_information') }}</textarea>

                        <button
                            type="submit"
                            class="mt-3 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-[#2CC295]/60 bg-[#2CC295]/95 px-4 py-3 text-sm font-bold text-[#000F1F] transition hover:bg-[#00BF81]"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="h-4 w-4">
                                <path d="M20 6 9 17l-5-5"/>
                            </svg>
                            Guardar y cargar archivos
                        </button>
                    </div>
                </article>
            </div>
        </form>

        <div class="h-px w-full bg-gradient-to-r from-transparent via-[#006A4C]/55 to-transparent"></div>

        <article class="overflow-hidden rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-xl font-semibold text-[#F1F7F6]">Archivos cargados</h2>
                <span class="inline-flex items-center rounded-full border border-[#006A4C]/55 bg-[#006A4C]/25 px-2.5 py-1 text-xs font-semibold text-[#2CC295]">
                    {{ $documents->count() }}
                </span>
            </div>

            @if ($documents->isEmpty())
                <p class="mt-3 text-sm text-[#AAC8C4]">Aun no has cargado archivos.</p>
            @else
                <ul class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
                    @foreach ($documents as $document)
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
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10 text-[#2CC295]">
                                                <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"/>
                                                <path d="M14 2v5h5"/>
                                            </svg>
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
                <h2 class="text-xl font-semibold text-[#F1F7F6]">Archivos enviados por administracion</h2>
                <span class="inline-flex items-center rounded-full border border-[#2CC295]/45 bg-[#2CC295]/18 px-2.5 py-1 text-xs font-semibold text-[#2CC295]">
                    {{ $adminDocuments->count() }}
                </span>
            </div>

            @if ($adminDocuments->isEmpty())
                <p class="mt-3 text-sm text-[#AAC8C4]">Aun no hay archivos enviados por el administrador.</p>
            @else
                <ul class="mt-4 grid grid-cols-2 gap-3 md:grid-cols-3 xl:grid-cols-5">
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
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10 text-[#2CC295]">
                                                <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"/>
                                                <path d="M14 2v5h5"/>
                                            </svg>
                                        </div>
                                    @endif

                                    <span class="absolute right-2 top-2 rounded-full bg-[#2CC295] px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide text-[#000F1F]">
                                        Nuevo
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
                <h2 class="text-lg font-semibold text-[#F1F7F6]">Tu informacion adicional guardada</h2>

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
                        Aun no has agregado informacion adicional.
                    </p>
                @endif
            </article>

            <article class="rounded-2xl border border-[#006A4C]/45 bg-[#032221]/78 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                <h2 class="text-lg font-semibold text-[#F1F7F6]">Mensajes recibidos del administrador</h2>

                @if ($adminMessages->isEmpty())
                    <p class="mt-3 text-sm text-[#AAC8C4]">Aun no hay mensajes del administrador.</p>
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
                                    <span class="ml-auto text-[#AAC8C4]">Soporte Secure Papers</span>
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
