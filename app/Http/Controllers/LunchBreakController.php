<?php

namespace App\Http\Controllers;

use App\Models\LunchBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LunchBreakController extends Controller
{
    public function index()
    {
        // Verifica se já existe um horário de almoço para o usuário logado
        $lunchBreak = LunchBreak::where('user_id', Auth::id())->first();

        // Se houver, envia para edição; se não, cria um novo
        return $lunchBreak 
            ? view('lunch-break.edit', compact('lunchBreak')) 
            : view('lunch-break.create');
    }

    public function edit()
    {
        // Busca o horário do usuário logado
        $lunchBreak = LunchBreak::where('user_id', Auth::id())->first();

        if (!$lunchBreak) {
            // Se não existir, redireciona para a criação
            return redirect()->route('lunch-break.create');
        }

        // Formata os horários corretamente
        $lunchBreak->start_time = Carbon::parse($lunchBreak->start_time)->format('H:i');
        $lunchBreak->end_time = Carbon::parse($lunchBreak->end_time)->format('H:i');

        return view('lunch-break.edit', compact('lunchBreak'));
    }

    public function store(Request $request)
    {
        // Validação do horário de almoço
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Busca ou cria o horário de almoço do usuário logado
        $lunchBreak = LunchBreak::updateOrCreate(
            ['user_id' => Auth::id()],
            ['start_time' => $validated['start_time'], 'end_time' => $validated['end_time']]
        );

        return redirect()->route('dashboard')->with('success', 'Horário de almoço atualizado com sucesso!');
    }
}
