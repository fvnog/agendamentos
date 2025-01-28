<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    protected $fillable = [
        'client_id',
        'user_id', // Identificador do barbeiro
        'date',
        'start_time',
        'end_time',
        'is_booked',
        'services',
        'client_id'
    ];

    // Verifica se o horário está durante o intervalo de almoço
    public static function isDuringLunchBreak($start_time, $end_time, $lunch_start, $lunch_end)
    {
        // Certifica-se de que os horários estão corretamente formatados
        $start_time = Carbon::parse($start_time);
        $end_time = Carbon::parse($end_time);
        $lunch_start = Carbon::parse($lunch_start);
        $lunch_end = Carbon::parse($lunch_end);

        return ($start_time >= $lunch_start && $start_time < $lunch_end) ||
               ($end_time > $lunch_start && $end_time <= $lunch_end);
    }

       // Relacionamento com o cliente
       public function client()
       {
           return $this->belongsTo(User::class, 'client_id');
       }
   
       // Relacionamento com o barbeiro
       public function barber()
       {
           return $this->belongsTo(User::class, 'user_id');
       }
   
       // Acessar os serviços como array
       public function getServicesAttribute($value)
       {
           return json_decode($value, true);
       }
   
       public function setServicesAttribute($value)
       {
           $this->attributes['services'] = json_encode($value);
       }
       
}
