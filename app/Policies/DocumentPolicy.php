<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;
use App\Services\ProjectAccess;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->isAdmin() || $user->isAdvisor()) {
            return app(ProjectAccess::class)->allows($user, $document->user_id);
        }

        if (! $user->canViewDocumentHistory()) {
            return false;
        }

        return $document->user_id === $user->id;
    }
}
