<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'date',
        'weekday',
        'start_time',
        'end_time',
        'is_booked',
        'is_locked',
        'services',
        'client_name',

        // novos campos
        'booked_by',
        'barber_id',
        'services_json',
        'amount_paid',
        'payment_id',
        'payment_status',
    ];

    protected $casts = [
        'is_booked'      => 'boolean',
        'is_locked'      => 'boolean',
        'services'       => 'array',
        'services_json'  => 'array',
        'amount_paid'    => 'decimal:2',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function barber()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getServicesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = is_array($value)
            ? json_encode($value)
            : json_encode([]);
    }
}

