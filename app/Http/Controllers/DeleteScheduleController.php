<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DeleteScheduleController extends Controller
{
    // Exibe a lista de horários para exclusão
    public function index(Request $request)
    {
        $user = Auth::user();

        // Se o usuário selecionou uma data específica, busca por essa data
        if ($request->has('date')) {
            $date = Carbon::parse($request->date)->format('Y-m-d');
            $schedules = Schedule::where('user_id', $user->id)
                ->where('date', $date)
                ->orderBy('start_time', 'asc')
                ->get()
                ->groupBy('date');
        } else {
            // Busca os últimos 10 dias automaticamente
            $dateLimit = Carbon::now()->subDays(10)->format('Y-m-d');
            $schedules = Schedule::where('user_id', $user->id)
                ->where('date', '>=', $dateLimit)
                ->orderBy('date', 'desc')
                ->orderBy('start_time', 'asc')
                ->get()
                ->groupBy('date');
        }

        return view('schedules.delete', ['paginatedSchedules' => $schedules]);
    }

    // Exclui um único horário
    public function deleteSingle(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
        ]);

        Schedule::where('id', $request->schedule_id)->delete();

        return redirect()->route('schedules.delete')->with('success', 'Horário excluído com sucesso.');
    }

    // Exclui todos os horários de um dia específico
    public function deleteByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
        ]);

        $user = Auth::user();
        Schedule::where('user_id', $user->id)
            ->where('date', $request->date)
            ->delete();

        return redirect()->route('schedules.delete')->with('success', 'Todos os horários do dia foram excluídos.');
    }

    // Exclui todos os horários futuros
    public function deleteFutureSchedules()
    {
        $user = Auth::user();
        $today = Carbon::now()->format('Y-m-d');

        Schedule::where('user_id', $user->id)
            ->where('date', '>=', $today)
            ->delete();

        return redirect()->route('schedules.delete')->with('success', 'Todos os horários futuros foram excluídos.');
    }
}
