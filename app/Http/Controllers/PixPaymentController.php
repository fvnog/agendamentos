<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Schedule;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class PixPaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        try {
            Log::info("🔹 Iniciando criação de pagamento Pix", ['request' => $request->all()]);

            // 🔹 Credenciais da API do BB
            $clientId = "eyJpZCI6ImExZDc5ZmJmLTgzN2YtNDYwMi1iYjdiLTk3YmRhMDdjNmNkNSIsImNvZGlnb1B1YmxpY2Fkb3IiOjAsImNvZGlnb1NvZnR3YXJlIjoxMjM0MzMsInNlcXVlbmNpYWxJbnN0YWxhY2FvIjoxfQ";
            $clientSecret = "eyJpZCI6ImQxNjhkZmItYzhjYy00NjIiLCJjb2RpZ29QdWJsaWNhZG9yIjowLCJjb2RpZ29Tb2Z0d2FyZSI6MTIzNDMzLCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE3MzgxMTEwMzQwNDh9";
            $tokenUrl = "https://oauth.sandbox.bb.com.br/oauth/token";
            $pixUrl = "https://api.hm.bb.com.br/pix/v2/cob";
            $chavePix = "9e881f18-cc66-4fc7-8f2c-a795dbb2bfc1"; // Sua chave Pix
            $gwDevAppKey = "c27196995c7578b34bfbbf6ff99c5a3e"; // Chave do BB

            // 🔹 Obter Token de Acesso
            Log::info("🔹 Solicitando Token de Acesso", ['url' => $tokenUrl]);

            $tokenResponse = Http::asForm()->withOptions([
                'verify' => '/home/plox-dev/certificados-webhook-bb/sandbox/Apos 12-02-2025/bb-cert-chain.pem'
            ])->withHeaders([
                'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
                'Accept' => 'application/json'
            ])->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'scope' => 'cob.write cob.read pix.read pix.write'
            ]);

            if (!$tokenResponse->successful()) {
                Log::error("❌ Erro ao obter token", ['status' => $tokenResponse->status(), 'error' => $tokenResponse->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao obter token',
                    'error' => $tokenResponse->body()
                ], 401);
            }

            $accessToken = $tokenResponse->json()['access_token'];
            Log::info("🔹 Token obtido com sucesso", ['token' => $accessToken]);

            // 🔹 Criar Cobrança Pix
            $devedor = [
                'cnpj' => "12345678000195",
                'nome' => "Empresa de Serviços SA"
            ];

            $data = [
                'calendario' => [
                    'expiracao' => 3600
                ],
                'devedor' => $devedor,
                'valor' => [
                    'original' => number_format((float) $request->valor, 2, '.', ''),
                    'modalidadeAlteracao' => 0 // 🔹 Adicionando esse campo
                ],
                'chave' => $chavePix,
                'solicitacaoPagador' => "Solicitacao Pix",
                'infoAdicionais' => [
                    [
                        'nome' => 'Campo 1',
                        'valor' => 'Informação Adicional1 do PSP-Recebedor'
                    ],
                    [
                        'nome' => 'Campo 2',
                        'valor' => 'Informação Adicional2 do PSP-Recebedor'
                    ]
                ]
            ];

            Log::info("🔹 Enviando solicitação de cobrança Pix", [
                'url' => $pixUrl,
                'headers' => [
                    'Authorization' => "Bearer " . $accessToken,
                    'gw-dev-app-key' => $gwDevAppKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ],
                'data' => json_encode($data)
            ]);

            $pixResponse = Http::withOptions([
                'verify' => '/home/plox-dev/certificados-webhook-bb/sandbox/Apos 12-02-2025/bb-cert-chain.pem'
            ])->withHeaders([
                'Authorization' => "Bearer " . $accessToken,
                'gw-dev-app-key' => $gwDevAppKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->withBody(json_encode($data), 'application/json')->post($pixUrl);

            if (!$pixResponse->successful()) {
                Log::error("❌ Erro ao gerar cobrança Pix", ['status' => $pixResponse->status(), 'error' => $pixResponse->body()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao gerar cobrança Pix',
                    'error' => $pixResponse->body()
                ], 400);
            }

            $pix = $pixResponse->json();
            Log::info("✅ Cobrança Pix criada com sucesso", ['response' => $pix]);

            return response()->json([
                'success' => true,
                'pix_qrcode' => "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($pix['pixCopiaECola']),
                'pix_copiaecola' => $pix['pixCopiaECola'],
                'txid' => $pix['txid'],
                'location' => $pix['location']
            ]);
        } catch (\Exception $e) {
            Log::error("❌ Erro interno no servidor", ['exception' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro interno no servidor',
                'exception' => $e->getMessage()
            ], 500);
        }
    }


    private function getAccessToken()
{
    $clientId = "eyJpZCI6ImExZDc5ZmJmLTgzN2YtNDYwMi1iYjdiLTk3YmRhMDdjNmNkNSIsImNvZGlnb1B1YmxpY2Fkb3IiOjAsImNvZGlnb1NvZnR3YXJlIjoxMjM0MzMsInNlcXVlbmNpYWxJbnN0YWxhY2FvIjoxfQ";
    $clientSecret = "eyJpZCI6ImQxNjhkZmItYzhjYy00NjIiLCJjb2RpZ29QdWJsaWNhZG9yIjowLCJjb2RpZ29Tb2Z0d2FyZSI6MTIzNDMzLCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE3MzgxMTEwMzQwNDh9";
    $tokenUrl = "https://oauth.sandbox.bb.com.br/oauth/token";

    $tokenResponse = Http::asForm()->withOptions([
        'verify' => '/home/plox-dev/certificados-webhook-bb/sandbox/Apos 12-02-2025/bb-cert-chain.pem'
    ])->withHeaders([
        'Authorization' => 'Basic ' . base64_encode("$clientId:$clientSecret"),
        'Accept' => 'application/json'
    ])->post($tokenUrl, [
        'grant_type' => 'client_credentials',
        'scope' => 'cob.write cob.read pix.read pix.write'
    ]);

    if (!$tokenResponse->successful()) {
        Log::error("❌ Erro ao obter token", ['status' => $tokenResponse->status(), 'error' => $tokenResponse->body()]);
        throw new \Exception("Erro ao obter token de acesso.");
    }

    return $tokenResponse->json()['access_token'];
}


