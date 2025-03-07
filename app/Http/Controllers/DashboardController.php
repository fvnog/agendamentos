<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Redireciona se não for admin
        if (!auth()->user()->is_admin) {
            return redirect()->route('client.schedule.index');
        }

        $barbeiroId = Auth::id(); // Obtém o ID do barbeiro logado

        // 🔹 Total de usuários cadastrados
        $totalUsers = User::count();

        // 🔹 Agendamentos de hoje apenas do barbeiro logado
        $appointmentsToday = Schedule::where('user_id', $barbeiroId)
                                    ->whereDate('date', Carbon::today())
                                    ->where('is_booked', 1)
                                    ->count();

        // 🔹 Horários disponíveis hoje do barbeiro logado
        $pendingAppointments = Schedule::where('user_id', $barbeiroId)
                                       ->whereDate('date', Carbon::today())
                                       ->where('is_booked', 0)
                                       ->where('is_locked', 0)
                                       ->count();

        return view('dashboard', compact('totalUsers', 'appointmentsToday', 'pendingAppointments'));
    }
}
