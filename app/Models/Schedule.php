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
        'weekday', // Dia da semana para horários fixos
        'start_time',
        'end_time',
        'is_booked',
        'is_locked',
        'services'
    ];

    protected $casts = [
        'is_booked' => 'boolean',
        'is_locked' => 'boolean',
        'services'  => 'array', // Garante que `services` seja tratado como array
    ];

    /**
     * 🔹 Relacionamento com o cliente (usuário que reservou)
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * 🔹 Relacionamento com o barbeiro
     */
    public function barber()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * 🔹 Verifica se o horário está dentro do intervalo de almoço
     */
    public static function isDuringLunchBreak($start_time, $end_time, $lunch_start, $lunch_end)
    {
        $start_time = Carbon::parse($start_time);
        $end_time = Carbon::parse($end_time);
        $lunch_start = Carbon::parse($lunch_start);
        $lunch_end = Carbon::parse($lunch_end);

        return ($start_time >= $lunch_start && $start_time < $lunch_end) ||
               ($end_time > $lunch_start && $end_time <= $lunch_end);
    }

    /**
     * 🔹 Obtém os serviços como array
     */
    public function getServicesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * 🔹 Salva os serviços como JSON no banco de dados
     */
    public function setServicesAttribute($value)
    {
        $this->attributes['services'] = is_array($value) ? json_encode($value) : json_encode([]);
    }

    /**
     * 🔹 Verifica se o horário é fixo (não tem `date`, apenas `weekday`)
     */
    public function isFixed()
    {
        return is_null($this->date);
    }
}
