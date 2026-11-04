<?php

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSessionObserver
{
    public function updating(User $user): void
    {
        if ($user->isDirty('status') && $user->status !== 'active') {
            // Se guarda junto al estado: reactivar nunca restaura una sesión anterior.
            $user->session_epoch = (string) Str::uuid();
            $user->setRememberToken(Str::random(60));
        }
    }

    public function updated(User $user): void
    {
        if ($user->wasChanged('session_epoch') && config('session.driver') === 'database') {
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)->delete();
        }
    }
}
