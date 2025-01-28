<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048', // Máx 2MB
        ]);

        $photoPath = $request->file('photo')->store('services', 'public');


        Service::create([
            'name' => $request->name,
            'duration' => $request->duration,
            'price' => $request->price,
            'photo' => $photoPath,
        ]);

        return redirect()->route('services.index')->with('success', 'Serviço criado com sucesso!');
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'duration' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->file('photo')) {
            $photoPath = $request->file('photo')->store('services', 'public');
            $service->photo = $photoPath;
        }

        $service->update([
            'name' => $request->name,
            'duration' => $request->duration,
            'price' => $request->price,
        ]);

        return redirect()->route('services.index')->with('success', 'Serviço atualizado com sucesso!');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Serviço excluído com sucesso!');
    }
}
