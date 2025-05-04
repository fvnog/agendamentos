<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StripeAccount extends Model
{
    use HasFactory;

    protected $table = 'stripe_accounts';

    protected $fillable = [
        'user_id',
        'gateway_name',
        'stripe_secret_key',
        'stripe_public_key',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
