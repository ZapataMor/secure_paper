<?php

namespace App\Services;

use App\Models\DocumentType;

class DocumentTypeResolver
{
    public function resolveId(?string $extension): int
    {
        $extension = strtolower((string) $extension);

        [$name, $description] = match ($extension) {
            'pdf' => ['PDF', 'Documento PDF'],
            'doc', 'docx' => ['Word', 'Documento de Microsoft Word'],
            'xls', 'xlsx' => ['Excel', 'Hoja de calculo de Microsoft Excel'],
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp' => ['Imagen', 'Archivo de imagen'],
            default => ['Otro', 'Archivo cargado por usuario'],
        };

        return DocumentType::query()
            ->firstOrCreate(
                ['name' => $name],
                ['description' => $description]
            )
            ->id;
    }
}
