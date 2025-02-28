<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $valor = (int) $request->valor; // Certificar-se de que é inteiro em centavos

            $charge = Charge::create([
                'amount' => $valor, // Já está em centavos
                'currency' => 'brl',
                'source' => $request->stripeToken,
                'description' => "Pagamento para " . $request->nomeCliente . " (CPF: " . $request->cpf . ")"
            ]);

            // 🔹 Se o pagamento foi aprovado, reservar o horário
            if ($charge->status === "succeeded") {
                // 🔹 Captura os dados do frontend
                $userId = $request->user_id;
                $scheduleId = $request->schedule_id;
                $services = $request->services; // JSON com serviços selecionados

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

                // 🔹 Atualiza a reserva no BD
                $schedule->update([
                    'is_booked' => 1,
                    'client_id' => $user->id,
                    'services' => json_encode($services)
                ]);

                Log::info("✅ Horário reservado com sucesso!", [
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
