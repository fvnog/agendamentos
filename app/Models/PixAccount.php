<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PixAccount extends Model
{
    use HasFactory;

    protected $table = 'pix_account';

    protected $fillable = [
        'user_id',
        'bank_name',
        'pix_key',
        'pix_key_type',
        'bb_client_id',        // 🔹 Banco do Brasil - Client ID
        'bb_client_secret',    // 🔹 Banco do Brasil - Client Secret
        'bb_gw_app_key',       // 🔹 Banco do Brasil - Gateway App Key
        'sicoob_client_id',    // 🔹 Sicoob - Client ID
        'sicoob_access_token', // 🔹 Sicoob - Access Token
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
