<?php

namespace App\Http\Controllers;

use App\Models\FixedSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Service; // Certifique-se de importar o modelo Service

class FixedScheduleController extends Controller
{
    // Exibe a página de gerenciamento de horários fixos

    public function index()
    {
        $clients = User::where('is_admin', 0)->get();
        $barbers = User::where('is_admin', 1)->get();
        $fixedSchedules = FixedSchedule::with('client', 'barber')->get();
        $services = Service::all(); // Obtém todos os serviços
    


    // Recuperar serviços vinculados
    foreach ($fixedSchedules as $fixed) {
        if (!empty($fixed->services)) {
            $serviceIds = json_decode($fixed->services, true);

            if (is_array($serviceIds) && !empty($serviceIds)) {
                // Buscando os serviços pelo ID
                $fixed->service_names = Service::whereIn('id', $serviceIds)->pluck('name')->toArray();
            } else {
                $fixed->service_names = [];
            }
        } else {
            $fixed->service_names = [];
        }
    }
    
        return view('schedules.fixed', compact('clients', 'barbers', 'fixedSchedules', 'services'));
    }
    

    // Armazena um novo horário fixo
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:users,id',
            'barber_id' => 'required|exists:users,id',
            'weekday' => 'required|integer|min:0|max:6',
            'fixed_time' => 'required|date_format:H:i',
        ]);

        FixedSchedule::create([
            'client_id' => $validated['client_id'],
            'barber_id' => $validated['barber_id'],
            'weekday' => $validated['weekday'],
            'start_time' => $validated['fixed_time'],
            'services' => json_encode([]), // Inicia vazio
        ]);

        return redirect()->route('schedules.fixed.index')->with('success', 'Horário fixo criado com sucesso!');
    }

    // Atualiza os serviços do cliente fixo
    public function updateServices(Request $request)
    {
        $validated = $request->validate([
            'schedule_id' => 'required|exists:fixed_schedules,id',
            'services' => 'nullable|array'
        ]);
    
        FixedSchedule::where('id', $validated['schedule_id'])->update([
            'services' => json_encode($validated['services'])
        ]);
    
        return response()->json(['success' => true]);
    }
    
    // Exclui um horário fixo
    public function delete(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:fixed_schedules,id',
        ]);

        FixedSchedule::where('id', $request->schedule_id)->delete();

        return redirect()->route('schedules.fixed.index')->with('success', 'Horário fixo removido com sucesso!');
    }
}
