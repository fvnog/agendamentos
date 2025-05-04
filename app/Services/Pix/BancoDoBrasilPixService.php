<?php

namespace App\Services\Pix;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\PixAccount;
use Exception;

class BancoDoBrasilPixService implements PixInterface
{
    protected $pixAccount;

    public function __construct(PixAccount $pixAccount)
    {
        $this->pixAccount = $pixAccount;
    }

    public function createPayment($amount, $userId)
    {
        try {
            Log::info("🔹 Criando pagamento Pix no Banco do Brasil para usuário {$userId}");

            // 🔹 Obtendo credenciais do Banco do Brasil a partir do banco de dados
            $clientId = $this->pixAccount->bb_client_id;
            $clientSecret = $this->pixAccount->bb_client_secret;
            $gwDevAppKey = $this->pixAccount->bb_gw_app_key;
            $certPath = storage_path(env('BB_CERT_PATH'));

            $tokenUrl = env('BB_TOKEN_URL');
            $pixUrl = env('BB_PIX_URL');


            // 🔹 Obtendo a chave PIX do usuário
            $chavePix = $this->pixAccount->pix_key;

            if (!$chavePix) {
                throw new Exception("Chave PIX não encontrada para o usuário {$userId}.");
            }

            // 🔹 Obtendo Token de Acesso
            Log::info("🔹 Solicitando Token de Acesso ao Banco do Brasil...");

            $tokenResponse = Http::asForm()->withOptions(['verify' => $certPath])
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
                    'Accept' => 'application/json'
                ])->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'cob.write cob.read pix.read pix.write'
                ]);

            if (!$tokenResponse->successful()) {
                throw new Exception("Erro ao obter token do Banco do Brasil: " . $tokenResponse->body());
            }

            $accessToken = $tokenResponse->json()['access_token'];
            Log::info("🔹 Token obtido com sucesso!");

            // 🔹 Gera um TXID único
            $txid = Str::random(12);

            // 🔹 Formata o valor
            $formattedAmount = number_format((float) $amount, 2, '.', '');

            // 🔹 Monta os dados para a cobrança PIX
            $pixData = [
                'calendario' => ['expiracao' => 3600],
                'txid' => $txid,
                'valor' => ['original' => $formattedAmount],
                'chave' => $chavePix,
                'solicitacaoPagador' => "Pagamento via Pix"
            ];

            Log::info("🔹 JSON enviado ao Banco do Brasil:", ['json' => json_encode($pixData)]);

            // 🔹 Envia a requisição para criar a cobrança PIX
            $pixResponse = Http::withOptions(['verify' => $certPath])
                ->withHeaders([
                    'Authorization' => "Bearer " . $accessToken,
                    'gw-dev-app-key' => $gwDevAppKey,
                    'Accept' => 'application/json'
                ])->post($pixUrl, $pixData);

            if (!$pixResponse->successful()) {
                throw new Exception("Erro ao gerar cobrança Pix no Banco do Brasil: " . $pixResponse->body());
            }

            $pixResult = $pixResponse->json();
            Log::info("✅ Cobrança Pix criada com sucesso", ['response' => $pixResult]);

            return [
                'success' => true,
                'pix_copiaecola' => $pixResult['pixCopiaECola'],
                'txid' => $pixResult['txid'],
                'location' => $pixResult['location']
            ];
        } catch (Exception $e) {
            Log::error("❌ Erro ao criar pagamento no Banco do Brasil: " . $e->getMessage());
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
            Log::info("🔹 Verificando status do pagamento PIX com TXID: {$txid}");

            // 🔹 Obtendo credenciais do Banco do Brasil a partir do banco de dados
            $clientId = $this->pixAccount->bb_client_id;
            $clientSecret = $this->pixAccount->bb_client_secret;
            $gwDevAppKey = $this->pixAccount->bb_gw_app_key;
            $certPath = storage_path(env('BB_CERT_PATH'));

            $tokenUrl = env('BB_TOKEN_URL');
            $pixUrl = env('BB_PIX_URL') . "/{$txid}";

            // 🔹 Obtendo Token de Acesso
            $tokenResponse = Http::asForm()->withOptions(['verify' => $certPath])
                ->withHeaders([
                    'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
                    'Accept' => 'application/json'
                ])->post($tokenUrl, [
                    'grant_type' => 'client_credentials',
                    'scope' => 'cob.read'
                ]);

            if (!$tokenResponse->successful()) {
                throw new Exception("Erro ao obter token para verificação de pagamento: " . $tokenResponse->body());
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // 🔹 Consultando status do pagamento
            $statusResponse = Http::withOptions(['verify' => $certPath])
                ->withHeaders([
                    'Authorization' => "Bearer " . $accessToken,
                    'gw-dev-app-key' => $gwDevAppKey,
                    'Accept' => 'application/json'
                ])->get($pixUrl);

            if (!$statusResponse->successful()) {
                throw new Exception("Erro ao verificar pagamento PIX: " . $statusResponse->body());
            }

            $statusData = $statusResponse->json();
            Log::info("✅ Status do pagamento recebido", ['response' => $statusData]);

            return [
                'success' => true,
                'status' => $statusData['status'] ?? 'Desconhecido'
            ];
        } catch (Exception $e) {
            Log::error("❌ Erro ao verificar pagamento PIX: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erro ao verificar status do pagamento PIX.',
                'error' => $e->getMessage()
            ];
        }
    }
}
