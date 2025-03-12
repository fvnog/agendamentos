<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Payment;

class AdminScheduleController extends Controller
{
    // Exibir os horários do barbeiro logado e permitir filtragem por data
    public function index(Request $request)
{
    // Obtém a data do filtro ou usa a data atual como padrão
    $selectedDate = $request->query('date', Carbon::today()->toDateString());

    // Busca os horários do barbeiro logado para a data selecionada
    $schedules = Schedule::where('user_id', auth()->id())
        ->whereDate('date', $selectedDate)
        ->orderBy('start_time', 'asc')
        ->get();

    // Adicionar a verificação na tabela de pagamentos caso não tenha serviços na tabela schedules
    foreach ($schedules as $schedule) {
        // Garante que services seja um array válido
        $scheduleServices = $schedule->services;

        if (is_string($scheduleServices)) {
            $scheduleServices = json_decode($scheduleServices, true);
        }

        if (!is_array($scheduleServices) || empty($scheduleServices)) {
            // Buscar serviços no Payment caso não existam no Schedule
            $payment = Payment::where('schedule_id', $schedule->id)->first();
            if ($payment && !empty($payment->services)) {
                $scheduleServices = $payment->services;
            }
        }

        // Atualiza o schedule para garantir que os serviços estejam corretamente atribuídos
        $schedule->services = is_array($scheduleServices) ? $scheduleServices : [];
    }

    // Busca todos os usuários ativos para a seleção de clientes
    $users = User::orderBy('name')->get();

    // 🔹 Busca todos os serviços disponíveis para exibição no modal
    $availableServices = \DB::table('services')->get(); // Garante que esta tabela existe no banco

    return view('admin.schedules.index', compact('schedules', 'users', 'selectedDate', 'availableServices'));
}


    // Adicionar um cliente manualmente ao horário
    public function addClient(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'user_id' => 'nullable|exists:users,id',
            'client_name' => 'nullable|string|max:255',
            'services' => 'required|array',
        ]);
    
        $schedule = Schedule::find($request->schedule_id);
    
        if ($schedule->is_booked) {
            return back()->with('error', 'Este horário já está reservado.');
        }
    
        // Determinar o cliente
        $client_id = $request->user_id ? $request->user_id : null;
        $client_name = $request->client_name ? $request->client_name : null;
    
        // Decodificar os serviços selecionados e calcular o total
        $selectedServices = [];
        $totalAmount = 0;
    
        foreach ($request->services as $serviceJson) {
            $service = json_decode($serviceJson, true);
            $selectedServices[] = ['name' => $service['name'], 'price' => $service['price']];
            $totalAmount += $service['price'];
        }
    
        // 🔹 Atualiza a reserva no Schedule incluindo os serviços
        $schedule->update([
            'is_booked' => 1,
            'client_id' => $client_id,
            'client_name' => $client_name,
            'services' => $selectedServices, // Agora salva os serviços também no schedule
        ]);
    
        // 🔹 Criar registro de pagamento, garantindo que 'user_id' só será salvo se existir
        $paymentData = [
            'schedule_id' => $schedule->id,
            'type' => 'manual',
            'amount' => $totalAmount,
            'txid' => uniqid('MANUAL_'),
            'services' => $selectedServices,
        ];
    
        if ($client_id) {
            $paymentData['user_id'] = $client_id;
        }
    
        Payment::create($paymentData);
    
        return back()->with('success', 'Horário agendado e pagamento registrado!');
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
