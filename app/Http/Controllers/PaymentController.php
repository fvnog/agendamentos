<?php

// app/Http/Controllers/PaymentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;  // Seu modelo de agendamento
use App\Models\User;  // Importa o modelo User
use App\Models\Service;   // Seu modelo de serviços

class PaymentController extends Controller
{
// app/Http/Controllers/PaymentController.php


public function showPaymentPage(Request $request)
{
    $schedule = Schedule::find($request->schedule_id);
    $user = User::find($request->barber_id);  // Recupera o usuário (barbeiro)

    // Verifica se o usuário é admin (barbeiro)
    if ($user->isAdmin()) {
        $barber = $user; // O usuário é o barbeiro
    } else {
        // Tratar caso o usuário não seja admin (não é barbeiro)
        $barber = null;
    }

    // Recupera os serviços selecionados
    $selectedServices = Service::find($request->services);  

    // Calcula o preço total
    $totalPrice = $selectedServices->sum('price');

    // Passa as variáveis para a view
    return view('client.payment', compact('schedule', 'barber', 'selectedServices', 'totalPrice'));
}

}
