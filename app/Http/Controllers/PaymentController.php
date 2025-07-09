<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Service;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Net\MPSearchRequest;


class PaymentController extends Controller
{

public function checkStatus(Request $request)
{
    try {
        $paymentId = $request->query('payment_id');
        \Log::info('🔍 Verificando status por payment_id', ['payment_id' => $paymentId]);

        if (!$paymentId) {
            return response()->json(['status' => 'error', 'message' => 'payment_id ausente.'], 400);
        }

        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
        $client = new PaymentClient();

        $payment = $client->get($paymentId);

        if ($payment->status === 'approved') {
            return response()->json(['status' => 'approved']);
        }

        return response()->json(['status' => 'pending']);
    } catch (\Exception $e) {
        \Log::error('❌ Erro ao consultar pagamento:', ['message' => $e->getMessage()]);
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

    public function showPaymentPage(Request $request)
    {
        // Recupera dados
        $schedule = Schedule::findOrFail($request->schedule_id);
        $user = User::findOrFail($request->barber_id);
        $barber = $user->isAdmin() ? $user : null;
        $selectedServices = Service::find($request->services);

        // Token
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

        // Itens em array simples
        $items = [];
        foreach ($selectedServices as $service) {
            $items[] = [
                "title" => $service->name,
                "quantity" => 1,
                "unit_price" => (float) $service->price,
                "currency_id" => "BRL"
            ];
        }

        // Cliente da preferência
        $client = new PreferenceClient();

        try {
            // Cria preferência via array
            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success" => route('payment.success'),
                    "failure" => route('payment.failure'),
                    "pending" => route('payment.pending'),
                ],
                "auto_return" => "approved",
                "external_reference" => "reserva_" . uniqid(),
                "metadata" => [
                    "schedule_id" => $schedule->id,
                    "barber_id" => $barber?->id,
                ]
            ]);

            return redirect($preference->init_point);
        } catch (MPApiException $e) {
            return back()->with('error', 'Erro ao criar pagamento: ' . $e->getMessage());
        }
    }

public function success(Request $request)
{
    try {
        \Log::info('✅ Pagamento aprovado com retorno do Mercado Pago', [
            'query' => $request->all(),
            'raw_get' => $_GET
        ]);

        // 🔒 Força uso direto da query string
        $paymentId = $_GET['payment_id'] ?? null;

        if (!$paymentId) {
            \Log::warning('⚠️ payment_id ausente no retorno do Mercado Pago.');
            return view('client.payment_failure')->with('message', 'Pagamento aprovado, mas não foi possível verificar o agendamento.');
        }

        // Configura SDK
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
        $client = new PaymentClient();
        $payment = $client->get($paymentId);

        // Busca o ID do agendamento salvo no metadata
        $scheduleId = $payment->metadata['schedule_id'] ?? null;

        if (!$scheduleId) {
            \Log::warning('⚠️ schedule_id não encontrado no metadata do pagamento.', [
                'payment_id' => $paymentId
            ]);
            return view('client.payment_failure')->with('message', 'Pagamento confirmado, mas agendamento não encontrado.');
        }

        $schedule = Schedule::find($scheduleId);

        if (!$schedule) {
            \Log::error('❌ Agendamento não encontrado no banco.', ['schedule_id' => $scheduleId]);
            return view('client.payment_failure')->with('message', 'Agendamento não encontrado.');
        }

        if ($schedule->is_booked) {
            \Log::info('📌 Agendamento já estava marcado.', ['schedule_id' => $scheduleId]);
        } else {
            $schedule->update([
                'is_booked' => 1,
                'is_locked' => 0,
                'locked_until' => null,
            ]);

            \Log::info('📅 Horário reservado com sucesso!', ['schedule_id' => $scheduleId]);
        }

        // ✅ Exibe tela de sucesso
        return view('client.payment_success')->with('message', 'Pagamento aprovado e horário reservado com sucesso.');

    } catch (\Exception $e) {
        \Log::error('❌ Erro ao processar reserva após sucesso do pagamento', [
            'error' => $e->getMessage()
        ]);
        return view('client.payment_failure')->with('message', 'Pagamento confirmado, mas houve erro ao reservar seu horário.');
    }
}

public function webhook(Request $request)
{
    \Log::info('📥 Webhook recebido do Mercado Pago', ['payload' => $request->all()]);

    $action = $request->input('action');
    $type = $request->input('type');
    $paymentId = $request->input('data.id');

    if ($action === 'payment.updated' && $type === 'payment') {
        try {
            MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));

            $client = new \MercadoPago\Client\Payment\PaymentClient();
            $payment = $client->get($paymentId);

            if ($payment->status === 'approved') {
                $externalReference = $payment->external_reference; // ex: reserva_123abc
                \Log::info("✅ Pagamento aprovado via webhook", ['payment_id' => $paymentId, 'ref' => $externalReference]);

                // Extrai o ID do agendamento
                if (str_starts_with($externalReference, 'reserva_')) {
                    $scheduleId = str_replace('reserva_', '', $externalReference);
                    $schedule = Schedule::find($scheduleId);

                    if ($schedule && !$schedule->is_booked) {
                        $schedule->update([
                            'is_booked' => 1,
                            'is_locked' => 0,
                            'locked_until' => null,
                        ]);

                        \Log::info('📅 Horário reservado com sucesso via webhook', ['schedule_id' => $scheduleId]);
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('❌ Erro ao processar webhook do Mercado Pago', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Erro interno'], 500);
        }
    }

    return response()->json(['success' => true]);
}



    public function failure()
    {
        return view('client.payment_failure');
    }

    public function pending()
    {
        return view('client.payment_pending');
    }
}
