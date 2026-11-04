<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrivateDocumentRequest;
use App\Models\Document;
use App\Models\User;
use App\Models\UserMessage;
use App\Services\DocumentSubmissionFingerprint;
use App\Services\DocumentTypeResolver;
use App\Services\ProjectAccess;
use App\Services\ProjectEmailNotices;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PrivateDocumentUploadController extends Controller
{
    public function __construct(private readonly DocumentTypeResolver $documentTypeResolver)
    {
        //
    }

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->isClient(), 403);
        if (! $user->canViewDocumentHistory()) {
            return redirect()->route('private.planes')->with('membership_error',
                'Necesitas un servicio pagado vigente o finalizado para consultar el expediente.');
        }

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
            ->with('sender:id,name,last_name')
            ->where('receiver_id', $user->id)
            ->whereHas('sender', fn ($query) => $query->whereIn('role', ['admin', 'advisor']))
            ->latest('sent_at')
            ->latest('id')
            ->get();

        $additionalInformationEntries = $this->formatAdditionalInformationEntries(
            $user->additionalInformationEntries()
        );

        $previousSubmissionId = $request->old('submission_id');

        return view('private.upload-document', [
            'submissionId' => is_string($previousSubmissionId) && Str::isUuid($previousSubmissionId)
                ? strtolower($previousSubmissionId) : (string) Str::uuid(),
            'canUpload' => $user->hasActiveMembership(),
            'documents' => $documents,
            'adminDocuments' => $adminDocuments,
            'adminMessages' => $adminMessages,
            'additionalInformationEntries' => $additionalInformationEntries,
            'projectAssignment' => app(ProjectAccess::class)->ready() ? $user->projectAssignment()->with('advisor:id,name,last_name')->first() : null,
        ]);
    }

    public function store(StorePrivateDocumentRequest $request): RedirectResponse
    {
        $storedPaths = [];
        try {
            return DB::transaction(function () use ($request, &$storedPaths) {
                $user = User::whereKey($request->user()->id)->lockForUpdate()->firstOrFail();

                $additionalInformation = trim((string) $request->input('additional_information'));
                $additionalInformation = $additionalInformation !== '' ? $additionalInformation : null;

                $hasFiles = $request->hasFile('document_files');

                if (! $hasFiles && $additionalInformation === null) {
                    return redirect()
                        ->route('private.upload-document')
                        ->withErrors(['document_files' => 'Debes cargar al menos un archivo o agregar informacion adicional.'])
                        ->withInput();
                }

                $submissionId = strtolower($request->validated('submission_id'));
                $fingerprint = app(DocumentSubmissionFingerprint::class)->make(
                    $additionalInformation, $request->file('document_files', [])
                );
                $receipt = DB::table('document_submissions')
                    ->where('user_id', $user->id)->where('submission_id', $submissionId)->first();
                if ($receipt) {
                    if (! hash_equals($receipt->payload_hash, $fingerprint)) {
                        return redirect()->route('private.upload-document')
                            ->withErrors(['submission_id' => 'Este envío ya fue recibido con otro contenido. Revisa tus indicaciones y selecciona los archivos para realizar un envío nuevo.'])
                            ->withInput($request->only('additional_information'));
                    }

                    return redirect()->route('private.upload-document')
                        ->with('status', 'Este envío ya se había recibido. No se duplicaron archivos ni indicaciones.')
                        ->with('upload_saved', true);
                }
                $fileCount = count($request->file('document_files', []));
                $status = $fileCount > 0
                    ? ($fileCount === 1 ? 'Archivo cargado correctamente.' : 'Archivos cargados correctamente.')
                    : 'Informacion adicional guardada correctamente.';
                // Reserva única dentro de la misma transacción: solo persiste si se guarda todo el envío.
                DB::table('document_submissions')->insert([
                    'user_id' => $user->id, 'submission_id' => $submissionId,
                    'payload_hash' => $fingerprint, 'status_message' => $status,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
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
                    if (! $path) {
                        throw new \RuntimeException('No fue posible guardar el archivo.');
                    }
                    $storedPaths[] = $path;

                    Document::create([
                        'user_id' => $user->id,
                        'document_type_id' => $this->documentTypeResolver->resolveId($uploadedFile->getClientOriginalExtension()),
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

                if (config('project_notifications.enabled')) {
                    app(ProjectEmailNotices::class)->schedule($user->id, 'upload');
                }

                return redirect()
                    ->route('private.upload-document')
                    ->with('status', $status)
                    ->with('upload_saved', true);
            });
        } catch (\Throwable $exception) {
            Storage::disk('local')->delete($storedPaths);
            report($exception);

            return redirect()
                ->route('private.upload-document')
                ->withErrors(['document_files' => 'No pudimos guardar el envío. Tus indicaciones se conservan; vuelve a seleccionar los archivos e inténtalo de nuevo.'])
                ->withInput($request->only('additional_information', 'submission_id'));
        }
    }

    private function formatAdditionalInformationEntries(Collection $entries): Collection
    {
        return $entries
            ->sortByDesc(fn (array $entry): int => ($entry['created_at'] ?? null)?->getTimestamp() ?? -1)
            ->map(function (array $entry): array {
                $createdAt = ($entry['created_at'] ?? null)?->copy()->setTimezone(config('app.display_timezone'));

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