public function verificarPagamento(Request $request)
{
    try {
        $txid = $request->txid;
        if (!$txid) {
            return response()->json([
                'success' => false,
                'message' => 'TXID não informado.'
            ], 400);
        }

        Log::info("🔹 Verificando pagamento do PIX", ['txid' => $txid]);

        // 🔹 Credenciais da API do BB
        $gwDevAppKey = "c27196995c7578b34bfbbf6ff99c5a3e";
        $accessToken = $this->getAccessToken(); // Obtém o token de acesso novamente

        // 🔹 URL de consulta
        $pixUrl = "https://api.hm.bb.com.br/pix/v2/cob/{$txid}";

        Log::info("🔹 Enviando requisição para verificar pagamento", ['url' => $pixUrl]);

        $response = Http::withOptions([
            'verify' => '/home/plox-dev/certificados-webhook-bb/sandbox/Apos 12-02-2025/bb-cert-chain.pem'
        ])->withHeaders([
            'Authorization' => "Bearer " . $accessToken,
            'gw-dev-app-key' => $gwDevAppKey,
            'Accept' => 'application/json'
        ])->get($pixUrl);

        if (!$response->successful()) {
            Log::error("❌ Erro ao verificar pagamento PIX", ['status' => $response->status(), 'error' => $response->body()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao verificar pagamento PIX',
                'error' => $response->body()
            ], 400);
        }

        $pix = $response->json();
        Log::info("✅ Resposta da API PIX", ['response' => $pix]);

        // 🔹 Se o status for "CONCLUIDA", significa que o PIX foi pago
        if (isset($pix['status']) && $pix['status'] === "CONCLUIDA") {
            // 🔹 Captura os dados recebidos do frontend
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

            Log::info("✅ Horário reservado com sucesso!", ['schedule_id' => $scheduleId, 'user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => 'Pagamento confirmado e horário reservado!',
                'status' => $pix['status']
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Pagamento ainda não foi realizado',
                'status' => $pix['status'] ?? 'Desconhecido'
            ]);
        }

    } catch (\Exception $e) {
        Log::error("❌ Erro interno ao verificar pagamento", ['exception' => $e->getMessage()]);
        return response()->json([
            'success' => false,
            'message' => 'Erro interno ao verificar pagamento',
            'exception' => $e->getMessage()
        ], 500);
    }
}


}
