<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;

class AdminScheduleController extends Controller
{
    // Exibir os horários do barbeiro logado e permitir filtragem por data
    public function index(Request $request)
    {
        // Obtém a data do filtro ou usa a data atual como padrão
        $selectedDate = $request->query('date', Carbon::today()->toDateString());

        // Busca apenas os horários do barbeiro logado para a data selecionada
        $schedules = Schedule::where('user_id', auth()->id())
            ->whereDate('date', $selectedDate)
            ->orderBy('start_time', 'asc') // Ordena do mais cedo para o mais tarde
            ->get();

        // Busca todos os usuários ativos para a seleção de clientes
        $users = User::orderBy('name')->get();

        return view('admin.schedules.index', compact('schedules', 'users', 'selectedDate'));
    }

    // Adicionar um cliente manualmente ao horário
    public function addClient(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'user_id' => 'nullable|exists:users,id',
            'client_name' => 'nullable|string|max:255',
        ]);

        $schedule = Schedule::find($request->schedule_id);

        // Se o horário já estiver reservado, impede a ação
        if ($schedule->is_booked) {
            return back()->with('error', 'Este horário já está reservado.');
        }

        // Selecione um cliente existente ou insira um nome manualmente
        if ($request->user_id) {
            $schedule->client_id = $request->user_id;
            $schedule->client_name = null; // Limpa o nome manual se um usuário for selecionado
        } else {
            $schedule->client_name = $request->client_name;
            $schedule->client_id = null; // Limpa o ID se for um nome manual
        }

        $schedule->is_booked = 1; // Marca o horário como reservado
        $schedule->save();

        return back()->with('success', 'Horário agendado com sucesso!');
    }

    public function removeClient(Request $request)
{
    $request->validate([
        'schedule_id' => 'required|exists:schedules,id',
    ]);

    $schedule = Schedule::find($request->schedule_id);

    // Se o horário não estiver reservado, retorna erro
    if (!$schedule->is_booked) {
        return back()->with('error', 'Este horário já está disponível.');
    }

    // Desmarca o cliente
    $schedule->is_booked = 0;
    $schedule->client_id = null;
    $schedule->client_name = null;
    $schedule->save();

    return back()->with('success', 'Horário desmarcado com sucesso!');
}


}
