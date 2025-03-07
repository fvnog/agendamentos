<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\LunchBreak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ScheduleController extends Controller
{
    // Exibe os horários do usuário logado
    public function index()
    {
        // Recuperando os horários do usuário logado
        $user = Auth::user();
        $schedules = Schedule::where('user_id', $user->id)->get();
    
        // Garantindo que 'date' seja um objeto Carbon
        foreach ($schedules as $schedule) {
            $schedule->date = Carbon::parse($schedule->date); // Isso garante que 'date' seja um Carbon
        }
    
        return view('schedules.index', compact('schedules'));
    }
    
    public function showSchedulePage()
{
    // Supondo que você tenha um modelo Schedule com horários disponíveis
    $schedules = Schedule::all(); // ou qualquer lógica que você tenha para pegar os horários disponíveis
    
    // Passando a variável para a visão
    return view('schedule.index', compact('schedules'));
}


public function getSchedules(Request $request)
{
    $date = $request->query('date', now()->toDateString()); // Obtém a data selecionada ou usa a atual
    $barberId = $request->query('barber_id'); // Obtém o ID do barbeiro, se existir

    // Obtém apenas os usuários que são barbeiros (is_admin = 1)
    $barbers = User::where('is_admin', 1)->pluck('id')->toArray();

    // Selecione o primeiro barbeiro como padrão se nenhum for escolhido
    if (!$barberId) {
        $barberId = reset($barbers);
    }

    // Garante que o ID do barbeiro esteja na lista de barbeiros válidos
    if (!in_array($barberId, $barbers)) {
        return response()->json([]);
    }

    // **CACHE - Armazena os horários por 5 minutos**
    $cacheKey = "schedules_{$barberId}_{$date}";

    $schedules = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($date, $barberId) {
        return Schedule::whereDate('date', $date)
                       ->where('user_id', $barberId)
                       ->get();
    });

    return response()->json($schedules);
}


public function checkAvailability(Request $request)
{
    $schedule = Schedule::find($request->schedule_id);

    if (!$schedule) {
        return response()->json(['status' => 'error', 'message' => 'Horário não encontrado.'], 404);
    }

    if ($schedule->is_booked) {
        return response()->json(['status' => 'booked', 'message' => 'Este horário já foi reservado.']);
    }

    if ($schedule->is_locked) {
        return response()->json(['status' => 'locked', 'message' => 'Este horário está sendo reservado por outro usuário.']);
    }

    return response()->json(['status' => 'available']);
}



    // Exibe o formulário para criação de horários
    public function create()
    {
        return view('schedules.create');
    }

    public function store(Request $request)
    {
        // Validação do formulário
        $validated = $request->validate([
            'interval' => 'required|integer', // Intervalo de tempo (em minutos)
            'time_frame' => 'required|in:day,week,month', // Período de tempo
            'start_time' => 'required|date_format:H:i', // Hora de início
            'end_time' => 'required|date_format:H:i|after:start_time', // Hora de término
        ]);

        // Obter o usuário logado
        $user = Auth::user();
        $start_time = Carbon::parse($request->start_time);
        $end_time = Carbon::parse($request->end_time);
        
        // Criar horários conforme o intervalo e período
        $interval = $validated['interval'];
        $time_frame = $validated['time_frame'];

        // Obter o horário de almoço do usuário logado
        $lunchBreak = LunchBreak::where('user_id', $user->id)->first();

        // Gerar os horários baseados no intervalo e período
        $date = Carbon::today(); // Data atual

        switch ($time_frame) {
            case 'day':
                $this->createSchedulesForDay($user, $start_time, $end_time, $interval, $date, $lunchBreak);
                break;

            case 'week':
                $this->createSchedulesForWeek($user, $start_time, $end_time, $interval, $date, $lunchBreak);
                break;

            case 'month':
                $this->createSchedulesForMonth($user, $start_time, $end_time, $interval, $date, $lunchBreak);
                break;
        }

        return redirect()->route('dashboard')->with('success', 'Horários criados com sucesso!');
    }

    private function createSchedulesForDay($user, $start_time, $end_time, $interval, $date, $lunchBreak)
    {
        // Garantir que o intervalo é um inteiro
        $interval = (int) $interval;
    
        while ($start_time < $end_time) {
            // Verificar se o horário de almoço do usuário está conflitando com o agendamento
            if ($lunchBreak && $start_time->between($lunchBreak->start_time, $lunchBreak->end_time)) {
                // Se o horário de início do agendamento está dentro do horário de almoço, pula esse horário
                $start_time->addMinutes($interval);
                continue;
            }
    
            // Verificar se já existe um horário no banco de dados
            if (Schedule::where('user_id', $user->id)
                    ->where('date', $date)
                    ->where('start_time', $start_time->format('H:i'))
                    ->exists()) {
                // Se já existir, incrementa o intervalo e tenta novamente
                $start_time->addMinutes($interval);
                continue;
            }
    
            // Criar novo horário
            Schedule::create([
                'user_id' => $user->id,
                'date' => $date,
                'start_time' => $start_time->format('H:i'),
                'end_time' => $start_time->addMinutes($interval)->format('H:i'),
            ]);
        }
    }
    

    private function createSchedulesForWeek($user, $start_time, $end_time, $interval, $date, $lunchBreak)
    {
        $end_of_week = Carbon::parse($date)->endOfWeek();

        while ($date <= $end_of_week) {
            // Chama a criação para o dia atual da semana
            $this->createSchedulesForDay($user, $start_time, $end_time, $interval, $date, $lunchBreak);
            // Move para o próximo dia
            $date->addDay();
        }
    }

    private function createSchedulesForMonth($user, $start_time, $end_time, $interval, $date, $lunchBreak)
    {
        $end_of_month = Carbon::parse($date)->endOfMonth();

        while ($date <= $end_of_month) {
            // Chama a criação para o dia atual do mês
            $this->createSchedulesForDay($user, $start_time, $end_time, $interval, $date, $lunchBreak);
            // Move para o próximo dia
            $date->addDay();
        }
    }
}
