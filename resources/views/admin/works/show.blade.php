<x-layouts::app :title="__('Trabajos de usuario')">
    @php
        $additionalInformationEntries = collect($additionalInformationEntries ?? []);
        $additionalInformationByDate = $additionalInformationEntries->groupBy('date_key');
    @endphp

    <section class="sp-admin-page mx-auto w-full max-w-6xl">
        <header class="sp-admin-page-header">
            <p class="sp-admin-page-kicker">Modulo administrador</p>
            <h1>Trabajo de {{ $targetUser->fullName() }}</h1>
            <p>Gestiona archivos y mensajes de seguimiento para este usuario.</p>
        </header>

        <a
            href="{{ route('admin.works.index') }}"
            class="inline-flex w-fit items-center rounded-lg border border-[#AAC8C4]/35 bg-[#000F1F]/35 px-3 py-2 text-xs font-semibold text-[#F1F7F6] transition hover:border-[#2CC295]/65 hover:bg-[#032221]"
        >
            Volver a trabajos
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

        <article class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Informacion adicional del usuario</h2>

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
                    El usuario aun no ha agregado informacion adicional.
                </p>
            @endif
        </article>

        <article class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Archivos cargados por el usuario</h2>
            <x-document-preview-grid
                :documents="$userDocuments"
                empty-text="Este usuario aun no ha cargado archivos."
            />
        </article>

        <article class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Enviar informacion al usuario</h2>

            <form
                method="POST"
                action="{{ route('admin.works.store', $targetUser) }}"
                enctype="multipart/form-data"
                class="mt-4 space-y-5"
            >
                @csrf

                <div>
                    <label for="admin_message" class="mb-2 block text-sm font-medium text-[#F1F7F6]">
                        Mensaje para el usuario
                    </label>
                    <textarea
                        id="admin_message"
                        name="admin_message"
                        rows="5"
                        placeholder="Escribe aqui la informacion que deseas compartir, incluyendo links si aplica."
                        class="w-full rounded-lg border border-[#AAC8C4]/45 bg-[#032221] px-3 py-2 text-sm text-[#F1F7F6] placeholder:text-[#AAC8C4]/70 focus:border-[#2CC295] focus:outline-none"
                    >{{ old('admin_message') }}</textarea>
                </div>

                <div class="rounded-xl border border-dashed border-[#2CC295]/50 bg-[#000F1F]/35 p-4">
                    <label for="admin_files" class="mb-2 block text-sm font-medium text-[#F1F7F6]">
                        Cargar archivos para el usuario
                    </label>
                    <input
                        id="admin_files"
                        type="file"
                        name="admin_files[]"
                        multiple
                        accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.webp,.bmp,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,image/*"
                        class="block w-full cursor-pointer rounded-lg border border-[#AAC8C4]/45 bg-[#032221] px-3 py-2 text-sm text-[#F1F7F6] file:mr-3 file:rounded-md file:border-0 file:bg-[#2CC295]/25 file:px-3 file:py-2 file:font-semibold file:text-[#D7FFF2] hover:file:bg-[#2CC295]/35"
                    >
                    <p class="mt-2 text-xs text-[#AAC8C4]">Tamano maximo por archivo: 20 MB.</p>
                </div>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center rounded-xl border border-[#2CC295]/65 bg-[#006A4C]/55 px-4 py-2 text-sm font-semibold text-[#F1F7F6] transition hover:bg-[#2CC295]/30"
                >
                    Enviar al usuario
                </button>
            </form>
        </article>

        <article class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Archivos enviados por administracion</h2>
            <x-document-preview-grid
                :documents="$adminDocuments"
                empty-text="Aun no has enviado archivos a este usuario."
            />
        </article>

        <article class="rounded-2xl border border-[#006A4C] bg-[#032221]/80 p-5 shadow-[10px_10px_24px_rgba(0,15,31,0.24)]">
            <h2 class="text-lg font-semibold text-[#F1F7F6]">Mensajes enviados al usuario</h2>

            @if ($adminMessages->isEmpty())
                <p class="mt-3 text-sm text-[#AAC8C4]">Aun no hay mensajes enviados.</p>
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
