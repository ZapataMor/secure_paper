<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
{
    public function view(User $user, Document $document): bool
    {
        if ($user->isAdmin() || $user->isAdvisor()) {
            return true;
        }

        if (! $user->hasActiveMembership()) {
            return false;
        }

        return $document->user_id === $user->id
            || $document->uploaded_by === $user->id;
    }
}
