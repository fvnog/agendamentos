<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\ItauPixService;
use App\Models\Payment;

class ClientScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Fuso horário de Brasília
        $timezone = 'America/Sao_Paulo';
    
        // Data atual ou data filtrada
        $currentDate = Carbon::now($timezone)->format('Y-m-d');
        $filterDate = $request->query('date', $currentDate);
    
        // Recuperar horários disponíveis
        $schedules = Schedule::whereDate('date', $filterDate)
                             ->where('is_booked', false)
                             ->orderBy('start_time')
                             ->get();
    
        // Recuperar serviços e barbeiros (is_admin = 1)
        $services = Service::all();
        $barbers = User::where('is_admin', 1)->get();
    
        return view('client.schedule', compact('schedules', 'filterDate', 'services', 'barbers'));
    }
    

    public function store(Request $request, ItauPixService $pixService)
    {
        \Log::info('Início do método store para reserva de horário.', $request->all());
    
        // Validação dos dados enviados
        $validated = $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
        ]);
    
        \Log::info('Validação bem-sucedida.', $validated);
    
        // Recuperar o horário selecionado
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        \Log::info('Horário recuperado.', ['schedule' => $schedule]);
    
        if ($schedule->is_booked) {
            \Log::warning('Tentativa de reserva de horário já reservado.', ['schedule_id' => $schedule->id]);
            return back()->with('error', 'Este horário já foi reservado.');
        }
    
        // Calcular o valor total dos serviços selecionados
        $selectedServices = Service::whereIn('id', $validated['services'])->get();
        $totalValue = $selectedServices->sum('price');
        \Log::info('Serviços selecionados e valor total calculado.', [
            'selected_services' => $selectedServices,
            'total_value' => $totalValue,
        ]);
    
        if ($totalValue <= 0) {
            \Log::error('Valor total dos serviços inválido.', ['total_value' => $totalValue]);
            return back()->with('error', 'O valor dos serviços selecionados é inválido.');
        }
    
        // Gerar um TXID único para o pagamento
        $txid = 'SCHEDULE' . $schedule->id . uniqid();
        \Log::info('TXID gerado.', ['txid' => $txid]);
    
        // Criar cobrança Pix
        try {
            $pix = $pixService->createPix($totalValue, $txid);
            \Log::info('Cobrança Pix gerada com sucesso.', ['pix' => $pix]);
        } catch (\Exception $e) {
            \Log::error('Erro ao gerar cobrança Pix.', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Erro ao gerar o pagamento Pix: ' . $e->getMessage());
        }
    
        // Salvar os dados do pagamento no banco
        try {
            Payment::create([
                'schedule_id' => $schedule->id,
                'txid' => $txid,
                'value' => $totalValue,
                'status' => 'pending',
                'services' => json_encode($validated['services']),
            ]);
            \Log::info('Pagamento registrado no banco de dados.', ['txid' => $txid]);
        } catch (\Exception $e) {
            \Log::error('Erro ao salvar pagamento no banco.', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Erro ao salvar os dados de pagamento.');
        }
    
        // Redirecionar para a página de pagamento
        try {
            return redirect()->route('payments.qrcode', [
                'qrcode' => $pix['qrcode'],
                'payload' => $pix['pix'],
                'expiration' => 10,
                'schedule_id' => $schedule->id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao redirecionar para a página de pagamento.', ['exception' => $e->getMessage()]);
            return back()->with('error', 'Erro ao redirecionar para a página de pagamento.');
        }
    }
    
    
    
}
