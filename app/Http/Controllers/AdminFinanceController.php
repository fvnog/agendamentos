<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class AdminFinanceController extends Controller
{
    public function index()
    {
        $barbeiroId = Auth::id(); // Pega o ID do barbeiro logado

        // 🔹 Filtra os pagamentos apenas do barbeiro logado
        $pagamentos = Payment::whereHas('schedule', function ($query) use ($barbeiroId) {
            $query->where('user_id', $barbeiroId);
        })->get();

        // 🔹 Valores arrecadados do barbeiro logado
        $totalGanhos = $pagamentos->sum('amount');
        $ganhosMes = $pagamentos->whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('amount');
        $ganhosSemana = $pagamentos->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
        $ganhosDia = $pagamentos->where('created_at', '>=', Carbon::today())->sum('amount');

        // 🔹 Contagem de pagamentos
        $pagamentosRealizados = $pagamentos->count();

        // 🔹 Serviços mais vendidos
        $servicosMaisVendidos = [];
        foreach ($pagamentos as $pagamento) {
            $services = json_decode($pagamento->services, true);
            if ($services) {
                foreach ($services as $service) {
                    if (isset($servicosMaisVendidos[$service['name']])) {
                        $servicosMaisVendidos[$service['name']]++;
                    } else {
                        $servicosMaisVendidos[$service['name']] = 1;
                    }
                }
            }
        }

        arsort($servicosMaisVendidos); // Ordena do mais vendido para o menos vendido

        return view('admin.payments.index', compact(
            'totalGanhos',
            'ganhosMes',
            'ganhosSemana',
            'ganhosDia',
            'pagamentosRealizados',
            'servicosMaisVendidos',
            'pagamentos'
        ));
    }
}
