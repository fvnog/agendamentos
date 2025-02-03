<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\Reservation;
use Auth;
use Illuminate\Support\Facades\Cache;

class PixPaymentController extends Controller
{
    const CLIENT_ID = 'eyJpZCI6ImExZDc5ZmJmLTgzN2YtNDYwMi1iYjdiLTk3YmRhMDdjNmNkNSIsImNvZGlnb1B1YmxpY2Fkb3IiOjAsImNvZGlnb1NvZnR3YXJlIjoxMjM0MzMsInNlcXVlbmNpYWxJbnN0YWxhY2FvIjoxfQ';
    const CLIENT_SECRET = 'eyJpZCI6ImQxNjhkZmItYzhjYy00NjIiLCJjb2RpZ29QdWJsaWNhZG9yIjowLCJjb2RpZ29Tb2Z0d2FyZSI6MTIzNDMzLCJzZXF1ZW5jaWFsSW5zdGFsYWNhbyI6MSwic2VxdWVuY2lhbENyZWRlbmNpYWwiOjEsImFtYmllbnRlIjoiaG9tb2xvZ2FjYW8iLCJpYXQiOjE3MzgxMTEwMzQwNDh9';
    const TOKEN_URL = 'https://oauth.sandbox.bb.com.br/oauth/token';

    public function getAccessToken()
    {
        // Verifica se o token já está armazenado em cache
        $cachedToken = Cache::get('bb_oauth_token');
        
        // Se o token está em cache e ainda é válido, retorna ele
        if ($cachedToken) {
            return $cachedToken;
        }
        
        // Caso contrário, faz uma requisição para obter um novo token
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'grant_type' => 'client_credentials',
            'client_id' => self::CLIENT_ID,
            'client_secret' => self::CLIENT_SECRET,
            'scope' => 'cob.write cob.read pix.read pix.write',
        ]);

        if ($response->successful()) {
            $accessToken = $response->json()['access_token'];

            // Armazena o token em cache por um tempo (ex: 3600 segundos - 1 hora)
            Cache::put('bb_oauth_token', $accessToken, now()->addSeconds(3600));

            return $accessToken;
        }

        throw new \Exception('Erro ao obter token de autenticação');
    }

    public function createPayment(Request $request)
    {
        // Obtém o token de acesso
        $accessToken = $this->getAccessToken();

        // Os dados para criar a cobrança
        $user = Auth::user();
        $schedule = Schedule::find($request->schedule_id);
        $selectedServices = Service::whereIn('id', $request->services)->get();

        // Calcula o valor total
        $totalValue = $selectedServices->sum('price');

        // Define a chave Pix do recebedor (pode ser seu CNPJ ou chave)
        $chavePix = '9e881f18-cc66-4fc7-8f2c-a795dbb2bfc1';

        // Dados para criar a cobrança
        $cobData = [
            'calendario' => [
                'criacao' => now()->toIso8601String(),
                'expiracao' => 3600, // 1 hora de expiração
            ],
            'devedor' => [
                'cnpj' => '12345678000195', // O CNPJ do recebedor
                'nome' => 'Empresa de Serviços SA',
            ],
            'valor' => [
                'original' => number_format($totalValue, 2, '.', ''),
            ],
            'chave' => $chavePix,
            'solicitacaoPagador' => 'Pagamento de Serviço de Barbearia',
            'infoAdicionais' => [
                [
                    'nome' => 'Serviços Selecionados',
                    'valor' => $selectedServices->pluck('name')->join(', '),
                ],
            ],
        ];

        // Faz a requisição para a API do Banco do Brasil para criar a cobrança
        $response = Http::withOptions([
            'verify' => 'C:/Certificados/cacert.pem', // Caminho do arquivo CA certificado
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post('https://api.hm.bb.com.br/pix/v2/cob', $cobData);
        
        

        if ($response->successful()) {
            $cob = $response->json();

            // Salvar a cobrança no banco de dados (pode ser um modelo Cobrança)
            $reservation = new Reservation();
            $reservation->user_id = $user->id;
            $reservation->schedule_id = $schedule->id;
            $reservation->total_value = $totalValue;
            $reservation->pix_txid = $cob['txid'];
            $reservation->status = 'Pendente';
            $reservation->save();

            // Retorna para a tela de pagamento com os dados Pix
            return view('client.payment', [
                'pix_url' => $cob['location'],
                'pix_qrcode' => $cob['pixCopiaECola'],
                'total_value' => $totalValue,
                'reservation' => $reservation
            ]);
        } else {
            return back()->withErrors(['error' => 'Erro ao criar a cobrança Pix.']);
        }
    }

    public function checkPaymentStatus($txid)
    {
        // Obtém o token de acesso
        $accessToken = $this->getAccessToken();

        // Faz a requisição para a API para verificar o status do pagamento
        $response = Http::withOptions([
            'verify' => false,
        ])->withHeaders([
            'Authorization' => 'Bearer ' . $accessToken,
        ])->get("https://api.hm.bb.com.br/pix/v2/cob/{$txid}");
        

        if ($response->successful()) {
            $paymentStatus = $response->json();

            // Verifica o status do pagamento
            if ($paymentStatus['status'] == 'PAID') {
                // Atualiza o status da reserva para pago
                $reservation = Reservation::where('pix_txid', $txid)->first();
                if ($reservation) {
                    $reservation->status = 'Pago';
                    $reservation->save();
                }
                return redirect()->route('client.schedule.index')->with('success', 'Pagamento confirmado!');
            } else {
                return back()->withErrors(['error' => 'O pagamento ainda não foi confirmado.']);
            }
        } else {
            return back()->withErrors(['error' => 'Erro ao verificar o pagamento.']);
        }
    }
}
