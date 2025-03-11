<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-calendar-check"></i> Gerenciar Horários
        </h2>
    </x-slot>

    <div class="py-6  min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-clock"></i> Meus Horários
                    </h3>

                    <div class="flex gap-3">
                        <!-- Filtro por Data -->
                        <form method="GET" action="{{ route('admin.schedules.index') }}" class="flex items-center gap-2">
                            <label for="date" class="text-gray-300 text-sm">Selecionar Data:</label>
                            <input type="date" name="date" id="date" value="{{ request('date', now()->toDateString()) }}"
                                class="bg-gray-800 border border-gray-600 text-white rounded-lg px-3 py-2">
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                                <i class="fas fa-search"></i> Filtrar
                            </button>
                        </form>

                        <!-- Botão para atualizar -->
                        <button onclick="location.reload()" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
                            <i class="fas fa-sync-alt"></i> Atualizar
                        </button>
                    </div>
                </div>

              <!-- Grid de Horários -->
<div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">
    @foreach($schedules as $schedule)
        <div class="bg-gray-800 shadow-md rounded-lg p-6 text-center border border-gray-700">
            <h4 class="text-2xl font-bold text-white">
                {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
            </h4>
            <p class="text-gray-300 text-lg font-semibold mt-2">
                <i class="far fa-clock"></i> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
            </p>

            @if($schedule->is_booked)
                <!-- Quando o horário está reservado -->
                <div class="mt-8 p-4 bg-green-700 rounded-lg">
                    <p class="text-xl font-bold text-white">
                        <i class="fas fa-user"></i> {{ $schedule->client->name ?? $schedule->client_name }}
                    </p>
                    @if($schedule->client && $schedule->client->telefone)
                        <p class="text-white text-sm">
                            <i class="fas fa-phone"></i> {{ $schedule->client->telefone }}
                        </p>
                    @endif

                    <!-- Exibir Serviços Agendados -->
@php
    $services = is_array($schedule->services) ? $schedule->services : json_decode($schedule->services, true);
@endphp

                    @if(!empty($services))
                        <p class="text-white font-semibold mt-2">Serviços:</p>
                        <ul class="text-white text-sm">
                            @foreach($services as $service)
                                <li><i class="fas fa-cut"></i> {{ $service['name'] }}</li>
                            @endforeach
                        </ul>
                    @endif


                    <!-- Botão para desmarcar o horário -->
                    <form method="POST" action="{{ route('admin.schedules.remove-client') }}" class="mt-3">
                        @csrf
                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                        <button type="submit" class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-times"></i> Desmarcar
                        </button>
                    </form>
                </div>
            @else
                <!-- Quando o horário está disponível -->
                <p class="text-gray-400 mt-3 text-lg font-semibold">Disponível</p>

                <!-- Formulário para agendar manualmente -->
                <form method="POST" action="{{ route('admin.schedules.add-client') }}" class="mt-4 space-y-2">
                    @csrf
                    <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">

                    <select name="user_id" class="w-full bg-gray-700 border border-gray-600 text-white rounded-md px-3 py-2 text-sm">
                        <option value="">Selecionar Usuário</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>

                    <input type="text" name="client_name" placeholder="Ou digite o nome"
                        class="w-full bg-gray-700 border border-gray-600 text-white rounded-md px-3 py-2 text-sm" />

                    <button type="submit" 
                        class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        <i class="fas fa-check"></i> Marcar
                    </button>
                </form>
            @endif
        </div>
    @endforeach
</div>


                <!-- Mensagem caso não tenha horários -->
                @if($schedules->isEmpty())
                    <p class="text-gray-400 text-center mt-6">Nenhum horário encontrado para esta data.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
