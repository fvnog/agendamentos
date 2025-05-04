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

<!-- Grid de Horários Melhorada -->
<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
    @foreach($schedules as $schedule)
        <div class="p-6 text-center rounded-lg shadow-lg border 
                    {{ $schedule->is_booked ? 'bg-yellow-700 border-yellow-500' : 'bg-gray-800 border-gray-700' }}">
            
            <!-- Data e Hora -->
            <h4 class="text-2xl font-bold text-white">
                <i class="fas fa-calendar-day"></i> 
                {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
            </h4>
            <p class="text-lg font-semibold mt-2 {{ $schedule->is_booked ? 'text-gray-200' : 'text-yellow-400' }}">
                <i class="far fa-clock"></i> 
                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
            </p>

            @if($schedule->is_booked)
                <!-- Quando o horário está reservado -->

                <p class="text-sm text-white mt-1">
                        <i class="fas fa-check-circle"></i> Agendado
                    </p>

                <div class="mt-4 p-4 bg-gray-900 rounded-lg shadow">


                    <p class="text-lg font-bold text-white">
                        <i class="fas fa-user"></i> {{ $schedule->client->name ?? $schedule->client_name }}
                    </p>


@php
    // Garantir que services seja tratado corretamente
    $services = $schedule->services;

    if (is_string($services)) {
        $services = json_decode($services, true); // Decodifica JSON caso seja uma string
    }

    if (!is_array($services)) {
        $services = []; // Se não for array, inicializa como array vazio
    }
@endphp

@if(count($services) > 0)
    <p class="text-white font-semibold mt-3">Serviços:</p>
    <ul class="list-disc text-gray-300 text-sm text-left pl-4">
        @foreach($services as $service)
            @php
                // Garantir que cada item dentro do array seja um array associativo válido
                if (!is_array($service)) {
                    continue; // Pula qualquer item que não seja um array
                }
            @endphp
            <li class="mt-2"><i class="fas fa-cut"></i> {{ $service['name'] ?? 'Serviço Desconhecido' }}</li>
        @endforeach
    </ul>
@else
    <p class="text-gray-400 mt-2">Nenhum serviço cadastrado</p>
@endif
                </div>
            @else
                <!-- Quando o horário está disponível -->
                <p class="text-gray-400 mt-3 text-lg font-semibold">Disponível</p>

                <!-- Botão para abrir o modal de marcação -->
                <button onclick="openModal({{ $schedule->id }})" 
                    class="w-full px-4 py-3 mt-3 text-white font-bold rounded-lg transition 
                           bg-blue-600 hover:bg-blue-700">
                    <i class="fas fa-user-plus"></i> Marcar Horário
                </button>
            @endif
        </div>
    @endforeach
</div>


<!-- Modal de Agendamento -->
<div id="modal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
    <div class="bg-gray-900 p-6 rounded-lg shadow-lg w-96 text-white">
        <h3 class="text-lg font-semibold mb-4">Marcar Horário</h3>

        <form method="POST" action="{{ route('admin.schedules.add-client') }}">
            @csrf
            <input type="hidden" name="schedule_id" id="schedule_id">

            <!-- Seleção de Cliente -->
            <label class="block text-sm font-medium">Selecionar Cliente</label>
            <select name="user_id" class="w-full bg-gray-700 border border-gray-600 text-white rounded-md px-3 py-2 mb-2">
                <option value="">Selecionar Usuário</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>

            <label class="block text-sm font-medium">Ou digite o nome</label>
            <input type="text" name="client_name" placeholder="Nome do Cliente"
                class="w-full bg-gray-700 border border-gray-600 text-white rounded-md px-3 py-2 mb-3" />

            <!-- Seleção de Serviços -->
            <label class="block text-sm font-medium">Selecione os Serviços</label>
            <div id="services-list" class="mb-4">
                @php
                    // Garante que $availableServices seja iterável
                    if (!isset($availableServices) || !is_iterable($availableServices)) {
                        $availableServices = [];
                    }
                @endphp

                @foreach($availableServices as $service)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="services[]" 
                               value="{{ json_encode(['name' => $service->name, 'price' => $service->price]) }}" 
                               class="form-checkbox text-green-500">
                        <span>{{ $service->name }} - R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                    </label>
                @endforeach
            </div>

            <div class="mt-4 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-600 rounded-lg hover:bg-gray-700">Cancelar</button>
                <button type="submit" class="px-4 py-2 bg-green-600 rounded-lg hover:bg-green-700">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(scheduleId) {
        document.getElementById('schedule_id').value = scheduleId;
        document.getElementById('modal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
    }
</script>



                <!-- Mensagem caso não tenha horários -->
                @if($schedules->isEmpty())
                    <p class="text-gray-400 text-center mt-6">Nenhum horário encontrado para esta data.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
