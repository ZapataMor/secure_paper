<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'payment_plan_id',
    'start_date',
    'end_date',
    'status',
    'payment_status',
    'amount_paid',
])]
class UserSubscription extends Model
{
    use HasFactory;

    protected const ACTIVE_STATUSES = [
        'active',
        'vigente',
    ];

    protected const PAID_STATUSES = [
        'paid',
        'pagado',
        'approved',
        'aprobado',
        'completed',
        'completado',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'amount_paid' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentPlan(): BelongsTo
    {
        return $this->belongsTo(PaymentPlan::class);
    }

    public function scopeActivePaid(Builder $query): Builder
    {
        return $query
            ->whereIn('status', self::ACTIVE_STATUSES)
            ->whereIn('payment_status', self::PAID_STATUSES)
            ->whereDate('start_date', '<=', now()->toDateString())
            ->whereDate('end_date', '>=', now()->toDateString());
    }
}
