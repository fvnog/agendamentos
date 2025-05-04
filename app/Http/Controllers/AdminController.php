<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\LunchBreak;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('dashboard');
    }

    public function createSchedule()
    {
        // Lógica para criar um horário
        return view('schedules.create');
    }

    public function storeSchedule(Request $request)
    {
        // Lógica para salvar o horário
        $schedule = new Schedule();
        $schedule->name = $request->name;
        // Defina o restante dos campos necessários
        $schedule->save();

        return redirect()->route('schedules.index');
    }

    public function createLunchBreak()
    {
        // Lógica para criar uma pausa para o almoço
        return view('lunch-break.create');
    }

    public function storeLunchBreak(Request $request)
    {
        // Lógica para salvar a pausa para o almoço
        $lunchBreak = new LunchBreak();
        $lunchBreak->start_time = $request->start_time;
        $lunchBreak->end_time = $request->end_time;
        $lunchBreak->save();

        return redirect()->route('lunch-break.index');
    }

    public function indexLunchBreak()
    {
        // Exibir a lista de pausas para o almoço
        $lunchBreaks = LunchBreak::all();
        return view('lunch-break.index', compact('lunchBreaks'));
    }
}
