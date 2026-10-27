<?php

namespace App\Http\Controllers;

use App\Models\PaymentQuote;
use App\Models\WompiOrder;
use App\Services\TrmService;
use App\Services\WompiGateway;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class WompiPaymentController extends Controller
{
    public function index(Request $request, WompiGateway $gateway)
    {
        $installed = Schema::hasTable('wompi_orders');
        try {
            $trm = app(TrmService::class)->current();
        } catch (ValidationException) {
            $trm = null;
        }

        return view('private.planes', [
            'trm' => $trm, 'billingTax' => $gateway->billingTax($request->user()->id), 'clientId' => $request->user()->id, 'catalog' => config('wompi.catalog'), 'gateway' => $gateway, 'installed' => $installed,
            'selectedPackage' => $request->query('paquete'),
            'canPayAdditional' => $gateway->canPayAdditional($request->user()->id),
            'quotes' => $installed ? PaymentQuote::where('user_id', $request->user()->id)->where('status', 'offered')->where('expires_at', '>', now())->latest()->get() : collect(),
            'orders' => $installed ? WompiOrder::where('user_id', $request->user()->id)->latest()->limit(10)->get() : collect(),
        ]);
    }

    public function store(Request $request, WompiGateway $gateway)
    {
        abort_unless(Schema::hasTable('wompi_orders'), 503, 'Los pagos todavía están en preparación.');
        $data = $request->validate([
            'quote_id' => ['nullable', 'integer', 'required_without:package'],
            'additional_type' => ['nullable', Rule::in(['peer_review', 'extra_words'])],
            'extra_words' => ['nullable', 'required_if:additional_type,extra_words', 'integer', 'min:1', 'max:1000000'],
            'package' => ['nullable', 'required_without:quote_id', Rule::in(array_keys(config('wompi.catalog')))],
            'option' => ['nullable', 'required_without_all:quote_id,additional_type', 'string', 'max:50'],
        ]);
        $order = $gateway->createOrder($request->user()->id, $data);

        return redirect()->route('private.payments.show', $order);
    }

    public function show(Request $request, WompiOrder $order, WompiGateway $gateway)
    {
        abort_unless($order->user_id === $request->user()->id, 403);
        // La redirección del navegador nunca es prueba de pago.
        if ($request->filled('id')) {
            $id = $request->validate(['id' => ['required', 'string', 'regex:/^[a-zA-Z0-9_-]{1,150}$/D']])['id'];
            try {
                $transaction = $gateway->transaction($id);
            } catch (RequestException|ConnectionException) {
                // Una consulta fallida no demuestra aprobación ni rechazo del pago.
                // No guardar el ID del retorno hasta verificarlo con el proveedor.
                $order->refresh();

                return view('private.payment-checkout', [
                    'order' => $order, 'gateway' => $gateway,
                    'paymentStatusUnavailable' => true,
                    'statusRetryUrl' => route('private.payments.show', ['order' => $order, 'id' => $id]),
                ]);
            }
            abort_unless(($transaction['reference'] ?? '') === $order->reference, 422);
            $gateway->applyTransaction($transaction);
            $order->refresh();
        }

        return view('private.payment-checkout', ['order' => $order, 'gateway' => $gateway]);
    }

    public function webhook(Request $request, string $environment, WompiGateway $gateway)
    {
        abort_unless($environment === config('wompi.environment'), 422);
        $event = $request->json()->all();
        abort_unless($gateway->validEvent($event), 401);
        abort_unless(($event['environment'] ?? '') === ($environment === 'production' ? 'prod' : 'test'), 422);
        if (($event['event'] ?? '') !== 'transaction.updated') {
            return response()->json(['received' => true]);
        }
        $id = data_get($event, 'data.transaction.id');
        abort_unless(is_string($id), 422);
        // Confirma el estado, la referencia y el importe con el API privado de Wompi.
        $transaction = $gateway->transaction($id);
        abort_unless(($transaction['id'] ?? null) === $id, 422);
        $gateway->applyTransaction($transaction);

        return response()->json(['received' => true]);
    }
}
