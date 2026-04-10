<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\User;
use App\Models\UserMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with('role:id,name')
            ->whereHas('role', fn ($query) => $query->where('name', 'client'))
            ->orderBy('name')
            ->orderBy('last_name')
            ->get([
                'id',
                'role_id',
                'name',
                'last_name',
            ]);

        return view('admin.works.index', [
            'users' => $users,
        ]);
    }

    public function show(User $user): View
    {
        abort_unless($user->role?->name === 'client', 404);

        $userDocuments = Document::query()
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
            ->latest('uploaded_at')
            ->latest('id')
            ->get();

        $adminMessages = UserMessage::query()
            ->with('sender:id,name,last_name')
            ->where('receiver_id', $user->id)
            ->whereHas('sender.role', fn ($query) => $query->where('name', 'admin'))
            ->latest('sent_at')
            ->latest('id')
            ->get();

        $additionalInformationEntries = $this->formatAdditionalInformationEntries(
            $user->additionalInformationEntries()
        );

        return view('admin.works.show', [
            'targetUser' => $user,
            'userDocuments' => $userDocuments,
            'adminDocuments' => $adminDocuments,
            'adminMessages' => $adminMessages,
            'additionalInformationEntries' => $additionalInformationEntries,
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        abort_unless($user->role?->name === 'client', 404);

        $request->validate([
            'admin_message' => ['nullable', 'string', 'max:8000'],
            'admin_files' => ['nullable', 'array'],
            'admin_files.*' => ['file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,gif,webp,bmp', 'max:20480'],
        ]);

        $admin = $request->user();
        $adminMessage = trim((string) $request->input('admin_message'));
        $adminMessage = $adminMessage !== '' ? $adminMessage : null;
        $hasFiles = $request->hasFile('admin_files');

        if (! $hasFiles && $adminMessage === null) {
            return redirect()
                ->route('admin.works.show', $user)
                ->withErrors(['admin_files' => 'Debes escribir un mensaje o cargar al menos un archivo.'])
                ->withInput();
        }

        if ($adminMessage !== null) {
            UserMessage::create([
                'sender_id' => $admin->id,
                'receiver_id' => $user->id,
                'subject' => 'Actualizacion de trabajo',
                'message' => $adminMessage,
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        }

        $uploadedCount = 0;

        foreach ($request->file('admin_files', []) as $uploadedFile) {
            $originalName = $uploadedFile->getClientOriginalName();
            $title = pathinfo($originalName, PATHINFO_FILENAME);
            $title = $title !== '' ? $title : $originalName;

            $path = $uploadedFile->store(
                'documents/user-'.$user->id.'/admin-'.$admin->id,
                'local'
            );

            Document::create([
                'user_id' => $user->id,
                'document_type_id' => $this->resolveDocumentTypeId($uploadedFile->getClientOriginalExtension()),
                'uploaded_by' => $admin->id,
                'title' => $title,
                'description' => $adminMessage,
                'file_path' => $path,
                'original_name' => $originalName,
                'mime_type' => (string) ($uploadedFile->getClientMimeType() ?? $uploadedFile->getMimeType() ?? 'application/octet-stream'),
                'file_size' => (int) ($uploadedFile->getSize() ?? 0),
                'status' => 'shared_by_admin',
                'uploaded_at' => now(),
            ]);

            $uploadedCount++;
        }

        $status = $uploadedCount > 0
            ? ($uploadedCount === 1 ? 'Archivo enviado al usuario correctamente.' : 'Archivos enviados al usuario correctamente.')
            : 'Mensaje enviado al usuario correctamente.';

        return redirect()
            ->route('admin.works.show', $user)
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
