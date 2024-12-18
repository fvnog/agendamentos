<?php

namespace App\Http\Controllers;

use App\Models\LunchBreak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LunchBreakController extends Controller
{
    public function index()
    {
        // Verifica se já existe um horário de almoço para o usuário
        $user = Auth::user();
        $lunchBreak = LunchBreak::where('user_id', $user->id)->first();

        // Se já houver um horário de almoço, passa ele para a view para edição
        if ($lunchBreak) {
            return view('lunch-break.edit', compact('lunchBreak'));
        }

        return view('lunch-break.create');
    }

    public function edit($id)
{
    $lunchBreak = LunchBreak::where('user_id', Auth::id())->first();

    if ($lunchBreak) {
        // Formatando os horários para o formato correto
        $lunchBreak->start_time = \Carbon\Carbon::parse($lunchBreak->start_time)->format('H:i');
        $lunchBreak->end_time = \Carbon\Carbon::parse($lunchBreak->end_time)->format('H:i');
    }

    return view('lunch-break.edit', compact('lunchBreak'));
}



    public function store(Request $request)
    {
        // Validação do horário de almoço
        $validated = $request->validate([
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
    
        // Verificar se a hora de término é depois da hora de início
        $start_time = \Carbon\Carbon::parse($validated['start_time']);
        $end_time = \Carbon\Carbon::parse($validated['end_time']);
    
        // Verifique se o horário de término é realmente após o horário de início
        if ($end_time <= $start_time) {
            return redirect()->back()->withErrors(['end_time' => 'A hora de término deve ser depois da hora de início.'])->withInput();
        }
    
        // Criar ou atualizar o horário de almoço para o usuário logado
        $user = Auth::user();
        $lunchBreak = LunchBreak::where('user_id', $user->id)->first();
    
        if ($lunchBreak) {
            // Se já houver, atualiza o horário de almoço
            $lunchBreak->update([
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);
            return redirect()->route('dashboard')->with('success', 'Horário de almoço atualizado com sucesso!');
        }
    
        // Caso contrário, cria um novo horário de almoço
        LunchBreak::create([
            'user_id' => $user->id,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);
    
        return redirect()->route('dashboard')->with('success', 'Horário de almoço cadastrado com sucesso!');
    }
    
}
