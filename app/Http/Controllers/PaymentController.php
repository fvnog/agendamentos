<?php

namespace App\Http\Controllers;

use App\Services\ItauPixService;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\Schedule;


class PaymentController extends Controller
{
    public function create(Request $request, ItauPixService $pixService)
    {
        $scheduleId = $request->input('schedule_id');
        $value = $request->input('value');

        // Gerar um TXID único
        $txid = 'SCHEDULE' . $scheduleId . uniqid();

        // Criar cobrança Pix
        $pix = $pixService->createPix($value, $txid);

        // Salvar no banco de dados
        Payment::create([
            'schedule_id' => $scheduleId,
            'txid' => $txid,
            'value' => $value,
            'status' => 'pending',
        ]);

        return view('payments.qrcode', [
            'qrcode' => $pix['qrcode'],
            'payload' => $pix['pix'],
            'expiration' => 10, // minutos
        ]);
    }

    public function show(Request $request)
    {
        $qrcode = $request->input('qrcode');
        $payload = $request->input('payload');
        $expiration = $request->input('expiration');
        $schedule = Schedule::find($request->input('schedule_id'));

        if (!$qrcode || !$payload) {
            return back()->with('error', 'Erro ao gerar o QR Code do Pix.');
        }

        return view('payments.qrcode', compact('qrcode', 'payload', 'expiration', 'schedule'));
    }



    public function checkPayment(Request $request, ItauPixService $pixService)
    {
        $txid = $request->input('txid');

        // Buscar o pagamento no banco
        $payment = Payment::where('txid', $txid)->first();

        if (!$payment) {
            return back()->with('error', 'Transação não encontrada.');
        }

        try {
            // Consultar status do Pix
            $details = $pixService->getPixDetails($txid);

            // Verificar o status retornado pela API
            if ($details['status'] === 'CONCLUIDO') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                // Marcar o horário como reservado
                $payment->schedule->update(['is_booked' => true]);

                return redirect()->route('client.schedule.index')->with('success', 'Pagamento confirmado e horário reservado!');
            }

            return back()->with('error', 'Pagamento ainda não foi confirmado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao consultar o status do pagamento: ' . $e->getMessage());
        }
    }
}
