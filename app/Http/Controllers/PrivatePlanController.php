<?php

namespace App\Http\Controllers;

use App\Models\PaymentPlan;
use App\Models\UserSubscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrivatePlanController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $plansByName = PaymentPlan::query()
            ->where('status', 'active')
            ->get()
            ->keyBy('name');

        $pendingSubscription = $user->userSubscriptions()
            ->with('paymentPlan:id,name')
            ->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->latest('updated_at')
            ->first();

        return view('private.planes', [
            'plansByName' => $plansByName,
            'hasActiveMembership' => $user->hasActiveMembership(),
            'activePlanName' => $user->activeMembershipPlanName(),
            'pendingSubscription' => $pendingSubscription,
        ]);
    }

    public function select(Request $request, PaymentPlan $paymentPlan): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->isClient(), 403);
        abort_unless($paymentPlan->status === 'active', 404);

        if ($user->hasActiveMembership()) {
            return redirect()
                ->route('private.upload-document')
                ->with('status', 'Ya tienes una membresia activa.');
        }

        UserSubscription::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_status' => 'pending',
            ],
            [
                'payment_plan_id' => $paymentPlan->id,
                'start_date' => now()->toDateString(),
                'end_date' => now()->addDays((int) $paymentPlan->duration_days)->toDateString(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'amount_paid' => $paymentPlan->price,
            ]
        );

        return redirect()
            ->route('private.planes')
            ->with('status', 'Solicitud de plan registrada. El acceso a carga se habilitara cuando el pago quede aprobado.');
    }
}
