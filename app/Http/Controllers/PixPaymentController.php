<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PixAccount;
use App\Models\Schedule;
use App\Services\Pix\PixServiceFactory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;

class PixPaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        try {
            Log::info("🔹 Recebendo solicitação para criar pagamento PIX", [
                'user_id' => Auth::id(),
                'dados_recebidos' => $request->all(),
            ]);

            $user = Auth::user();
            $pixAccount = PixAccount::where('user_id', $user->id)->first();

            if (!$pixAccount) {
                Log::warning("⚠️ Conta PIX não configurada para o usuário.", ['user_id' => $user->id]);
                return response()->json(['error' => 'Conta PIX não configurada para este usuário.'], 400);
            }

            // 🔹 Obtendo a chave Pix diretamente do banco de dados
            $chavePix = $pixAccount->pix_key;
            if (!$chavePix) {
                Log::error("❌ Erro: Chave Pix não encontrada no banco de dados.", ['user_id' => $user->id]);
                return response()->json(['error' => 'Chave Pix não encontrada.'], 400);
            }

            // 🔹 Ajustando o valor corretamente
            $amount = number_format((float) $request->valor, 2, '.', '');
            Log::info("💰 Valor ajustado para o pagamento: {$amount}");

            // 🔹 Obtendo o serviço correto com base no banco do usuário
            $pixService = PixServiceFactory::create($pixAccount);
            if (!$pixService) {
                Log::error("❌ Nenhum serviço PIX encontrado para o banco selecionado.", ['banco' => $pixAccount->bank_name]);
                return response()->json(['error' => 'Banco não suportado.'], 400);
            }

            // 🔹 Criando pagamento PIX
            $response = $pixService->createPayment($amount, $user->id);
            Log::info("✅ Pagamento PIX criado com sucesso.", ['response' => $response]);

            // 🔹 Verificando se o QR Code e o Pix Copia e Cola estão retornando corretamente
            if (!isset($response['pix_copiaecola']) || !isset($response['location'])) {
                Log::error("❌ Erro: Resposta do Banco do Brasil não contém código Pix válido.", ['response' => $response]);
                return response()->json([
                    'error' => 'Erro ao gerar QR Code Pix. Resposta inválida do banco.'
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pagamento criado com sucesso!',
                'pix_qrcode' => "https://chart.googleapis.com/chart?chs=250x250&cht=qr&chl=" . urlencode($response['pix_copiaecola']),
                'pix_copiaecola' => $response['pix_copiaecola'],
                'txid' => $response['txid'],
                'location' => $response['location']
            ]);
        } catch (Exception $e) {
            Log::error("❌ Erro ao criar pagamento PIX", ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function checkPaymentStatus(Request $request)
    {
        try {
            Log::info("🔹 Verificando status do pagamento PIX", ['txid' => $request->txid]);

            $user = Auth::user();
            $pixAccount = PixAccount::where('user_id', $user->id)->first();

            if (!$pixAccount) {
                Log::warning("⚠️ Conta PIX não configurada para o usuário.", ['user_id' => $user->id]);
                return response()->json(['error' => 'Conta PIX não configurada para este usuário.'], 400);
            }

            // 🔹 Obtendo o serviço correto para consulta
            $pixService = PixServiceFactory::create($pixAccount);
            if (!$pixService) {
                Log::error("❌ Nenhum serviço PIX encontrado para consulta.", ['banco' => $pixAccount->bank_name]);
                return response()->json(['error' => 'Banco não suportado.'], 400);
            }

            // 🔹 Consultando status do pagamento
            $response = $pixService->checkPaymentStatus($request->txid);
            Log::info("✅ Status do PIX verificado com sucesso.", ['response' => $response]);

            return response()->json(['message' => $response]);
        } catch (Exception $e) {
            Log::error("❌ Erro ao verificar pagamento PIX", ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function lockSchedule(Request $request)
{
    $scheduleId = $request->schedule_id;

    if (!$scheduleId) {
        return response()->json(['success' => false, 'message' => 'ID do agendamento não informado.']);
    }

    $schedule = Schedule::find($scheduleId);

    if (!$schedule || $schedule->is_booked) {
        return response()->json(['success' => false, 'message' => 'Horário já foi reservado ou não existe.']);
    }

    $schedule->update([
        'is_locked' => 1,
        'locked_until' => now()->addMinutes(10) // Define um tempo de bloqueio maior
    ]);

    return response()->json(['success' => true, 'message' => 'Horário bloqueado com sucesso.']);
}

public function unlockSchedule(Request $request)
{
    $scheduleId = $request->schedule_id;

    if (!$scheduleId) {
        return response()->json(['success' => false, 'message' => 'ID do agendamento não informado.']);
    }

    $schedule = Schedule::find($scheduleId);

    if (!$schedule || $schedule->is_booked) {
        return response()->json(['success' => false, 'message' => 'Horário já foi reservado ou não existe.']);
    }

    $schedule->update([
        'is_locked' => 0,
        'locked_until' => null
    ]);

    return response()->json(['success' => true, 'message' => 'Horário liberado com sucesso.']);
}

}
