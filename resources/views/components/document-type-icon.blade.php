@props([
    'extension' => '',
    'class' => 'h-12 w-12',
])

@php
    $extension = strtolower(ltrim((string) $extension, '.'));

    $icon = match ($extension) {
        'doc', 'docx' => [
            'label' => 'Documento Word',
            'text' => 'DOC',
            'fill' => '#185ABD',
            'bar' => '#103F91',
            'line' => '#D8E7FF',
        ],
        'xls', 'xlsx' => [
            'label' => 'Documento Excel',
            'text' => 'XLS',
            'fill' => '#107C41',
            'bar' => '#0B5D31',
            'line' => '#D7F5E4',
        ],
        'pdf' => [
            'label' => 'Documento PDF',
            'text' => 'PDF',
            'fill' => '#D93025',
            'bar' => '#A61F17',
            'line' => '#FFE0DD',
        ],
        default => [
            'label' => 'Archivo',
            'text' => $extension !== '' ? strtoupper(substr($extension, 0, 3)) : 'FILE',
            'fill' => '#64748B',
            'bar' => '#334155',
            'line' => '#E2E8F0',
        ],
    };
@endphp

<svg
    viewBox="0 0 64 64"
    role="img"
    aria-label="{{ $icon['label'] }}"
    class="{{ $class }}"
>
    <path d="M14 4h25l11 11v43a2 2 0 0 1-2 2H14a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" fill="{{ $icon['fill'] }}"/>
    <path d="M39 4v12h11" fill="#FFFFFF" opacity=".32"/>
    <path d="M22 21h20M22 27h20M22 33h13" stroke="{{ $icon['line'] }}" stroke-width="3" stroke-linecap="round" opacity=".88"/>
    <rect x="8" y="38" width="48" height="16" rx="4" fill="{{ $icon['bar'] }}"/>
    <text x="32" y="49.5" fill="#FFFFFF" font-family="Arial, Helvetica, sans-serif" font-size="10" font-weight="700" text-anchor="middle">{{ $icon['text'] }}</text>
</svg>
