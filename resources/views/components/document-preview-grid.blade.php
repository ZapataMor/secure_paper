@props([
    'documents' => collect(),
    'emptyText' => 'No hay archivos disponibles.',
])

@php
    $documents = $documents instanceof \Illuminate\Support\Collection ? $documents : collect($documents);
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

@if ($documents->isEmpty())
    <p class="mt-3 text-sm text-[#AAC8C4]">{{ $emptyText }}</p>
@else
    <ul class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-4">
        @foreach ($documents as $document)
            @php
                $mimeType = strtolower((string) $document->mime_type);
                $isImage = str_starts_with($mimeType, 'image/');
                $extension = strtolower(pathinfo((string) $document->original_name, PATHINFO_EXTENSION));
                $fileUrl = route('documents.file.show', $document);
                $badge = $document->documentType?->name ?? ($extension !== '' ? strtoupper($extension) : 'ARCHIVO');
            @endphp

            <li class="rounded-xl border border-[#AAC8C4]/25 bg-[#000F1F]/35 p-2">
                <a href="{{ $fileUrl }}" target="_blank" rel="noopener" class="block">
                    <div class="aspect-[4/5] overflow-hidden rounded-lg border border-[#AAC8C4]/25 bg-[#031A19]/75">
                        @if ($isImage)
                            <img
                                src="{{ $fileUrl }}"
                                alt="{{ $document->original_name }}"
                                class="h-full w-full object-cover"
                                loading="lazy"
                            >
                        @else
                            <div class="flex h-full w-full flex-col items-center justify-center gap-3 p-3 text-[#D7FFF2]">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-10 w-10">
                                    <path d="M14 2H7a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7z"/>
                                    <path d="M14 2v5h5"/>
                                </svg>
                                <span class="rounded-full border border-[#2CC295]/50 bg-[#2CC295]/15 px-2 py-1 text-[10px] font-semibold uppercase tracking-wide text-[#2CC295]">
                                    {{ $extension !== '' ? $extension : 'file' }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <p class="mt-2 text-xs font-semibold leading-4 text-[#F1F7F6] break-words">
                        {{ $document->original_name }}
                    </p>
                    <p class="mt-1 text-[11px] leading-4 text-[#AAC8C4]">
                        {{ $badge }} - {{ $formatFileSize((int) $document->file_size) }}
                    </p>
                </a>
            </li>
        @endforeach
    </ul>
@endif
