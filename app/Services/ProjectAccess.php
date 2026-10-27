<?php
namespace App\Services;

use App\Models\ProjectAssignment;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class ProjectAccess {
    public function ready(): bool { return Schema::hasTable('project_assignments'); }
    public function allows(User $staff, int $clientId): bool {
        if($staff->isAdmin()) return true;
        if(!$staff->isAdvisor() || $staff->status!=='active' || $staff->trashed() || !$this->ready()) return false;
        return ProjectAssignment::where('client_id',$clientId)->where('advisor_id',$staff->id)->exists();
    }
}
