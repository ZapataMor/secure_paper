<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentQuote;
use App\Models\PaymentPlan;
use App\Models\UserSubscription;
use App\Models\WompiOrder;
use App\Services\ServiceAccessPolicy;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Services\AdditionalQuotePricing;
use App\Services\WompiGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PaymentQuoteController extends Controller
{
    public function index()
    {
        return view('admin.payment-quotes', [
            'installed' => Schema::hasTable('payment_quotes'),
            'clients' => User::where('role', 'client')->orderBy('name')->get(),
            'quotes' => Schema::hasTable('payment_quotes') ? PaymentQuote::latest()->limit(30)->get() : collect(),
            'serviceAccess' => UserSubscription::with(['user', 'paymentPlan'])->whereHas('paymentPlan', fn ($q) => $q->whereIn('name', [app(ServiceAccessPolicy::class)->planName('correccion-completa'), app(ServiceAccessPolicy::class)->planName('grandes-volumenes')]))->latest('id')->limit(50)->get(),
        ]);
    }

    public function access(Request $request, ServiceAccessPolicy $policy)
    {
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'client')->whereNull('deleted_at')],
            'package' => ['required', Rule::in(['correccion-completa', 'grandes-volumenes'])],
            'subscription_id' => ['nullable', 'integer'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date', 'after_or_equal:today'],
        ]);
        DB::transaction(function () use ($data, $policy) {
            User::whereKey($data['user_id'])->lockForUpdate()->firstOrFail();
            if (! empty($data['subscription_id'])) {
                $subscription = UserSubscription::whereKey($data['subscription_id'])->where('user_id', $data['user_id'])
                    ->whereHas('paymentPlan', fn ($q) => $q->where('name', $policy->planName($data['package'])))
                    ->lockForUpdate()->firstOrFail();
                if (in_array($subscription->status, ['cancelled', 'canceled', 'voided'], true)) {
                    throw ValidationException::withMessages(['subscription_id' => 'No se puede actualizar un servicio cancelado.']);
                }
                if (WompiOrder::where('user_subscription_id', $subscription->id)->where('status', 'PENDING')->where('expires_at', '>', now())->exists()) {
                    throw ValidationException::withMessages(['end_date' => 'Hay un pago pendiente para este acuerdo. Espera su confirmación o vencimiento antes de cambiar las fechas.']);
                }
                // Cambiar fechas nunca cambia el estado del pago ni activa un servicio sin pagar.
                $subscription->update(['start_date' => $data['start_date'], 'end_date' => $data['end_date']]);
            } else {
                $plan = PaymentPlan::firstOrCreate(['name' => $policy->planName($data['package'])], [
                    'description' => 'Vigencia acordada por servicio; importe registrado en la orden de pago.',
                    'price' => 0, 'duration_days' => 0, 'max_documents' => 1,
                    'includes_meetings' => true, 'meetings_limit' => 0, 'status' => 'active',
                ]);
                UserSubscription::create(['user_id' => $data['user_id'], 'payment_plan_id' => $plan->id,
                    'start_date' => $data['start_date'], 'end_date' => $data['end_date'],
                    'status' => 'pending', 'payment_status' => 'pending', 'amount_paid' => null]);
            }
        });

        return redirect()->route('admin.payment-quotes')->with('status', 'Período de trabajo y ajustes guardado. La carga requiere un pago confirmado y una fecha vigente.');
    }

    public function store(Request $request, WompiGateway $gateway, AdditionalQuotePricing $pricing)
    {
        abort_unless(Schema::hasTable('payment_quotes'), 503, 'Las cotizaciones todavía están en preparación.');
        $data = $request->validate([
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'client')->whereNull('deleted_at')],
            'package' => ['required', Rule::in(array_keys(config('wompi.catalog')))],
            'description' => ['required', 'string', 'max:500'],
            'additional_type' => ['required', Rule::in(['peer_review', 'extra_words', 'other'])],
            'extra_words' => ['nullable', 'required_if:additional_type,extra_words', 'integer', 'min:1', 'max:1000000'],
            'agreed_usd' => ['nullable', 'required_if:additional_type,other', 'regex:/^[0-9]{1,6}(\.[0-9]{1,2})?$/D', 'numeric', 'min:0.01', 'max:100000'],
            'cop_per_usd' => ['required', 'regex:/^[0-9]{1,6}(\.[0-9]{1,4})?$/D', 'numeric', 'min:0.0001', 'max:100000'],
            'billing_country' => ['required', 'regex:/^[A-Z]{2}$/D'],
            'tax_treatment' => ['required', Rule::in(['colombia_vat', 'export_exempt'])],
            'export_verified' => ['sometimes', 'boolean'],
            'expires_at' => ['required', 'date'],
        ]);
        abort_unless($gateway->canPayAdditional((int) $data['user_id']), 422, 'El cliente debe haber pagado su primer servicio.');
        $amounts = $pricing->calculate($data);
        $expires = Carbon::parse($data['expires_at'], 'America/Bogota')->utc();
        if (! $expires->isFuture()) {
            throw ValidationException::withMessages(['expires_at' => 'El vencimiento debe estar en el futuro.']);
        }
        PaymentQuote::create(array_merge($amounts, [
            'user_id' => $data['user_id'], 'created_by' => $request->user()->id,
            'package' => $data['package'], 'description' => $data['description'],
            'access_days' => 0, 'expires_at' => $expires, 'status' => 'offered',
        ]));

        return redirect()->route('admin.payment-quotes')->with('status', 'Cotización creada. El cliente ya puede consultarla en Pagos.');
    }
}
