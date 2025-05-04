<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Service;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ClientScheduleController extends Controller
{
    private $bbOauthUrl = 'https://oauth.sandbox.bb.com.br/oauth/token';
    private $bbPixUrl = 'https://api.hm.bb.com.br/pix/v2/cob';
    private $clientId = 'SEU_CLIENT_ID';
    private $clientSecret = 'SEU_CLIENT_SECRET';
    private $appKey = 'SEU_APP_KEY';

    public function index(Request $request)
    {
        $timezone = 'America/Sao_Paulo';
        $currentDate = Carbon::now($timezone)->format('Y-m-d');
        $filterDate = $request->query('date', $currentDate);

        $schedules = Schedule::whereDate('date', $filterDate)
            ->where('is_booked', false)
            ->orderBy('start_time')
            ->get();

        $services = Service::all();
        $barbers = User::where('is_admin', 1)->get();

        return view('client.schedule', compact('schedules', 'filterDate', 'services', 'barbers'));
    }

    private function getAccessToken()
    {
        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->withOptions([
                'verify' => base_path('storage/certs/bb/cacert.pem'),
                'cert' => base_path('storage/certs/bb/api_webhook_hm_bb_com_br.pem'),
            ])
            ->post($this->bbOauthUrl, [
                'grant_type' => 'client_credentials',
                'scope' => 'cob.write cob.read pix.read pix.write',
            ]);

        if ($response->successful()) {
            return $response->json()['access_token'];
        }

        abort(500, 'Erro ao obter token de acesso');
    }

    public function store(Request $request)
    {
        \Log::info('Requisição recebida:', $request->all());
        
        $request->validate([
            'valor' => 'required|numeric|min:0.01',
            'cnpj' => 'required|string',
            'nome' => 'required|string',
            'chave' => 'required|string',
            'descricao' => 'nullable|string',
        ]);

        $accessToken = $this->getAccessToken();

        $body = [
            'calendario' => ['expiracao' => 3600],
            'devedor' => [
                'cnpj' => $request->cnpj,
                'nome' => $request->nome,
            ],
            'valor' => ['original' => number_format($request->valor, 2, '.', '')],
            'chave' => $request->chave,
            'solicitacaoPagador' => $request->descricao ?? 'Pagamento via Pix.',
        ];

        $response = Http::withOptions([
                'verify' => base_path('storage/certs/bb/cacert.pem'),
            ])
            ->withToken($accessToken)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'gw-dev-app-key' => $this->appKey,
            ])
            ->post($this->bbPixUrl, $body);

        if ($response->successful()) {
            $pixData = $response->json();
            return response()->json([
                'codigoPix' => $pixData['loc']['location'],
                'qrCode' => $pixData['pixCopiaECola'],
            ], 201);
        }

        return response()->json([
            'error' => 'Erro ao criar a cobrança',
            'details' => $response->json(),
        ], 500);
    }
}
