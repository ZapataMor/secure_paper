<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WompiOrder extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return ['amount_in_cents' => 'integer', 'access_days' => 'integer', 'approved_at' => 'datetime', 'expires_at' => 'datetime', 'is_additional' => 'boolean'];
    }
}
