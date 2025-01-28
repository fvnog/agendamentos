<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserSchedulesController extends Controller
{
    public function index(Request $request)
    {
        // Define o fuso horário de Brasília
        $timezone = 'America/Sao_Paulo';
        $currentDate = Carbon::now($timezone)->format('Y-m-d');

        // Recupera a data do filtro (ou a data atual, caso não tenha sido enviada)
        $filterDate = $request->query('date', $currentDate);

        // Busca os horários para a data selecionada
        $schedules = Schedule::whereDate('date', $filterDate)
                             ->orderBy('start_time')
                             ->get();

        return view('schedules.index', compact('schedules', 'filterDate'));
    }
}
