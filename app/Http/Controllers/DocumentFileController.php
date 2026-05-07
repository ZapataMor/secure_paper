<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentFileController extends Controller
{
    public function show(Request $request, Document $document): BinaryFileResponse|Response
    {
        Gate::authorize('view', $document);

        $disk = Storage::disk('local');

        if (! $disk->exists($document->file_path)) {
            abort(404);
        }

        return response()->file($disk->path($document->file_path));
    }
}
