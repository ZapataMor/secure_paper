<?php

namespace App\Services;

use App\Models\PaymentPlan;
use App\Models\PaymentQuote;
use App\Models\User;
use App\Models\UserSubscription;
use App\Models\WompiOrder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class WompiGateway
{
    public function canPayAdditional(int $userId): bool
    {
        return UserSubscription::where('user_id', $userId)
            ->whereIn('payment_status', ['paid', 'pagado', 'approved', 'aprobado', 'completed', 'completado'])
            ->whereNotIn('status', ['cancelled', 'canceled', 'voided'])->exists();
    }

    public function ready(): bool
    {
        $env = config('wompi.environment');
        $prefix = $env === 'production' ? 'prod' : 'test';

        return in_array($env, ['sandbox', 'production'], true)
            && str_starts_with((string) config('wompi.public_key'), 'pub_'.$prefix.'_')
            && str_starts_with((string) config('wompi.private_key'), 'prv_'.$prefix.'_')
            && str_starts_with((string) config('wompi.integrity_secret'), $prefix.'_integrity_')
            && str_starts_with((string) config('wompi.events_secret'), $prefix.'_events_');
    }

    public function billingTax(int $userId): ?int
    {
        $user = User::findOrFail($userId);
        if (! $user->payment_billing_verified_at || ! $user->payment_billing_country) {
            return null;
        }
        $tax = $user->payment_tax_basis_points;
        if ($tax === null || ! in_array((int) $tax, [0, 1900], true)) {
            return null;
        }
        if ((int) $tax === 0 && ($user->payment_billing_country === 'CO' || ! $user->payment_export_verified)) {
            return null;
        }

        return (int) $tax;
    }

    public function standardAmount(string $package, string $option, ?int $userId = null): ?int
    {
        $usd = config("wompi.catalog.$package.options.$option.usd");
        $tax = $userId ? $this->billingTax($userId) : null;
        $days = $userId ? app(ServiceAccessPolicy::class)->days($userId, $package) : null;
        if (! $usd || $tax === null || $days === null || $days < 0) {
            return null;
        }
        try {
            $subtotal = (int) $usd * 100;

            return app(TrmService::class)->convert($subtotal + intdiv($subtotal * $tax + 5000, 10000), app(TrmService::class)->current());
        } catch (ValidationException) {
            return null;
        }
    }

    public function createOrder(int $userId, array $data): WompiOrder
    {
        abort_unless($this->ready(), 503, 'La pasarela todavía no está configurada.');
        $trm = app(TrmService::class)->current();

        return DB::transaction(function () use ($userId, $data, $trm) {
            User::whereKey($userId)->lockForUpdate()->firstOrFail();
            $quoteId = $data['quote_id'] ?? null;
            $type = $data['additional_type'] ?? null;
            $words = null;
            $units = null;
            $agreement = null;
            if ($quoteId) {
                $quote = PaymentQuote::query()->lockForUpdate()->findOrFail($quoteId);
                abort_unless($quote->user_id === $userId && $this->canPayAdditional($userId), 403);
                abort_unless($quote->status === 'offered' && $quote->expires_at->isFuture(), 409);
                $pending = WompiOrder::where('payment_quote_id', $quote->id)
                    ->where('environment', config('wompi.environment'))->where('status', 'PENDING')->first();
                if ($pending) {
                    return $pending;
                }
                if ($quote->usd_total_cents === null) {
                    throw ValidationException::withMessages(['payment' => 'Este cobro antiguo requiere revisión del administrador.']);
                }
                $package = $quote->package;
                $description = $quote->description;
                $subtotal = (int) $quote->usd_subtotal_cents;
                $taxAmount = (int) $quote->usd_tax_cents;
                $tax = (int) $quote->tax_basis_points;
                $days = 0;
                $type = $quote->additional_type;
                $words = $quote->extra_words;
                $units = $quote->units;
            } else {
                $package = $data['package'];
                $tax = $this->billingTax($userId);
                if ($tax === null) {
                    throw ValidationException::withMessages(['payment' => 'Falta verificar tus datos fiscales. Comunícate con nosotros antes de pagar.']);
                }
                $days = 0;
                if ($type) {
                    abort_unless($this->canPayAdditional($userId), 403, 'Primero debes pagar un servicio.');
                    if ($type === 'extra_words') {
                        $words = (int) ($data['extra_words'] ?? 0);
                        if ($package !== 'correccion-completa' || $words < 1 || $words > 1000000) {
                            throw ValidationException::withMessages(['extra_words' => 'Indica las palabras adicionales de Corrección Completa.']);
                        }
                        $units = intdiv($words + 1999, 2000);
                        $subtotal = $units * 5000;
                        $description = 'Corrección Completa · '.$words.' palabras adicionales';
                    } elseif ($type === 'peer_review') {
                        $units = 1;
                        $subtotal = 16000;
                        $description = 'Pares evaluadores · '.config("wompi.catalog.$package.name");
                    } else {
                        throw ValidationException::withMessages(['payment' => 'Adicional no disponible.']);
                    }
                } else {
                    $option = $data['option'];
                    $usd = config("wompi.catalog.$package.options.$option.usd");
                    $policy = app(ServiceAccessPolicy::class);
                    $days = $policy->days($userId, $package);
                    $agreement = $policy->agreement($userId, $package);
                    if (! $usd || $days === null || $days < 0) {
                        throw ValidationException::withMessages(['package' => 'Antes de pagar, debemos registrar el período de trabajo y ajustes acordado para este servicio.']);
                    }
                    if ($agreement) {
                        // Mantiene fijo el acuerdo que el cliente revisó al iniciar este pago.
                        $agreement = UserSubscription::whereKey($agreement->id)->lockForUpdate()->firstOrFail();
                    }
                    $subtotal = (int) $usd * 100;
                    $description = config("wompi.catalog.$package.name").' · '.config("wompi.catalog.$package.options.$option.name");
                }
                // Ambos adicionales suman el IVA verificado a su tarifa base.
                $taxAmount = intdiv($subtotal * $tax + 5000, 10000);
            }
            $total = $subtotal + $taxAmount;
            if (! $quoteId) {
                $pending = WompiOrder::where('user_id', $userId)->whereNull('payment_quote_id')
                    ->where('environment', config('wompi.environment'))->where('status', 'PENDING')
                    ->where('description', $description)->where('tax_basis_points', $tax)
                    ->where('user_subscription_id', $agreement?->id)
                    ->where('usd_total_cents', $total)->where('expires_at', '>', now())->first();
                if ($pending) {
                    return $pending;
                }
            }
            if ($agreement && WompiOrder::where('user_subscription_id', $agreement->id)
                ->where('environment', config('wompi.environment'))
                ->where('status', 'PENDING')->where('expires_at', '>', now())->exists()) {
                throw ValidationException::withMessages(['payment' => 'Ya hay un intento de pago vigente para este acuerdo. Consúltalo en el estado de tus pagos.']);
            }
            $amount = app(TrmService::class)->convert($total, $trm);
            $expiry = now()->addHour()->min(now('America/Bogota')->endOfDay()->utc());
            if ($quoteId) {
                $expiry = $expiry->min($quote->expires_at);
            }

            return WompiOrder::create([
                'user_id' => $userId, 'payment_quote_id' => $quoteId, 'user_subscription_id' => $agreement?->id, 'reference' => 'PROPAPS-'.Str::uuid(),
                'environment' => config('wompi.environment'), 'package' => $package, 'description' => $description,
                'amount_in_cents' => $amount, 'currency' => 'COP', 'access_days' => $days, 'status' => 'PENDING',
                'is_additional' => (bool) ($quoteId || $type), 'expires_at' => $expiry,
                'usd_subtotal_cents' => $subtotal, 'usd_tax_cents' => $taxAmount, 'usd_total_cents' => $total,
                'tax_basis_points' => $tax, 'cop_rate_units' => $trm['units'], 'trm_valid_from' => $trm['from'],
                'trm_valid_until' => $trm['until'], 'trm_source' => $trm['source'],
                'additional_type' => $type, 'extra_words' => $words, 'additional_units' => $units,
            ]);
        });
    }

    public function integrity(WompiOrder $order): string
    {
        return hash('sha256', $order->reference.$order->amount_in_cents.$order->currency.$order->expires_at->copy()->utc()->toIso8601String().config('wompi.integrity_secret'));
    }

    public function canApplyServiceChanges(WompiOrder $order): bool
    {
        return $order->environment === 'production'
            || ($order->environment === 'sandbox' && config('wompi.sandbox_access') && ! app()->environment('production'));
    }

    public function validEvent(array $event): bool
    {
        $properties = data_get($event, 'signature.properties');
        $checksum = data_get($event, 'signature.checksum');
        if (! is_array($properties) || ! in_array('transaction.id', $properties, true) || ! is_string($checksum) || ! preg_match('/^[a-fA-F0-9]{64}$/D', $checksum) || ! is_int($event['timestamp'] ?? null) || ! config('wompi.events_secret')) {
            return false;
        }
        $values = '';
        foreach ($properties as $path) {
            if (! is_string($path)) {
                return false;
            }
            $value = data_get($event['data'] ?? [], $path);
            if (! is_string($value) && ! is_int($value)) {
                return false;
            }
            $values .= (string) $value;
        }

        return hash_equals(hash('sha256', $values.$event['timestamp'].config('wompi.events_secret')), strtolower($checksum));
    }

    public function transaction(string $id): array
    {
        abort_unless($this->ready(), 503);
        abort_unless((bool) preg_match('/^[a-zA-Z0-9_-]{1,150}$/D', $id), 422);
        $base = config('wompi.environment') === 'production' ? 'https://production.wompi.co/v1' : 'https://sandbox.wompi.co/v1';

        return Http::acceptJson()->withToken(config('wompi.private_key'))->timeout(15)
            ->get($base.'/transactions/'.$id)->throw()->json('data') ?? [];
    }

    public function applyTransaction(array $transaction): ?WompiOrder
    {
        return DB::transaction(function () use ($transaction) {
            $order = WompiOrder::query()->where('reference', $transaction['reference'] ?? '')->lockForUpdate()->first();
            if (! $order) {
                return null;
            }
            abort_unless($order->environment === config('wompi.environment'), 422);
            abort_unless(is_int($transaction['amount_in_cents'] ?? null) && $transaction['amount_in_cents'] === $order->amount_in_cents && ($transaction['currency'] ?? null) === $order->currency, 422);
            $id = $transaction['id'] ?? null;
            abort_unless(is_string($id) && $id !== '' && (! $order->transaction_id || $order->transaction_id === $id), 422);
            $status = $transaction['status'] ?? '';
            if (! in_array($status, ['PENDING', 'APPROVED', 'DECLINED', 'ERROR', 'VOIDED'], true)) {
                return $order;
            }
            // Un evento repetido o fuera de orden no vuelve a conceder ni reduce el acceso.
            if ($order->status === 'VOIDED' || ($order->status === 'APPROVED' && $status !== 'VOIDED')) {
                return $order;
            }
            if (in_array($order->status, ['DECLINED', 'ERROR'], true) && $status === 'PENDING') {
                return $order;
            }
            $order->transaction_id = $id;
            $order->status = $status;
            $canGrant = $this->canApplyServiceChanges($order);
            if ($status === 'APPROVED') {
                if ($canGrant && $order->payment_quote_id) {
                    $quote = PaymentQuote::query()->lockForUpdate()->findOrFail($order->payment_quote_id);
                    // Una autorización iniciada antes del vencimiento puede confirmarse después.
                    $quote->update(['status' => 'paid']);
                }
                $order->approved_at = now();
                if ($canGrant && ! $order->is_additional && $order->user_subscription_id) {
                    $subscription = UserSubscription::whereKey($order->user_subscription_id)->where('user_id', $order->user_id)->lockForUpdate()->firstOrFail();
                    if ($subscription->status === 'pending' && $subscription->payment_status === 'pending') {
                        $subscription->update(['status' => 'active', 'payment_status' => 'approved', 'amount_paid' => $order->amount_in_cents / 100]);
                    }
                } elseif ($canGrant && ! $order->is_additional && ! $order->user_subscription_id) {
                    $start = now()->startOfDay();
                    $end = $order->package === 'modo-indexacion' ? $start->copy()->addYearNoOverflow() : $start->copy()->addDays($order->access_days);
                    $plan = PaymentPlan::firstOrCreate(['name' => 'Portafolio 7.1 · '.config("wompi.catalog.$order->package.name")], [
                        'description' => 'Servicio del portafolio vigente. Importes registrados por pedido en COP.',
                        'price' => 0, 'duration_days' => $order->access_days, 'max_documents' => 1,
                        'includes_meetings' => true, 'meetings_limit' => 0, 'status' => 'active',
                    ]);
                    $subscription = UserSubscription::create([
                        'user_id' => $order->user_id, 'payment_plan_id' => $plan->id,
                        'start_date' => $start->toDateString(), 'end_date' => $end->toDateString(),
                        'status' => 'active', 'payment_status' => 'approved', 'amount_paid' => $order->amount_in_cents / 100,
                    ]);
                    $order->user_subscription_id = $subscription->id;
                }
            } elseif ($canGrant && $status === 'VOIDED' && $order->user_subscription_id) {
                UserSubscription::whereKey($order->user_subscription_id)->where('user_id', $order->user_id)
                    ->update(['status' => 'cancelled', 'payment_status' => 'voided']);
            }
            $order->save();

            return $order;
        });
    }
}
