<x-layouts.client>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <!-- Filtro por data -->
        <form method="GET" action="{{ route('client.schedule.index') }}" class="mb-6">
            <label for="date" class="block text-gray-700 font-medium mb-2">Escolha uma data:</label>
            <div class="flex items-center gap-4">
                <input 
                    type="date" 
                    id="date" 
                    name="date" 
                    value="{{ $filterDate }}" 
                    class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <button 
                    type="submit" 
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                    Filtrar
                </button>
            </div>
        </form>

        <!-- Exibe mensagem caso não haja horários -->
        @if($schedules->isEmpty())
            <p class="text-gray-600">Nenhum horário disponível para a data selecionada.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($schedules as $schedule)
                    <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                        <div class="font-semibold text-xl text-gray-800">
                            {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <p><strong>Início:</strong> {{ $schedule->start_time }}</p>
                            <p><strong>Fim:</strong> {{ $schedule->end_time }}</p>
                        </div>
                        @if(auth()->check())
                            <!-- Botão para abrir o modal -->
                            <button 
                                onclick="openModal({{ $schedule->id }}, '{{ $schedule->start_time }}', '{{ $schedule->end_time }}')"
                                class="mt-4 px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600">
                                Reservar
                            </button>
                        @else
                            <!-- Botão para abrir o modal de login -->
                            <button 
                                onclick="openLoginModal()"
                                class="mt-4 px-4 py-2 bg-green-500 text-white font-medium rounded-lg hover:bg-green-600">
                                Reservar
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal de Reservar -->
<!-- Modal de Reservar -->
<div id="reservation-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
    <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
        <h2 class="text-xl font-semibold mb-4">Reservar Horário</h2>
        <form method="POST" action="{{ route('client.payment.create') }}">
            @csrf
            <input type="hidden" name="schedule_id" id="modal-schedule-id">
            <input type="hidden" name="start_time" id="modal-start-time">
            <input type="hidden" name="end_time" id="modal-end-time">

            <!-- Selecionar Barbeiro -->
            <div class="mb-4">
                <label for="barber" class="block text-gray-700">Escolha o barbeiro:</label>
                <select name="barber_id" id="barber" class="w-full rounded-md border-gray-300 shadow-sm">
                    @foreach($barbers as $barber)
                        <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Selecionar Serviços -->
            <div class="mb-4">
                <label class="block text-gray-700">Escolha os serviços:</label>
                <div id="services-container">
    @foreach($services as $service)
        <div class="flex items-center gap-2 mb-2">
            <input 
                type="checkbox" 
                name="services[]" 
                id="service-{{ $service->id }}" 
                value="{{ $service->id }}" 
                data-duration="{{ $service->duration }}" 
                data-price="{{ $service->price }}" 
                class="service-checkbox">
            <label for="service-{{ $service->id }}">
                {{ $service->name }} ({{ $service->duration }} min) - R$ {{ number_format($service->price, 2, ',', '.') }}
            </label>
        </div>
    @endforeach
</div>
<p class="mt-2 text-sm text-gray-700">
    <strong>Total do Serviço:</strong> R$ <span id="total-price">0,00</span>
</p>
<p id="time-warning" class="text-red-500 hidden mt-2">O tempo total dos serviços selecionados ultrapassa o limite do horário disponível.</p>

            </div>

            <div class="flex justify-end gap-4">
                <button type="button" onclick="closeModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" 
    id="confirm-button" 
    class="px-4 py-2 bg-blue-500 text-white rounded-lg">
    Confirmar e Gerar Pix
</button>


            </div>
        </form>
    </div>
</div>


    <!-- Modal de Login -->
    <div id="login-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-semibold mb-4">Faça login para continuar</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-4">
                    <label for="email" class="block text-gray-700">Email:</label>
                    <input type="email" name="email" id="email" class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="mb-4">
                    <label for="password" class="block text-gray-700">Senha:</label>
                    <input type="password" name="password" id="password" class="w-full rounded-md border-gray-300 shadow-sm">
                </div>
                <div class="flex justify-end gap-4">
                    <button type="button" onclick="closeLoginModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg">
                        Cancelar
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">
                        Entrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
   document.addEventListener('DOMContentLoaded', () => {
    const serviceCheckboxes = document.querySelectorAll('.service-checkbox');
    const totalPriceElement = document.getElementById('total-price');
    const warningMessage = document.getElementById('time-warning');
    const confirmButton = document.getElementById('confirm-button');

    function calculateTotals() {
        let totalDuration = 0;
        let totalPrice = 0;
        let hasCheckedServices = false;

        serviceCheckboxes.forEach(checkbox => {
            if (checkbox.checked) {
                hasCheckedServices = true;
                totalDuration += parseInt(checkbox.getAttribute('data-duration'));
                totalPrice += parseFloat(checkbox.getAttribute('data-price'));
            }
        });

        const startTime = document.getElementById('modal-start-time').value;
        const endTime = document.getElementById('modal-end-time').value;

        const availableTime = calculateTimeDifference(startTime, endTime);

        // Atualiza o preço total no HTML
        totalPriceElement.textContent = totalPrice.toFixed(2).replace('.', ',');

        // Verifica se o tempo total excede o limite
        if (totalDuration > availableTime || !hasCheckedServices) {
            warningMessage.classList.remove('hidden');
            confirmButton.disabled = true;
            confirmButton.classList.remove('bg-green-500', 'hover:bg-green-600');
            confirmButton.classList.add('bg-gray-400', 'cursor-not-allowed');
        } else {
            warningMessage.classList.add('hidden');
            confirmButton.disabled = false;
            confirmButton.classList.add('bg-green-500', 'hover:bg-green-600');
            confirmButton.classList.remove('bg-gray-400', 'cursor-not-allowed');
        }
    }

    function calculateTimeDifference(startTime, endTime) {
        const [startHour, startMinute] = startTime.split(':').map(Number);
        const [endHour, endMinute] = endTime.split(':').map(Number);

        const start = new Date();
        start.setHours(startHour, startMinute);

        const end = new Date();
        end.setHours(endHour, endMinute);

        return (end - start) / 60000; // Diferença em minutos
    }

    serviceCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', calculateTotals);
    });
});

</script>




    <script>
        function openModal(scheduleId, startTime, endTime) {
            document.getElementById('modal-schedule-id').value = scheduleId;
            document.getElementById('modal-start-time').value = startTime;
            document.getElementById('modal-end-time').value = endTime;
            document.getElementById('reservation-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('reservation-modal').classList.add('hidden');
        }

        function openLoginModal() {
            document.getElementById('login-modal').classList.remove('hidden');
        }

        function closeLoginModal() {
            document.getElementById('login-modal').classList.add('hidden');
        }
    </script>
</x-layouts.client>
