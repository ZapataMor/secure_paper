<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class DocumentSubmissionFingerprint
{
    /** @param array<int, UploadedFile> $files */
    public function make(?string $notes, array $files): string
    {
        $items = array_map(function (UploadedFile $file): array {
            $hash = hash_file('sha256', $file->getRealPath());
            if ($hash === false) {
                throw new \RuntimeException('No se pudo identificar el archivo recibido.');
            }

            return ['name' => $file->getClientOriginalName(), 'hash' => $hash];
        }, $files);
        // La selección de los mismos archivos en otro orden sigue siendo el mismo envío.
        usort($items, fn ($a, $b) => [$a['name'], $a['hash']] <=> [$b['name'], $b['hash']]);

        return hash('sha256', json_encode(['notes' => $notes, 'files' => $items], JSON_THROW_ON_ERROR));
    }
}
