<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Throwable;

class TrmService
{
    public function current(): array
    {
        $day = now('America/Bogota')->toDateString();
        try {
            $rate = Cache::remember('propaps-trm-'.$day, 300, function () use ($day) {
                $rows = Http::acceptJson()->connectTimeout(3)->timeout(8)->get('https://www.datos.gov.co/resource/32sa-8pi3.json', [
                    '$where' => "vigenciadesde <= '$day"."T00:00:00' AND vigenciahasta >= '$day"."T00:00:00'",
                    '$order' => 'vigenciadesde DESC', '$limit' => 1,
                ])->throw()->json();
                $record = $rows[0] ?? [];
                $value = $record['valor'] ?? '';
                $from = substr((string) ($record['vigenciadesde'] ?? ''), 0, 10);
                $until = substr((string) ($record['vigenciahasta'] ?? ''), 0, 10);
                if (($record['unidad'] ?? '') !== 'COP' || ! is_string($value) || ! preg_match('/^[0-9]{1,5}(\.[0-9]{1,4})?$/D', $value)
                    || ! preg_match('/^\d{4}-\d{2}-\d{2}$/D', $from) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/D', $until)
                    || $from > $day || $until < $day || $until < $from) {
                    throw new \RuntimeException('Invalid TRM');
                }
                [$whole, $fraction] = array_pad(explode('.', $value, 2), 2, '');
                $units = (int) $whole * 10000 + (int) str_pad($fraction, 4, '0');
                if ($units <= 0) {
                    throw new \RuntimeException('Invalid TRM');
                }

                return ['units' => $units, 'from' => $from, 'until' => $until, 'source' => 'Superfinanciera · datos.gov.co'];
            });
            if ($rate['from'] > $day || $rate['until'] < $day) {
                throw new \RuntimeException('Expired TRM');
            }

            return $rate;
        } catch (Throwable $error) {
            throw ValidationException::withMessages(['payment' => 'No pudimos verificar la TRM vigente. Intenta de nuevo más tarde; no se inició ningún cobro.']);
        }
    }

    public function convert(int $usdCents, array $rate): int
    {
        $amount = intdiv($usdCents * $rate['units'] + 5000, 10000);
        if ($amount < 10000 || $amount > 10000000000) {
            throw ValidationException::withMessages(['payment' => 'El importe está fuera del rango de cobro permitido.']);
        }

        return $amount;
    }
}
