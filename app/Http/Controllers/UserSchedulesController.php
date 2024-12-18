<?php

namespace App\Http\Controllers;

use App\Models\Schedule;

class UserSchedulesController extends Controller
{
    public function index()
    {
        // Recupera os horários disponíveis
        $schedules = Schedule::where('is_booked', false) // Exemplo de filtro para horários não reservados
                              ->orderBy('date')
                              ->orderBy('start_time')
                              ->get();

        // Retorna a view desejada com os dados dos horários
        return view('user.schedules.index', compact('schedules'));
    }
}
