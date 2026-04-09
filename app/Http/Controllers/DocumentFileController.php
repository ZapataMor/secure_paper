<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentFileController extends Controller
{
    public function show(Request $request, Document $document): BinaryFileResponse|Response
    {
        $user = $request->user();

        if (! $this->canSeeDocument($user, $document)) {
            abort(403);
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($document->file_path)) {
            abort(404);
        }

        return response()->file($disk->path($document->file_path));
    }

    private function canSeeDocument(?Authenticatable $user, Document $document): bool
    {
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return true;
        }

        return $document->user_id === $user->getAuthIdentifier()
            || $document->uploaded_by === $user->getAuthIdentifier();
    }
}
