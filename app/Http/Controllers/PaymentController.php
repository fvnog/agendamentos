<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Service;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\Client\Payment\PaymentClient;

class PaymentController extends Controller
{
    /**
     * Polling para verificar status do pagamento
     */
    public function checkStatus(Request $request)
    {
        $paymentId = $request->query('payment_id');
        if (!$paymentId) {
            return response()->json(['status' => 'error', 'message' => 'payment_id ausente.'], 400);
        }

        try {
            Log::info('🔍 checkStatus() iniciado', ['payment_id' => $paymentId]);

            MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
            $payment = (new PaymentClient())->get($paymentId);

            Log::info('📥 Resposta Mercado Pago', [
                'payment_id' => $payment->id,
                'status'     => $payment->status,
                'metadata'   => $payment->metadata,
                'external_reference' => $payment->external_reference
            ]);

            if ($payment->status === 'approved') {
                $this->finalizeReservationFromPayment($payment);
                return response()->json(['status' => 'approved']);
            }

            return response()->json(['status' => $payment->status]);

        } catch (\Exception $e) {
            Log::error('❌ Erro no checkStatus', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Cria a preferência e redireciona para o Mercado Pago
     * Aceita tanto:
     *  - Radio (1 serviço):  service_id
     *  - Checkboxes (n serviços): services[]
     */
    public function showPaymentPage(Request $request)
    {
        // Validação básica
        $validated = $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'barber_id'   => ['required', 'integer', 'exists:users,id'],
            // service_id OU services[] (pelo menos um deles)
            'service_id'  => ['nullable', 'integer', 'exists:services,id'],
            'services'    => ['nullable', 'array'],
            'services.*'  => ['integer', 'exists:services,id'],
        ]);

        // Entidades principais
        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $user     = Auth::user();
        $barber   = User::findOrFail($validated['barber_id']);

        // Compat: radio (service_id) OU checkboxes (services[])
        $serviceIds = $request->input('services', []);
        if (empty($serviceIds) && $request->filled('service_id')) {
            $serviceIds = [(int) $request->input('service_id')];
        }

        // Garantia de array de inteiros e sem duplicatas
        $serviceIds = array_values(array_unique(array_map('intval', (array) $serviceIds)));

        if (empty($serviceIds)) {
            return back()->with('error', 'Selecione ao menos um serviço.')->withInput();
        }

        // Busca serviços
        $services = Service::whereIn('id', $serviceIds)->get();

        if ($services->isEmpty()) {
            return back()->with('error', 'Serviços não encontrados.')->withInput();
        }

        // Totais
        $serviceIds = $services->pluck('id')->toArray();
        $total      = (float) $services->sum('price');

        Log::info('🛒 Iniciando fluxo de pagamento', [
            'schedule_id' => $schedule->id,
            'user_id'     => optional($user)->id,
            'barber_id'   => $barber->id,
            'services'    => $serviceIds,
            'total'       => $total
        ]);

        // Mercado Pago
        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
        $client = new PreferenceClient();

        try {
            $items = $services->map(function (Service $service) {
                return [
                    "title"       => $service->name,
                    "quantity"    => 1,
                    "unit_price"  => (float) $service->price,
                    "currency_id" => "BRL",
                ];
            })->toArray();

            $preference = $client->create([
                "items" => $items,
                "back_urls" => [
                    "success" => route('payment.success'),
                    "failure" => route('payment.failure'),
                    "pending" => route('payment.pending'),
                ],
                "auto_return" => "approved",
                "external_reference" =>
                    "reserva_{$schedule->id}_user_" . optional($user)->id . "_barber_{$barber->id}_services_" .
                    implode('-', $serviceIds) . "_total_{$total}",
                "metadata" => [
                    "schedule_id" => $schedule->id,
                    "customer_id" => optional($user)->id,
                    "barber_id"   => $barber->id,
                    "service_ids" => $serviceIds,
                    "total_amount"=> $total,
                ]
            ]);

            $metadata = property_exists($preference, 'metadata') && isset($preference->metadata)
                ? (array) $preference->metadata
                : [];

            Log::info('📤 Preferência criada no Mercado Pago', [
                'preference_id'      => $preference->id ?? null,
                'init_point'         => $preference->init_point ?? null,
                'external_reference' => $preference->external_reference ?? null,
                'metadata'           => $metadata
            ]);

            // Redireciona para o checkout
            return redirect($preference->init_point);

        } catch (MPApiException $e) {
            Log::error('❌ Erro ao criar preferência no Mercado Pago', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao criar pagamento: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('❌ Erro inesperado ao criar pagamento', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro inesperado ao criar pagamento. Tente novamente.');
        }
    }

    public function success(Request $request)
    {
        Log::info('✅ Tela de sucesso exibida após pagamento', [
            'query'   => $request->all(),
            'raw_get' => $_GET
        ]);

        return view('client.payment_success')
            ->with('message', 'Pagamento aprovado! Estamos confirmando sua reserva...');
    }

    public function failure()
    {
        return view('client.payment_failure');
    }

    public function pending(Request $request)
    {
        Log::info('⏳ Usuário na tela de pagamento pendente', [
            'query'   => $request->all(),
            'raw_get' => $_GET
        ]);

        return view('client.payment_pending');
    }

    /**
     * Marca o horário e salva dados do cliente/serviços/valor/pagamento
     */
    private function finalizeReservationFromPayment($payment): void
    {
        $metadata    = (array) ($payment->metadata ?? []);
        $scheduleId  = $metadata['schedule_id'] ?? null;
        $customerId  = $metadata['customer_id'] ?? null;
        $barberId    = $metadata['barber_id'] ?? null;
        $serviceIds  = $metadata['service_ids'] ?? [];
        $totalAmount = $metadata['total_amount'] ?? ($payment->transaction_amount ?? null);

        // Fallback pelo external_reference
        if (!$scheduleId || !$customerId) {
            $ext = $payment->external_reference ?? '';
            if (preg_match('/^reserva_(\d+)_user_(\d+)_barber_(\d+)_services_([0-9\-]+)_total_([0-9\.]+)$/', $ext, $m)) {
                $scheduleId  = $scheduleId ?: (int) $m[1];
                $customerId  = $customerId ?: (int) $m[2];
                $barberId    = $barberId ?: (int) $m[3];
                $serviceIds  = $serviceIds ?: explode('-', $m[4]);
                $totalAmount = $totalAmount ?: (float) $m[5];
            }
        }

        Log::info('📝 Iniciando atualização da reserva', [
            'schedule_id' => $scheduleId,
            'customer_id' => $customerId,
            'barber_id'   => $barberId,
            'services'    => $serviceIds,
            'total'       => $totalAmount
        ]);

        if (!$scheduleId) {
            Log::warning('⚠️ Pagamento aprovado mas sem schedule_id', ['payment_id' => $payment->id]);
            return;
        }

        $schedule = Schedule::find($scheduleId);
        if (!$schedule) {
            Log::warning('⚠️ Schedule não encontrado', ['schedule_id' => $scheduleId]);
            return;
        }

        // Atualiza sempre, mesmo que já esteja marcado
        $schedule->update([
            'is_booked'      => 1,
            'is_locked'      => 0,
            'locked_until'   => null,
            'client_id'      => $customerId,
            'user_id'        => $barberId,
            'booked_by'      => $customerId,
            'barber_id'      => $barberId,
            'services'       => $serviceIds,
            'services_json'  => $serviceIds,
            'amount_paid'    => $totalAmount,
            'payment_id'     => $payment->id,
            'payment_status' => $payment->status,
        ]);

        Log::info('📅 Reserva salva/atualizada com sucesso', [
            'schedule_id' => $scheduleId,
            'payment_id'  => $payment->id
        ]);
    }
}
