<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users = User::query()
            ->with([
                'activePaidSubscription.paymentPlan:id,name',
            ])
            ->orderBy('name')
            ->orderBy('last_name')
            ->get([
                'id',
                'role',
                'name',
                'last_name',
                'email',
                'phone',
                'document_type',
                'document_number',
                'status',
                'created_at',
            ]);

        return view('admin.users.index', [
            'users' => $users,
            'statusChanges' => DB::table('user_activities')->where('action', 'account_status_updated')
                ->orderByDesc('id')->limit(20)->get()->map(function ($entry) {
                    $data = json_decode($entry->description, true) ?? [];
                    $target = User::withTrashed()->find($data['target_user_id'] ?? null);

                    return (object) [
                        'actor' => User::withTrashed()->find($entry->user_id)?->fullName() ?? 'Administrador no disponible',
                        'target' => $target?->fullName() ?? 'Cuenta no disponible',
                        'targetEmail' => $target?->email ?? '',
                        'before' => $data['before'] ?? '', 'after' => $data['after'] ?? '', 'reason' => $data['reason'] ?? '',
                        'date' => Carbon::parse($entry->created_at, config('app.timezone'))->setTimezone(config('app.display_timezone'))->format('d/m/Y H:i'),
                    ];
                }),
        ]);
    }

    public function confirmStatus(Request $request, User $user): View
    {
        $input = $request->validate(['status' => ['required', Rule::in(['active', 'inactive'])]]);

        return view('admin.users.confirm-status', ['targetUser' => $user, 'desiredStatus' => $input['status']]);
    }

    public function updateStatus(Request $request, User $user): RedirectResponse
    {
        $input = $request->validate([
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'expected_status' => ['required', Rule::in(['active', 'inactive'])],
            'expected_epoch' => ['present', 'nullable', 'uuid'],
            'confirmed' => ['required', 'accepted'],
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'confirmed.required' => 'Debes confirmar el cambio de esta cuenta.',
            'confirmed.accepted' => 'Debes confirmar el cambio de esta cuenta.',
            'reason.required' => 'Escribe un motivo para registrar el cambio.',
            'reason.max' => 'El motivo puede tener hasta 500 caracteres.',
        ]);
        $changed = DB::transaction(function () use ($request, $user, $input): bool {
            // Orden fijo para impedir que dos administradores se desactiven mutuamente a la vez.
            $admins = User::where('role', 'admin')->orderBy('id')->lockForUpdate()->get();
            $actor = $admins->firstWhere('id', $request->user()->id);
            abort_unless($actor && $actor->status === 'active', 403);
            $target = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ($input['status'] === 'inactive') {
                if ($target->isAdmin() && $target->status === 'active' && $admins->where('status', 'active')->count() <= 1) {
                    throw ValidationException::withMessages(['status' => 'No puedes desactivar al último administrador activo.']);
                }
                if ($target->id === $actor->id) {
                    throw ValidationException::withMessages(['status' => 'No puedes desactivar tu propia cuenta.']);
                }
            }
            if ($target->status === $input['status']) {
                return false;
            }
            if ($target->status !== $input['expected_status'] || $target->session_epoch !== $input['expected_epoch']) {
                throw ValidationException::withMessages(['status' => 'La cuenta cambió desde que abriste la confirmación. Vuelve al listado y revisa su estado.']);
            }
            $before = $target->status;
            // El guardado normal ejecuta la revocación de todas las sesiones de v24.
            $target->forceFill(['status' => $input['status']])->save();
            DB::table('user_activities')->insert([
                'user_id' => $actor->id, 'action' => 'account_status_updated', 'module' => 'accounts',
                'description' => json_encode(['target_user_id' => $target->id, 'before' => $before,
                    'after' => $target->status, 'reason' => trim($input['reason'])], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
                'created_at' => now(),
            ]);

            return true;
        });

        return redirect()->route('admin.users.index')->with('status', $changed
            ? ($input['status'] === 'inactive' ? 'Cuenta desactivada y sesiones cerradas. El expediente se conserva.' : 'Cuenta reactivada. Debe ingresar de nuevo; el expediente se conserva.')
            : 'La cuenta ya tenía ese estado. No se registró otro cambio.');
    }
}
