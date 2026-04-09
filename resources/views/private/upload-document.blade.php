<x-layouts::app :title="__('Cargar documento')">
    @php
        $documents = $documents ?? collect();
        $adminDocuments = $adminDocuments ?? collect();
        $adminMessages = $adminMessages ?? collect();
        $additionalInformationEntries = collect($additionalInformationEntries ?? []);
        $additionalInformationByDate = $additionalInformationEntries->groupBy('date_key');
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
            class="space-y-5"
        >
            @csrf

            <article class="w-full rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                <h2 class="text-lg font-semibold text-[#F1F7F6]">Contenedor de carga de archivos</h2>
                <p class="mt-2 text-sm text-[#AAC8C4]">
                    Puedes cargar la cantidad de archivos que necesites. Formatos permitidos: Word, PDF, Excel e imagenes.
                </p>

                <div class="mt-4 rounded-xl border border-dashed border-[#2CC295]/50 bg-[#000F1F]/35 p-4">
                    <label for="document_files" class="mb-2 block text-sm font-medium text-[#F1F7F6]">
                        Selecciona uno o varios archivos
                    </label>
                    <input
                        id="document_files"
                        type="file"
                        name="document_files[]"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.bmp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/*"
                        class="block w-full max-w-full cursor-pointer rounded-lg border border-[#AAC8C4]/45 bg-[#032221] px-3 py-2 text-sm text-[#F1F7F6] file:mr-3 file:rounded-md file:border-0 file:bg-[#2CC295]/25 file:px-3 file:py-2 file:font-semibold file:text-[#D7FFF2] hover:file:bg-[#2CC295]/35"
                    >
                    <p class="mt-2 text-xs text-[#AAC8C4]">Tamano maximo por archivo: 20 MB.</p>
                </div>
            </article>

            <article class="w-full rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
                <h2 class="text-lg font-semibold text-[#F1F7F6]">Informacion adicional</h2>
                <p class="mt-2 text-sm text-[#AAC8C4]">
                    Escribe notas, contexto de tu trabajo o links de apoyo. Esta informacion tambien la vera el administrador.
                </p>

                <label for="additional_information" class="sr-only">Informacion adicional</label>
                <textarea
                    id="additional_information"
                    name="additional_information"
                    rows="6"
                    placeholder="Ejemplo: Priorizar metodologia, revisar citas APA, link de referencia: https://..."
                    class="mt-4 w-full rounded-lg border border-[#AAC8C4]/45 bg-[#032221] px-3 py-2 text-sm text-[#F1F7F6] placeholder:text-[#AAC8C4]/70 focus:border-[#2CC295] focus:outline-none"
                >{{ old('additional_information') }}</textarea>
            </article>

            <button
                type="submit"
                class="inline-flex items-center justify-center rounded-xl border border-[#2CC295]/65 bg-[#006A4C]/55 px-4 py-2 text-sm font-semibold text-[#F1F7F6] transition hover:bg-[#2CC295]/30"
            >
                Guardar y cargar
            </button>
        </form>

        <article class="w-full rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Archivos cargados</h2>
            <x-document-preview-grid
                :documents="$documents"
                empty-text="Aun no has cargado archivos."
            />
        </article>

        <article class="w-full rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Tu informacion adicional guardada</h2>

            @if ($additionalInformationEntries->isNotEmpty())
                <div class="mt-4 space-y-4">
                    @foreach ($additionalInformationByDate as $entriesForDate)
                        <section class="rounded-xl border border-[#AAC8C4]/25 bg-[#000F1F]/35 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-[#AAC8C4]">
                                {{ $entriesForDate->first()['date_label'] ?? 'Sin fecha' }}
                            </p>

                            <ul class="mt-3 space-y-2">
                                @foreach ($entriesForDate as $entry)
                                    <li class="rounded-lg border border-[#AAC8C4]/20 bg-[#032221]/55 px-3 py-3">
                                        @if (filled($entry['time_label'] ?? null))
                                            <p class="text-xs text-[#AAC8C4]">{{ $entry['time_label'] }}</p>
                                        @endif

                                        <p class="mt-1 whitespace-pre-line break-words text-sm leading-6 text-[#F1F7F6]">
                                            {{ $entry['content'] }}
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endforeach
                </div>
            @else
                <p class="mt-3 text-sm text-[#AAC8C4]">
                    Aun no has agregado informacion adicional.
                </p>
            @endif
        </article>

        <article class="w-full rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Archivos enviados por administracion</h2>
            <x-document-preview-grid
                :documents="$adminDocuments"
                empty-text="Aun no hay archivos enviados por el administrador."
            />
        </article>

        <article class="w-full rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Mensajes recibidos del administrador</h2>

            @if ($adminMessages->isEmpty())
                <p class="mt-3 text-sm text-[#AAC8C4]">Aun no hay mensajes del administrador.</p>
            @else
                <ul class="mt-4 space-y-3">
                    @foreach ($adminMessages as $adminMessage)
                        <li class="rounded-xl border border-[#AAC8C4]/25 bg-[#000F1F]/35 px-4 py-3">
                            <p class="text-xs text-[#AAC8C4]">
                                {{ optional($adminMessage->sent_at)->format('d/m/Y H:i') }}
                            </p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm text-[#F1F7F6]">
                                {{ $adminMessage->message }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </article>
    </section>
</x-layouts::app>
