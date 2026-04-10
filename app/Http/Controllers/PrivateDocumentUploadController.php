<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentType;
use App\Models\UserMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class PrivateDocumentUploadController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        abort_if($user?->isAdmin(), 403);

        $documents = Document::query()
            ->with('documentType:id,name')
            ->where('user_id', $user->id)
            ->where('uploaded_by', $user->id)
            ->latest('uploaded_at')
            ->latest('id')
            ->get();

        $adminDocuments = Document::query()
            ->with('documentType:id,name')
            ->where('user_id', $user->id)
            ->where('uploaded_by', '!=', $user->id)
            ->where('status', 'shared_by_admin')
            ->latest('uploaded_at')
            ->latest('id')
            ->get();

        $adminMessages = UserMessage::query()
            ->where('receiver_id', $user->id)
            ->whereHas('sender.role', fn ($query) => $query->where('name', 'admin'))
            ->latest('sent_at')
            ->latest('id')
            ->get();

        $additionalInformationEntries = $this->formatAdditionalInformationEntries(
            $user->additionalInformationEntries()
        );

        return view('private.upload-document', [
            'documents' => $documents,
            'adminDocuments' => $adminDocuments,
            'adminMessages' => $adminMessages,
            'additionalInformationEntries' => $additionalInformationEntries,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user?->isAdmin(), 403);

        $request->validate([
            'document_files' => ['nullable', 'array'],
            'document_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,bmp', 'max:20480'],
            'additional_information' => ['nullable', 'string', 'max:8000'],
        ]);

        $additionalInformation = trim((string) $request->input('additional_information'));
        $additionalInformation = $additionalInformation !== '' ? $additionalInformation : null;

        $hasFiles = $request->hasFile('document_files');

        if (! $hasFiles && $additionalInformation === null) {
            return redirect()
                ->route('private.upload-document')
                ->withErrors(['document_files' => 'Debes cargar al menos un archivo o agregar informacion adicional.'])
                ->withInput();
        }

        if ($additionalInformation !== null) {
            $user->appendAdditionalInformation($additionalInformation);
            $user->save();
        }

        $documentDescription = $additionalInformation ?? $user->latestAdditionalInformationContent();

        $uploadedCount = 0;

        foreach ($request->file('document_files', []) as $uploadedFile) {
            $originalName = $uploadedFile->getClientOriginalName();
            $title = pathinfo($originalName, PATHINFO_FILENAME);
            $title = $title !== '' ? $title : $originalName;

            $path = $uploadedFile->store(
                'documents/user-'.$user->id,
                'local'
            );

            Document::create([
                'user_id' => $user->id,
                'document_type_id' => $this->resolveDocumentTypeId($uploadedFile->getClientOriginalExtension()),
                'uploaded_by' => $user->id,
                'title' => $title,
                'description' => $documentDescription,
                'file_path' => $path,
                'original_name' => $originalName,
                'mime_type' => (string) ($uploadedFile->getClientMimeType() ?? $uploadedFile->getMimeType() ?? 'application/octet-stream'),
                'file_size' => (int) ($uploadedFile->getSize() ?? 0),
                'uploaded_at' => now(),
            ]);

            $uploadedCount++;
        }

        $status = $uploadedCount > 0
            ? ($uploadedCount === 1 ? 'Archivo cargado correctamente.' : 'Archivos cargados correctamente.')
            : 'Informacion adicional guardada correctamente.';

        return redirect()
            ->route('private.upload-document')
            ->with('status', $status);
    }

    private function formatAdditionalInformationEntries(Collection $entries): Collection
    {
        return $entries
            ->sortByDesc(fn (array $entry): int => ($entry['created_at'] ?? null)?->getTimestamp() ?? -1)
            ->map(function (array $entry): array {
                $createdAt = $entry['created_at'] ?? null;

                return [
                    'content' => $entry['content'],
                    'date_key' => $createdAt?->format('Y-m-d') ?? 'sin-fecha',
                    'date_label' => $createdAt?->format('d/m/Y') ?? 'Sin fecha',
                    'time_label' => $createdAt?->format('H:i') ?? null,
                ];
            })
            ->values();
    }

    private function resolveDocumentTypeId(?string $extension): int
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
