<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Payment;
use App\Models\StripeAccount;
use App\Services\Payments\PaymentServiceFactory;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function checkout(Request $request)
    {
        try {
            Log::info("🔹 Iniciando pagamento via Stripe", ['user_id' => $request->user_id]);

            // 🔹 Buscar a conta Stripe do usuário
            $stripeAccount = StripeAccount::where('user_id', $request->user_id)->first();
            if (!$stripeAccount) {
                Log::warning("⚠️ Conta Stripe não encontrada!", ['user_id' => $request->user_id]);
                return response()->json(['success' => false, 'message' => 'Conta Stripe não configurada.'], 400);
            }

            // 🔹 Criar a instância do serviço correto usando o Factory
            $paymentService = PaymentServiceFactory::create($stripeAccount);

            // 🔹 Processar pagamento
            $valor = (int) $request->valor; // Converter para centavos
            $description = "Pagamento para " . $request->nomeCliente . " (CPF: " . $request->cpf . ")";

            $paymentResponse = $paymentService->processPayment($valor, $request->stripeToken, $description);

            if (!$paymentResponse['success']) {
                return response()->json(['success' => false, 'message' => $paymentResponse['message']], 400);
            }

            // 🔹 Reservar horário
            $schedule = Schedule::find($request->schedule_id);
            if (!$schedule) {
                return response()->json(['success' => false, 'message' => 'Horário não encontrado.'], 404);
            }

            $schedule->update([
                'is_booked' => 1,
                'client_id' => $request->user_id,
                'services' => $request->services
            ]);

            // 🔹 Registrar pagamento
            Payment::create([
                'user_id' => $request->user_id,
                'schedule_id' => $schedule->id,
                'type' => 'cartao',
                'amount' => $valor / 100, // Converter para reais
                'txid' => $paymentResponse['txid'],
                'services' => $request->services
            ]);

            Log::info("✅ Pagamento via Stripe confirmado e horário reservado!", [
                'txid' => $paymentResponse['txid'],
                'user_id' => $request->user_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pagamento confirmado e horário reservado!',
                'status' => $paymentResponse['status']
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Erro no pagamento Stripe", ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro no pagamento!',
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
