<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-calendar-check"></i> Criar Horários Fixos
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-user-clock"></i> Definir Horários Fixos</h3>

                <form method="POST" action="{{ route('schedules.fixed.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-300"><i class="fas fa-user"></i> Cliente</label>
                        <select name="client_id" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300"><i class="fas fa-user-tie"></i> Barbeiro</label>
                        <select name="barber_id" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                            @foreach($barbers as $barber)
                                <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300"><i class="far fa-calendar-alt"></i> Dia da Semana</label>
                        <select name="weekday" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                            <option value="0">Domingo</option>
                            <option value="1">Segunda-feira</option>
                            <option value="2">Terça-feira</option>
                            <option value="3">Quarta-feira</option>
                            <option value="4">Quinta-feira</option>
                            <option value="5">Sexta-feira</option>
                            <option value="6">Sábado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300"><i class="far fa-clock"></i> Horário</label>
                        <input type="time" name="fixed_time" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                    </div>

                    <button type="submit" class="col-span-2 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-calendar-check"></i> Criar Horário Fixo
                    </button>
                </form>

                <h4 class="text-lg font-semibold mt-6"><i class="fas fa-users"></i> Clientes com Horários Fixos</h4>

@foreach($fixedSchedules as $fixed)
    <div class="mt-2 bg-gray-800 p-3 rounded-lg flex justify-between items-center">
        <span>
            {{ $fixed->client->name }} - {{ \Carbon\Carbon::parse($fixed->start_time)->format('H:i') }} 
            ({{ ["Domingo", "Segunda-Feira", "Terça-Feira", "Quarta-Feira", "Quinta-Feira", "Sexta-Feira", "Sábado"][$fixed->weekday] }}) -

            @if(!empty($fixed->service_names))
                <span class="text-yellow-400 font-bold"> ({{ implode(', ', $fixed->service_names) }})</span>
            @else
                <span class="text-yellow-400 font-bold"> (Nenhum serviço)</span>
            @endif
        </span>

        <div class="flex gap-2">
            <!-- Botão para editar serviços -->
            <button onclick="openServiceModal({{ $fixed->id }}, '{{ json_encode($fixed->service_names) }}')" class="bg-yellow-500 px-3 py-1 rounded hover:bg-yellow-600">
                <i class="fas fa-edit"></i> Serviços
            </button>

            <!-- Botão para excluir -->
            <form method="POST" action="{{ route('schedules.fixed.delete') }}">
                @csrf
                <input type="hidden" name="schedule_id" value="{{ $fixed->id }}">
                <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
                    <i class="fas fa-trash"></i> Remover
                </button>
            </form>
        </div>
    </div>
@endforeach


            </div>
        </div>
    </div>

<!-- Modal para editar serviços -->
<div id="serviceModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex justify-center items-center hidden">
    <div class="bg-gray-800 p-6 rounded-lg w-96">
        <h3 class="text-lg text-white font-semibold mb-3">Editar Serviços</h3>

        <div id="serviceList" class="max-h-60 overflow-y-auto">
            <!-- Checkboxes de serviços serão preenchidos aqui -->
        </div>

        <div class="mt-4 flex justify-between">
            <button onclick="saveServices()" class="bg-green-500 px-4 py-2 rounded-lg text-white">Salvar</button>
            <button onclick="closeServiceModal()" class="bg-red-500 px-4 py-2 rounded-lg text-white">Fechar</button>
        </div>
    </div>
</div>

<script>
    let selectedScheduleId = null;
    let servicesAvailable = @json($services); // Pegando a lista de serviços do Laravel

    function openServiceModal(scheduleId, servicesJson) {
        selectedScheduleId = scheduleId;

        // Decodifica os serviços do cliente fixo
        let selectedServices = servicesJson ? JSON.parse(servicesJson) : [];

        let serviceListDiv = document.getElementById('serviceList');
        serviceListDiv.innerHTML = '';

        // Gerando a lista de checkboxes dos serviços
        servicesAvailable.forEach(service => {
            let isChecked = selectedServices.includes(service.id);
            let checkbox = `
                <label class="flex items-center space-x-2 text-white">
                    <input type="checkbox" class="service-checkbox" value="${service.id}" ${isChecked ? 'checked' : ''}>
                    <span>${service.name}</span>
                </label>
            `;
            serviceListDiv.innerHTML += checkbox;
        });

        document.getElementById('serviceModal').classList.remove('hidden');
    }

    function closeServiceModal() {
        document.getElementById('serviceModal').classList.add('hidden');
    }

    function saveServices() {
        let selectedServices = [];
        document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
            selectedServices.push(parseInt(checkbox.value));
        });

        fetch("{{ route('schedules.fixed.update') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                schedule_id: selectedScheduleId,
                services: selectedServices
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Serviços atualizados com sucesso!");
                location.reload();
            } else {
                alert("Erro ao atualizar serviços!");
            }
        })
        .catch(error => console.error("Erro na requisição:", error));

        closeServiceModal();
    }
</script>

</x-app-layout>
