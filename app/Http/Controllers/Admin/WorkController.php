<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdminWorkRequest;
use App\Models\Document;
use App\Models\User;
use App\Models\UserMessage;
use App\Services\DocumentTypeResolver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class WorkController extends Controller
{
    public function __construct(private readonly DocumentTypeResolver $documentTypeResolver)
    {
        //
    }

    public function index(): View
    {
        $users = User::query()
            ->with([
                'activePaidSubscription.paymentPlan:id,name',
            ])
            ->where('role', 'client')
            ->orderBy('name')
            ->orderBy('last_name')
            ->get([
                'id',
                'role',
                'name',
                'last_name',
            ])
            ->sortBy(function (User $user): string {
                $membershipPriority = $user->hasActiveMembership() ? '0' : '1';

                return sprintf(
                    '%s|%s',
                    $membershipPriority,
                    strtolower($user->fullName())
                );
            })
            ->values();

        return view('admin.works.index', [
            'users' => $users,
        ]);
    }

    public function show(User $user): View
    {
        abort_unless($user->isClient(), 404);

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
            ->whereHas('sender', fn ($query) => $query->whereIn('role', ['admin', 'advisor']))
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

    public function store(StoreAdminWorkRequest $request, User $user): RedirectResponse
    {
        abort_unless($user->isClient(), 404);

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
                'document_type_id' => $this->documentTypeResolver->resolveId($uploadedFile->getClientOriginalExtension()),
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
}
