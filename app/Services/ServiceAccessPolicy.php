<?php

namespace App\Services;

use App\Models\UserSubscription;

class ServiceAccessPolicy
{
    public function planName(string $package): string
    {
        return 'Portafolio 7.1 · '.config("wompi.catalog.$package.name");
    }

    public function agreement(int $userId, string $package): ?UserSubscription
    {
        if (! in_array($package, ['correccion-completa', 'grandes-volumenes'], true)) {
            return null;
        }

        return UserSubscription::where('user_id', $userId)
            ->where('status', 'pending')->where('payment_status', 'pending')
            ->whereDate('end_date', '>=', now('America/Bogota')->toDateString())
            ->whereColumn('end_date', '>=', 'start_date')
            ->whereHas('paymentPlan', fn ($query) => $query->where('name', $this->planName($package)))
            ->latest('id')->first();
    }

    public function days(int $userId, string $package): ?int
    {
        if ($package === 'modo-indexacion') {
            return (int) now('America/Bogota')->startOfDay()->diffInDays(now('America/Bogota')->startOfDay()->addYearNoOverflow());
        }
        $agreement = $this->agreement($userId, $package);

        return $agreement ? (int) $agreement->start_date->diffInDays($agreement->end_date) : null;
    }
}
