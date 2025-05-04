<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\FixedSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScheduleController extends Controller
{
    // Exibe o formulário de criação de horários
    public function create()
    {
        return view('schedules.create');
    }

    // Armazena os horários criados
    public function store(Request $request)
    {
        Log::info("🔵 Iniciando criação de horários pelo usuário: " . Auth::id());

        $validated = $request->validate([
            'schedule_type' => 'required|in:today,day,week,month',
            'specific_date' => 'nullable|date',
            'interval' => 'required|integer|min:5',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $user = Auth::user();
        $barberId = $user->id;
        $interval = (int) $validated['interval'];
        $start_time = Carbon::parse($request->start_time);
        $end_time = Carbon::parse($request->end_time);
        $scheduleType = $request->schedule_type;
        $specificDate = $request->specific_date;
        $dates = [];

        // 🔹 Determinar as datas de criação conforme a opção escolhida
        if ($scheduleType === 'today') {
            $dates[] = now()->toDateString();
        } elseif ($scheduleType === 'day' && $specificDate) {
            $dates[] = Carbon::parse($specificDate)->toDateString();
        } elseif ($scheduleType === 'week') {
            for ($i = 0; $i < 7; $i++) {
                $dates[] = now()->addDays($i)->toDateString();
            }
        } elseif ($scheduleType === 'month') {
            for ($i = 0; $i < 30; $i++) {
                $dates[] = now()->addDays($i)->toDateString();
            }
        }

        Log::info("📅 Datas geradas para criação de horários: " . json_encode($dates));

        foreach ($dates as $date) {
            $currentDate = Carbon::parse($date);
            $weekday = $currentDate->dayOfWeek;

            // 🔹 Buscar clientes fixos apenas para o barbeiro atual no dia da semana correto
            $fixedSchedules = FixedSchedule::where('barber_id', $barberId)
                ->where('weekday', $weekday)
                ->orderBy('start_time')
                ->get();

            Log::info("🔎 Encontrados " . $fixedSchedules->count() . " clientes fixos para o dia " . $weekday);

            // Exibir clientes fixos e seus horários no log
            $fixedClientsFound = [];
            foreach ($fixedSchedules as $fixed) {
                Log::info("📌 Cliente fixo: ID {$fixed->client_id} - Horário: {$fixed->start_time}");
                $fixedClientsFound[$fixed->client_id] = $fixed->start_time;
            }

            $currentStartTime = clone $start_time;
            $createdSchedules = []; // Lista de horários gerados

            // 🔹 Criar todos os horários primeiro (sem marcar cliente fixo ainda)
            while ($currentStartTime < $end_time) {
                $formattedStartTime = $currentStartTime->format('H:i');

                $schedule = Schedule::create([
                    'user_id' => $barberId,
                    'date' => $currentDate->format('Y-m-d'),
                    'start_time' => $formattedStartTime,
                    'end_time' => $currentStartTime->copy()->addMinutes($interval)->format('H:i'),
                    'client_id' => null,  // Ainda não marcamos cliente fixo
                    'is_booked' => 0,
                    'is_paid' => false,
                ]);

                Log::info("➖ Criado horário livre em " . $formattedStartTime);
                $createdSchedules[$formattedStartTime] = $schedule->id; // Salva os horários criados

                $currentStartTime->addMinutes($interval);
            }

            // 🔍 Agora tentar associar os clientes fixos aos horários criados
            $this->marcarClientesFixos($currentDate, $barberId, $fixedClientsFound, $createdSchedules);
        }

        Log::info("✅ Processo de criação de horários finalizado!");

        return redirect()->route('schedules.create')->with('success', 'Horários criados com sucesso!');
    }

    /**
     * Tenta associar os clientes fixos aos horários já criados.
     */
    private function marcarClientesFixos($date, $barberId, $fixedClientsFound, $createdSchedules)
    {
        Log::info("🔍 Iniciando marcação dos clientes fixos para a data: " . $date->format('Y-m-d'));
    
        foreach ($fixedClientsFound as $clientId => $expectedStartTime) {
            $expectedStartTimeFormatted = Carbon::parse($expectedStartTime)->format('H:i');
    
            Log::info("📌 Tentando marcar Cliente fixo ID {$clientId} para horário: {$expectedStartTimeFormatted}");
    
            // 🔹 Buscar os serviços do cliente fixo na tabela `fixed_schedules`
            $fixedSchedule = FixedSchedule::where('client_id', $clientId)
                ->where('barber_id', $barberId)
                ->where('weekday', $date->dayOfWeek)
                ->first();
    
            $services = [];
            $totalAmount = 0; // Inicia o total do valor dos serviços
    
            if ($fixedSchedule && $fixedSchedule->services) {
                $serviceIds = json_decode($fixedSchedule->services, true);
    
                $servicesData = DB::table('services')
                    ->whereIn('id', $serviceIds)
                    ->select('id', 'name', 'price')
                    ->get();
    
                foreach ($servicesData as $service) {
                    $services[] = [
                        'id' => $service->id,
                        'name' => $service->name
                    ];
                    $totalAmount += $service->price; // Soma o valor de cada serviço
                }
            }
    
            if (isset($createdSchedules[$expectedStartTimeFormatted])) {
                // 🔹 Se o horário exato existir, marcar o cliente fixo
                $scheduleId = $createdSchedules[$expectedStartTimeFormatted];
    
                Schedule::where('id', $scheduleId)
                    ->update([
                        'client_id' => $clientId,
                        'is_booked' => 1,
                        'services' => json_encode($services), // ✅ Salva os serviços corretamente
                    ]);
    
                // 🔥 Inserção do pagamento na tabela `payments`
                DB::table('payments')->insert([
                    'user_id' => $clientId,
                    'schedule_id' => $scheduleId,
                    'type' => 'pix',
                    'amount' => $totalAmount, // ✅ Agora inserimos o valor correto somado
                    'txid' => 'PIX',
                    'services' => json_encode($services), // ✅ Salva os serviços no pagamento
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
    
                Log::info("✅ Cliente fixo ID {$clientId} foi marcado no horário correto: {$expectedStartTimeFormatted} com serviços: " . json_encode($services) . " e valor: R$ " . number_format($totalAmount, 2, ',', '.'));
            } else {
                // 🔹 Se o horário exato não existir, buscar o primeiro horário depois disponível
                $sortedTimes = array_keys($createdSchedules);
                sort($sortedTimes);
    
                $nextAvailable = null;
                foreach ($sortedTimes as $time) {
                    if ($time >= $expectedStartTimeFormatted) {
                        $nextAvailable = $time;
                        break;
                    }
                }
    
                if ($nextAvailable) {
                    $scheduleId = $createdSchedules[$nextAvailable];
    
                    Schedule::where('id', $scheduleId)
                        ->update([
                            'client_id' => $clientId,
                            'is_booked' => 1,
                            'services' => json_encode($services), // ✅ Salva os serviços no horário
                        ]);
    
                    // 🔥 Inserção do pagamento na tabela `payments`
                    DB::table('payments')->insert([
                        'user_id' => $clientId,
                        'schedule_id' => $scheduleId,
                        'type' => 'pix',
                        'amount' => $totalAmount, // ✅ Agora inserimos o valor correto somado
                        'txid' => 'PIX',
                        'services' => json_encode($services), // ✅ Salva os serviços no pagamento
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
    
                    Log::info("✅ Cliente fixo ID {$clientId} foi marcado no horário disponível mais próximo: {$nextAvailable} com serviços: " . json_encode($services) . " e valor: R$ " . number_format($totalAmount, 2, ',', '.'));
                } else {
                    Log::error("❌ ERRO: Cliente fixo ID {$clientId} NÃO foi marcado, nenhum horário disponível.");
                }
            }
        }
    
        Log::info("🔍 Marcação dos clientes fixos concluída.");
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
    
        // 🔹 BUSCA DIRETA NO BANCO, SEM CACHE
        $schedules = Schedule::whereDate('date', $date)
                             ->where('user_id', $barberId)
                             ->get();
    
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

    
    
    
}
