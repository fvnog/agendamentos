<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    protected $fillable = [
        'user_id', 'date', 'start_time', 'end_time', 'lunch_start', 'lunch_end', 'is_lunch_break'
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
}
