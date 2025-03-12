<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;
use App\Models\Payment; // Importa o modelo de pagamentos

class StripeController extends Controller
{


    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    
        try {
            $valor = (int) $request->valor; // Certificar-se de que está em centavos
    
            $charge = Charge::create([
                'amount' => $valor,
                'currency' => 'brl',
                'source' => $request->stripeToken,
                'description' => "Pagamento para " . $request->nomeCliente . " (CPF: " . $request->cpf . ")"
            ]);
    
            if ($charge->status === "succeeded") {
                $userId = $request->user_id;
                $scheduleId = $request->schedule_id;
                $services = $request->services; // Dados do frontend
    
                // 🔹 Verifica se `services` é uma string JSON e converte para array se necessário
                if (is_string($services)) {
                    $services = json_decode($services, true);
                }
    
                // 🔹 Garante que `$services` seja sempre um array válido
                if (!is_array($services) || empty($services)) {
                    Log::warning("⚠️ Nenhum serviço válido foi enviado no pagamento.", ['services' => $services]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Nenhum serviço válido foi enviado.'
                    ], 400);
                }
    
                // 🔹 Verifica se o usuário está autenticado
                $user = User::find($userId);
                if (!$user) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Usuário não encontrado.'
                    ], 403);
                }
    
                // 🔹 Busca o horário no BD
                $schedule = Schedule::find($scheduleId);
                if (!$schedule) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Horário não encontrado.'
                    ], 404);
                }
    
                // 🔹 Atualiza a reserva no BD corretamente
                $schedule->update([
                    'is_booked' => 1,
                    'client_id' => $user->id,
                    'services' => $services // Agora armazenando diretamente como array
                ]);
    
                // 🔹 Registra o pagamento na tabela `payments`
                $payment = Payment::create([
                    'user_id' => $user->id,
                    'schedule_id' => $schedule->id,
                    'type' => 'cartao',
                    'amount' => $valor / 100, // Converte de centavos para reais
                    'txid' => $charge->id,
                    'services' => $services // Armazena como array JSON corretamente
                ]);
    
                Log::info("✅ Pagamento registrado e horário reservado!", [
                    'payment_id' => $payment->id,
                    'schedule_id' => $scheduleId,
                    'user_id' => $user->id,
                    'services' => $services
                ]);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Pagamento confirmado e horário reservado!',
                    'status' => $charge->status
                ]);
            }
    
            return response()->json(['success' => false, 'message' => 'Pagamento não concluído.']);
        } catch (\Exception $e) {
            Log::error("❌ Erro no pagamento Stripe", ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro no pagamento!',
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    

}
