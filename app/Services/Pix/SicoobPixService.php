<?php

namespace App\Services\Pix;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\PixAccount;
use Exception;

class SicoobPixService implements PixInterface
{
    protected $pixAccount;
    protected $baseUrl;

    public function __construct(PixAccount $pixAccount)
    {
        $this->pixAccount = $pixAccount;
        $this->baseUrl = env('SICOOB_BASE_URL', 'https://sandbox.sicoob.com.br/sicoob/sandbox/pix/api/v2');
    }

    public function createPayment($amount, $userId)
    {
        try {
            Log::info("🔹 Criando pagamento Pix no Sicoob para usuário {$userId}");

            // Obtendo credenciais do banco de dados
            $clientId = $this->pixAccount->sicoob_client_id;
            $accessToken = $this->pixAccount->sicoob_access_token;
            $chavePix = $this->pixAccount->pix_key;

            if (!$clientId || !$accessToken || !$chavePix) {
                throw new Exception("Credenciais do Sicoob não configuradas corretamente.");
            }

            // Gerando TXID único
            $txid = Str::random(12);
            $formattedAmount = number_format((float) $amount, 2, '.', '');

            // Criando cobrança Pix no Sicoob
            $pixData = [
                'calendario' => ['expiracao' => 3600],
                'txid' => $txid,
                'valor' => ['original' => $formattedAmount],
                'chave' => $chavePix,
                'solicitacaoPagador' => "Pagamento via Pix - Sicoob"
            ];

            Log::info("🔹 JSON enviado ao Sicoob:", ['json' => json_encode($pixData)]);

            // Enviando requisição
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $accessToken,
                'Content-Type' => 'application/json'
            ])->post("{$this->baseUrl}/cob", $pixData);

            if (!$response->successful()) {
                throw new Exception("Erro ao gerar cobrança Pix no Sicoob: " . $response->body());
            }

            $pixResult = $response->json();
            Log::info("✅ Cobrança Pix criada com sucesso", ['response' => $pixResult]);

            return [
                'success' => true,
                'pix_copiaecola' => $pixResult['pixCopiaECola'] ?? '',
                'txid' => $pixResult['txid'] ?? '',
                'location' => $pixResult['location'] ?? ''
            ];
        } catch (Exception $e) {
            Log::error("❌ Erro ao criar pagamento no Sicoob: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao processar o pagamento PIX.',
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkPaymentStatus($txid)
    {
        try {
            Log::info("🔹 Verificando status do pagamento PIX no Sicoob - TXID: {$txid}");

            // Obtendo credenciais do banco de dados
            $accessToken = $this->pixAccount->sicoob_access_token;

            if (!$accessToken) {
                throw new Exception("Token de acesso do Sicoob não configurado.");
            }

            // URL para verificar status do pagamento
            $pixUrl = "{$this->baseUrl}/cob/{$txid}";

            // Enviando requisição
            $response = Http::withHeaders([
                'Authorization' => "Bearer " . $accessToken,
                'Accept' => 'application/json'
            ])->get($pixUrl);

            if (!$response->successful()) {
                throw new Exception("Erro ao verificar pagamento PIX: " . $response->body());
            }

            $statusData = $response->json();
            Log::info("✅ Status do pagamento recebido", ['response' => $statusData]);

            return [
                'success' => true,
                'status' => $statusData['status'] ?? 'Desconhecido'
            ];
        } catch (Exception $e) {
            Log::error("❌ Erro ao verificar pagamento PIX no Sicoob: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao verificar status do pagamento PIX.',
                'error' => $e->getMessage()
            ];
        }
    }
}
