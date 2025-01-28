<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ItauPixService
{
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.itau_pix.base_url');
        $this->clientId = config('services.itau_pix.client_id');
        $this->clientSecret = config('services.itau_pix.client_secret');
    }

    // Autenticação
    public function authenticate()
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . base64_encode("{$this->clientId}:{$this->clientSecret}"),
        ])->post("{$this->baseUrl}/oauth/token", [
            'grant_type' => 'client_credentials',
            'scope' => 'pix.write',
        ]);

        if ($response->successful()) {
            return $response->json('access_token');
        }

        throw new \Exception('Erro ao autenticar na API do Itaú.');
    }

    // Criar Cobrança Pix
    public function createPix($value, $txid, $expiration = 600)
    {
        $token = $this->authenticate();

        $response = Http::withToken($token)->post("{$this->baseUrl}/pix_recebimentos_conciliacoes/v2/cobrancas_vencimento_pix", [
            'calendario' => [
                'expiracao' => $expiration,
            ],
            'valor' => [
                'original' => number_format($value, 2, '.', ''),
            ],
            'chave' => 'sua-chave-pix@exemplo.com',
            'infoAdicionais' => [
                [
                    'nome' => 'Descrição',
                    'valor' => 'Serviço de Barbearia',
                ],
            ],
        ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new \Exception('Erro ao criar cobrança Pix: ' . $response->body());
    }

    // Consultar uma cobrança específica
   public function getPixDetails($txid)
{
    $token = $this->authenticate();

    $response = Http::withToken($token)->get("{$this->baseUrl}/v1/cob/{$txid}");

    if ($response->successful()) {
        return $response->json();
    }

    throw new \Exception('Erro ao consultar o Pix.');
}

}
