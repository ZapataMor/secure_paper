<?php
namespace App\Services;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LegalConsent {
    public function record(User $user, string $method): void {
        DB::table('user_activities')->insert([
            'user_id' => $user->id, 'action' => 'legal_terms_accepted', 'module' => 'accounts',
            'description' => json_encode(['terms_version' => '2026-10-04', 'privacy_version' => '2026-10-04', 'registration_method' => $method]),
            'created_at' => now(),
        ]);
    }
}
