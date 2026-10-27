<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

class AdditionalQuotePricing
{
    public function calculate(array $data): array
    {
        $type = $data['additional_type'];
        $words = $type === 'extra_words' ? (int) ($data['extra_words'] ?? 0) : null;
        $units = $type === 'extra_words' ? intdiv($words + 1999, 2000) : 1;
        if ($type === 'extra_words' && ($words < 1 || $words > 1000000 || $data['package'] !== 'correccion-completa')) {
            throw ValidationException::withMessages(['extra_words' => 'Indica las palabras adicionales de Corrección Completa.']);
        }
        $subtotal = match ($type) {
            'peer_review' => 16000,
            'extra_words' => $units * 5000,
            'other' => $this->decimalUnits($data['agreed_usd'], 2),
        };
        $export = $data['tax_treatment'] === 'export_exempt';
        if ($export && ($data['billing_country'] === 'CO' || empty($data['export_verified']))) {
            throw ValidationException::withMessages(['export_verified' => 'La exención requiere verificar la exportación del servicio y un cliente en el exterior.']);
        }
        $taxBps = $export ? 0 : 1900;
        $tax = intdiv($subtotal * $taxBps + 5000, 10000);
        $total = $subtotal + $tax;
        $rate = $this->decimalUnits($data['cop_per_usd'], 4);
        $copCents = intdiv($total * $rate + 5000, 10000);
        if ($rate <= 0 || $rate > 1000000000 || $copCents < 10000 || $copCents > 10000000000) {
            throw ValidationException::withMessages(['cop_per_usd' => 'Revisa la tasa y el importe final en COP.']);
        }

        return [
            'additional_type' => $type, 'extra_words' => $words, 'units' => $units,
            'billing_country' => $data['billing_country'], 'tax_treatment' => $data['tax_treatment'],
            'export_verified' => $export, 'usd_subtotal_cents' => $subtotal,
            'usd_tax_cents' => $tax, 'usd_total_cents' => $total,
            'tax_basis_points' => $taxBps, 'cop_rate_units' => $rate, 'amount_in_cents' => $copCents,
        ];
    }

    private function decimalUnits(string $value, int $precision): int
    {
        [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');

        return (int) $whole * (10 ** $precision) + (int) str_pad($fraction, $precision, '0');
    }
}
