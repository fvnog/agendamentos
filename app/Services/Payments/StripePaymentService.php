<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\StripeAccount;
use Exception;

class StripePaymentService
{
    protected $stripeAccount;

    public function __construct(StripeAccount $stripeAccount)
    {
        $this->stripeAccount = $stripeAccount;
    }

    public function processPayment($amount, $token, $description)
    {
        try {
            Log::info("🔹 Iniciando pagamento via Stripe", [
                'amount' => $amount,
                'gateway' => $this->stripeAccount->gateway_name
            ]);

            // 🔹 Configurar a API com a chave do usuário
            Stripe::setApiKey($this->stripeAccount->stripe_secret_key);

            // 🔹 Criar a cobrança
            $charge = Charge::create([
                'amount' => $amount,
                'currency' => 'brl',
                'source' => $token,
                'description' => $description
            ]);

            if ($charge->status === "succeeded") {
                Log::info("✅ Pagamento Stripe bem-sucedido!", ['charge_id' => $charge->id]);
                return [
                    'success' => true,
                    'txid' => $charge->id,
                    'status' => $charge->status
                ];
            }

            throw new Exception("Pagamento não aprovado pela Stripe.");
        } catch (Exception $e) {
            Log::error("❌ Erro no pagamento via Stripe", ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => 'Erro ao processar o pagamento.',
                'error' => $e->getMessage()
            ];
        }
    }
}
