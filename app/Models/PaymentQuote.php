<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentQuote extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount_in_cents' => 'integer', 'access_days' => 'integer', 'expires_at' => 'datetime'];
    }
}
