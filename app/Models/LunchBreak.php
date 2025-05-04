<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LunchBreak extends Model
{
    use HasFactory;

    // Defina a tabela se for diferente do nome plural do modelo
    // protected $table = 'lunch_breaks';

    protected $fillable = [
        'user_id',
        'start_time',
        'end_time',
    ];
}
