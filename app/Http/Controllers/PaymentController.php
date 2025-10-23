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
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

class PaymentController extends Controller
{
    public function showPaymentPage(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => ['required', 'integer', 'exists:schedules,id'],
            'barber_id'   => ['required', 'integer', 'exists:users,id'],
            'service_id'  => ['nullable', 'integer', 'exists:services,id'],
            'services'    => ['nullable', 'array'],
            'services.*'  => ['integer', 'exists:services,id'],
        ]);

        $schedule = Schedule::findOrFail($validated['schedule_id']);
        $user     = Auth::user();
        $barber   = User::findOrFail($validated['barber_id']);

        $serviceIds = $request->input('services', []);
        if (empty($serviceIds) && $request->filled('service_id')) {
            $serviceIds = [(int) $request->input('service_id')];
        }
        $serviceIds = array_values(array_unique(array_map('intval', (array) $serviceIds)));

        if (empty($serviceIds)) {
            return back()->with('error', 'Selecione ao menos um serviço.');
        }

        $services = Service::whereIn('id', $serviceIds)->get();
        if ($services->isEmpty()) {
            return back()->with('error', 'Serviços não encontrados.');
        }

        $total = (float) $services->sum('price');

        Log::info('🛒 Iniciando pagamento', [
            'schedule_id' => $schedule->id,
            'user_id'     => optional($user)->id,
            'barber_id'   => $barber->id,
            'services'    => $serviceIds,
            'total'       => $total
        ]);

        MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
        $client = new PreferenceClient();

        try {
            $items = $services->map(fn($s) => [
                "title"       => $s->name,
                "quantity"    => 1,
                "unit_price"  => (float) $s->price,
                "currency_id" => "BRL",
            ])->toArray();

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
                    "schedule_id"   => $schedule->id,
                    "customer_id"   => optional($user)->id,
                    "barber_id"     => $barber->id,
                    "service_ids"   => $serviceIds,
                    "total_amount"  => $total,
                ],
                "notification_url" => "https://gsbarbeiro.com.br/webhook/mercadopago"
            ]);

            Log::info('📤 Preferência criada', [
                'preference_id' => $preference->id ?? null,
                'init_point'    => $preference->init_point ?? null,
                'metadata'      => $preference->metadata ?? []
            ]);

            return redirect($preference->init_point);
        } catch (MPApiException $e) {
            Log::error('❌ Erro MP', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro ao criar pagamento: ' . $e->getMessage());
        } catch (\Throwable $e) {
            Log::error('❌ Erro inesperado', ['error' => $e->getMessage()]);
            return back()->with('error', 'Erro inesperado. Tente novamente.');
        }
    }

    public function success(Request $request)
    {
        Log::info('✅ Tela sucesso', ['query' => $request->all()]);
        return view('client.payment_success')
            ->with('message', 'Pagamento aprovado! Sua reserva será confirmada em instantes.');
    }

    public function failure()
    {
        return view('client.payment_failure');
    }

    public function pending(Request $request)
    {
        Log::info('⏳ Pagamento pendente', ['query' => $request->all()]);
        return view('client.payment_pending');
    }

    public function checkStatus(Request $request)
    {
        $paymentId = $request->query('payment_id');
        if (!$paymentId) {
            return response()->json(['status' => 'error', 'message' => 'payment_id ausente.'], 400);
        }

        try {
            Log::info('🔍 checkStatus()', ['payment_id' => $paymentId]);

            MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
            $payment = (new PaymentClient())->get($paymentId);

            if ($payment->status === 'approved') {
                $this->finalizeReservationFromPayment($payment);
                return response()->json(['status' => 'approved']);
            }

            return response()->json(['status' => $payment->status]);

        } catch (\Exception $e) {
            Log::error('❌ Erro checkStatus', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        $data = $request->all();
        Log::info('📡 Webhook recebido', ['payload' => $data]);

        try {
            if (!isset($data['type']) || $data['type'] !== 'payment') {
                return response()->json(['message' => 'Tipo não suportado'], 400);
            }

            MercadoPagoConfig::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
            $payment = (new PaymentClient())->get($data['data']['id']);

            if ($payment->status === 'approved') {
                $this->finalizeReservationFromPayment($payment);
            }

            return response()->json(['message' => 'Webhook processado']);
        } catch (\Exception $e) {
            Log::error('❌ Erro webhook', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function finalizeReservationFromPayment($payment): void
    {
        $metadata = (array) ($payment->metadata ?? []);
        $scheduleId  = $metadata['schedule_id'] ?? null;
        $customerId  = $metadata['customer_id'] ?? null;
        $barberId    = $metadata['barber_id'] ?? null;
        $serviceIds  = $metadata['service_ids'] ?? [];
        $totalAmount = $metadata['total_amount'] ?? $payment->transaction_amount;

        if (!$scheduleId || !$customerId) {
            if (preg_match('/^reserva_(\d+)_user_(\d+)_barber_(\d+)_services_([0-9\-]+)_total_([0-9\.]+)$/', $payment->external_reference, $m)) {
                $scheduleId  = (int) $m[1];
                $customerId  = (int) $m[2];
                $barberId    = (int) $m[3];
                $serviceIds  = explode('-', $m[4]);
                $totalAmount = (float) $m[5];
            }
        }

        Log::info('📝 Finalizando reserva', [
            'schedule_id' => $scheduleId,
            'customer_id' => $customerId,
            'services'    => $serviceIds,
            'total'       => $totalAmount
        ]);

        if (!$scheduleId) {
            Log::warning('⚠️ Sem schedule_id');
            return;
        }

        $schedule = Schedule::find($scheduleId);
        if (!$schedule) {
            Log::warning('⚠️ Schedule não encontrado', ['id' => $scheduleId]);
            return;
        }

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

        Log::info('📅 Reserva salva com sucesso', [
            'schedule_id' => $scheduleId,
            'payment_id'  => $payment->id
        ]);
    }
}
