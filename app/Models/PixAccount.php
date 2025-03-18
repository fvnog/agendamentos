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
        'bb_client_id',       // 🔹 Novo campo para o BB
        'bb_client_secret',   // 🔹 Novo campo para o BB
        'bb_gw_app_key',      // 🔹 Novo campo para o BB
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
