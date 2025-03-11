<x-layouts.client>

    <div class="flex flex-col items-center justify-center min-h-screen bg-cover bg-center px-6 sm:px-12" style="background-image: url('{{ asset('storage/img/bg.png') }}');">
        
        <!-- Logo -->
        <img src="{{ asset('storage/img/gs2.png') }}" alt="GS Barbearia" class="w-64 sm:w-80 md:w-96 mb-8 drop-shadow-lg">

        <!-- Título -->
        <h1 class="text-white text-center text-4xl sm:text-5xl font-bold leading-tight drop-shadow-md">
            GS Barbearia: Estilo e Tradição para o Homem Moderno
        </h1>

        <!-- Descrição -->
        <p class="text-white text-lg sm:text-xl max-w-4xl text-center mt-6 leading-relaxed drop-shadow-md">
            Na GS Barbearia, cada corte é uma obra de arte, onde tradição e inovação se unem para um estilo impecável. 
            Nossos barbeiros são especialistas em transformar seu visual. Venha viver essa experiência única! ✂️🔥
        </p>

<!-- Seção de Serviços Disponíveis -->
<div class="mt-12 bg-white p-8 rounded-lg shadow-lg w-full max-w-8xl">
    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">💈 Serviços Disponíveis</h2>
    
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        @foreach($services as $service)
            <div class="bg-gray-100 p-5 rounded-lg shadow-md text-center flex flex-col items-center">
                <!-- Exibir a imagem caso exista -->
                @if($service->photo)
                    <img src="{{ asset('storage/' . $service->photo) }}" 
                        alt="{{ $service->name }}" 
                        class="w-24 h-24 object-cover rounded-full border-4 border-gray-300 shadow-lg mx-auto">
                @endif
                
                <h3 class="text-xl font-semibold text-gray-800 mt-3">{{ $service->name }}</h3>
                <p class="text-gray-600 mt-2">🕒 {{ $service->duration }} min</p>
                <p class="text-green-600 font-bold text-lg mt-2"> R$ {{ number_format($service->price, 2, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
</div>



<div class="mt-12 bg-white p-8 rounded-lg shadow-lg w-full max-w-8xl">

    <!-- Filtros -->
    <div class="flex flex-col md:flex-row gap-4 mb-6 items-stretch md:items-end">

        <!-- Escolher Data -->
        <div class="w-full md:w-auto">
            <label for="date" class="block text-gray-700 font-semibold mb-1">Escolha uma data:</label>
            <input 
                type="date" 
                id="date" 
                name="date" 
                value="{{ now()->toDateString() }}" 
                class="w-full md:w-auto rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 p-2">
        </div>

        <!-- Escolher Barbeiro -->
        <div class="w-full md:w-auto">
            <label for="barber" class="block text-gray-700 font-semibold mb-1">Escolha um barbeiro:</label>
            <select id="barber" name="barber" 
                class="w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 p-2">
                @foreach($barbers as $barber)
                    <option value="{{ $barber->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $barber->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Botões de Ação -->
        <div class="flex flex-col md:flex-row gap-2 w-full md:w-auto">
            <!-- Botão de Filtrar -->
            <button 
                id="filter-btn" 
                class="w-full md:w-auto px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300">
                <i class="fas fa-search"></i> Filtrar
            </button>

            <!-- Botão de Atualizar -->
            <button 
                id="refresh-btn" 
                class="w-full md:w-auto px-4 py-2 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-800 transition duration-300">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Lista de Horários Disponíveis -->
    <div id="schedule-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        <!-- Horários serão carregados aqui via AJAX -->
    </div>
</div>


<script>
$(document).ready(function () {
    let lastLoadedData = null; // Armazena os dados carregados para evitar chamadas desnecessárias
    let loggedUserId = "{{ auth()->check() ? auth()->user()->id : null }}"; // ID do usuário logado

    function loadSchedules() {
        let selectedDate = $("#date").val();
        let selectedBarber = $("#barber").val();

        $.ajax({
            url: "{{ route('schedules.get') }}",
            type: "GET",
            data: { date: selectedDate, barber_id: selectedBarber },
            success: function (response) {
                if (JSON.stringify(response) === JSON.stringify(lastLoadedData)) {
                    console.log("🔄 Dados não mudaram, evitando recarga desnecessária.");
                    return;
                }
                
                lastLoadedData = response;
                let scheduleContainer = $("#schedule-container");
                scheduleContainer.empty();

                if (response.length === 0) {
                    scheduleContainer.html('<p class="text-gray-600 text-center text-lg">Nenhum horário disponível para a data selecionada. ⏳</p>');
                } else {
                    response.forEach(schedule => {
                        let startTime = schedule.start_time.substring(0, 5);
                        let endTime = schedule.end_time.substring(0, 5);
                        let isLocked = schedule.is_locked;
                        let isBooked = schedule.is_booked;
                        let isMySchedule = (schedule.client_id == loggedUserId);

                        let buttonHtml = "";

                        if (isMySchedule) {
                            buttonHtml = `<button class="mt-4 w-full px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg cursor-not-allowed">
                                Meu Horário 🏆
                            </button>`;
                        } else if (isBooked) {
                            buttonHtml = `<button class="mt-4 w-full px-4 py-2 bg-blue-500 text-white font-semibold rounded-lg cursor-not-allowed">
                                Horário Reservado ✅
                            </button>`;
                        } else if (isLocked) {
                            buttonHtml = `<button class="mt-4 w-full px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg cursor-not-allowed opacity-75">
                                Horário sendo reservado ⏳
                            </button>`;
                        } else if (loggedUserId) {
                            buttonHtml = `<button onclick="checkAndOpenModal(${schedule.id}, '${startTime}', '${endTime}')" 
                                class="mt-4 w-full px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300">
                                Reservar ✂️
                            </button>`;
                        } else {
                            buttonHtml = `<button onclick="window.location.href='{{ route('login') }}'"
                                class="mt-4 w-full px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition duration-300">
                                Faça login para Reservar 
                            </button>`;
                        }

                        let scheduleCard = `
                            <div class="bg-white p-5 rounded-lg shadow-md text-center border border-gray-200">
                                <h3 class="text-xl font-bold text-gray-900">
                                    ${new Date(schedule.date).toLocaleDateString('pt-BR')}
                                </h3>
                                <p class="text-gray-700 mt-2"><strong>Início:</strong> ${startTime}</p>
                                <p class="text-gray-700"><strong>Fim:</strong> ${endTime}</p>
                                ${buttonHtml}
                            </div>
                        `;

                        scheduleContainer.append(scheduleCard);
                    });
                }
            },
            error: function () {
                console.error("❌ Erro ao carregar horários.");
            }
        });
    }

    // 🔄 Atualiza os horários apenas quando houver mudança
    $("#filter-btn").on("click", function () {
        loadSchedules();
    });

    // 🔄 Atualiza os horários ao mudar data ou barbeiro
    $("#date, #barber").on("change", function () {
        loadSchedules();
    });

    // 🔄 Botão de atualizar horários manualmente
    $("#refresh-btn").on("click", function () {
        loadSchedules();
    });

    // 🚀 Carrega os horários iniciais
    loadSchedules();
});
</script>




    </div>
<!-- Modal de Reservar -->
<div id="reservation-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg md:w-1/3 md:max-w-2xl overflow-y-auto max-h-[90vh]">
        <h2 class="text-xl font-semibold mb-4 text-center md:text-left">Reservar Horário</h2>
        <form method="POST" action="{{ route('client.payment.showPaymentPage') }}">
            @csrf
            <input type="hidden" name="schedule_id" id="modal-schedule-id">
            <input type="hidden" name="start_time" id="modal-start-time">
            <input type="hidden" name="end_time" id="modal-end-time">

            <!-- Selecionar Barbeiro -->
            <div class="mb-4">
                <label for="barber" class="block text-gray-700">Escolha o barbeiro:</label>
                <select name="barber_id" id="barber" class="w-full rounded-md border-gray-300 shadow-sm p-2">
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
                            <label for="service-{{ $service->id }}" class="flex-1">
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

            <!-- Botões -->
            <div class="flex flex-col md:flex-row justify-end gap-2">
                <button type="button" onclick="closeModal()" class="w-full md:w-auto px-4 py-2 bg-gray-300 text-gray-800 rounded-lg">
                    Cancelar
                </button>
                <button type="submit" id="confirm-button" class="w-full md:w-auto px-4 py-2 bg-green-700 text-white rounded-lg">
                    Confirmar
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

        function checkAndOpenModal(scheduleId, startTime, endTime) {
    // Cria o overlay de carregamento
    let loadingOverlay = document.createElement("div");
    loadingOverlay.id = "loading-overlay";
    loadingOverlay.className = "fixed inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-75 text-white z-50";
    
    loadingOverlay.innerHTML = `
        <div class="flex flex-col items-center">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white border-opacity-75"></div>
            <p class="mt-4 text-xl font-semibold">Verificando disponibilidade...</p>
        </div>
    `;
    
    document.body.appendChild(loadingOverlay);

    // Garante que o loading fique na tela por pelo menos 5 segundos
    let minLoadingTime = new Promise(resolve => setTimeout(resolve, 5000));

    let checkAvailability = $.ajax({
        url: "{{ route('schedule.check') }}",
        type: "POST",
        data: {
            schedule_id: scheduleId,
            _token: "{{ csrf_token() }}"
        }
    });

    // Espera pelo menos 5 segundos antes de remover o loading
    Promise.all([minLoadingTime, checkAvailability]).then(([_, response]) => {
        document.getElementById("loading-overlay").remove(); // Remove o loading

        if (response.status === "available") {
            openModal(scheduleId, startTime, endTime);
        } else {
            alert(response.message);
        }
    }).catch(() => {
        document.getElementById("loading-overlay").remove();
        alert("Erro ao verificar a disponibilidade. Tente novamente.");
    });
}


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
