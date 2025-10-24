<x-layouts.client>

  <div class="relative flex flex-col items-center justify-center bg-cover bg-center px-6 sm:px-6"
    style="background-image: url('{{ asset('storage/img/bg.png') }}');">

    <!-- Logo -->
    <img src="{{ asset('storage/img/gs2.png') }}" alt="GS Barbearia"
      class="w-64 sm:w-80 md:w-96 mb-6 drop-shadow-lg animate-fade-in">

    <!-- Título -->
    <h1 class="text-white text-center text-4xl sm:text-5xl font-bold leading-tight drop-shadow-md animate-fade-in">
      Estilo e Tradição para o Homem Moderno
    </h1>

    <!-- Descrição -->
    <p class="text-white text-lg sm:text-xl max-w-4xl text-center mt-4 leading-relaxed drop-shadow-md animate-fade-in">
      Na GS Barbearia, cada corte é uma obra de arte. Nossos profissionais são especialistas em transformar o seu
      visual! ✂️🔥
    </p>

    <!-- Botões de Ação -->
    <div class="mt-6 flex flex-col sm:flex-row gap-4 animate-slide-up">
      <a href="#services"
        class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition duration-300 shadow-md">
        <i class="fas fa-info-circle"></i> Conheça a GS Barbearia
      </a>
      <a href="#schedule"
        class="px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300 shadow-md">
        <i class="fas fa-calendar-check"></i> Agendar Agora
      </a>
    </div>

  </div>

  <!-- Seção de Serviços -->
  <section id="services" class="mt-6 bg-white p-8 rounded-lg shadow-lg w-full mx-auto">
    <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">💈 Nossos Serviços</h2>

    <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-3 gap-6">
      @foreach($services as $service)
        <div class="bg-gray-100 p-5 rounded-lg shadow-md text-center flex flex-col items-center">
          @if($service->photo)
            <img src="{{ asset('storage/' . $service->photo) }}" alt="{{ $service->name }}"
              class="w-24 h-24 object-cover rounded-full border-4 border-gray-300 shadow-lg mx-auto">
          @endif

          <h3 class="text-xl font-semibold text-gray-800 mt-3">{{ $service->name }}</h3>
          <p class="text-gray-600 mt-2">🕒 {{ $service->duration }} min</p>
          <p class="text-green-600 font-bold text-lg mt-2"> R$ {{ number_format($service->price, 2, ',', '.') }}</p>
        </div>
      @endforeach
    </div>
  </section>

  <!-- Seção de Agendamento -->
  <section id="schedule" class="mt-16 bg-white p-8 rounded-2xl shadow-xl w-full mx-auto ">
    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">📅 Agende seu Horário</h2>

    <!-- Filtros -->
    <div class="flex flex-col md:flex-row gap-4 mb-8 items-stretch md:items-end">

      <!-- Escolher Data -->
      <div class="w-full md:w-auto flex-1">
        <label for="date" class="block text-gray-700 font-semibold mb-1">Escolha uma data:</label>
        <input type="date" id="date" name="date" value="{{ now()->toDateString() }}"
          class="w-full h-[52px] rounded-lg border-gray-300 shadow-sm focus:ring-green-500 focus:border-green-500 px-3 text-gray-800 font-medium">
      </div>

      <!-- Escolher Barbeiro -->
      <div class="w-full md:w-auto flex-1 relative"
        x-data="{ open: false, selected: {{ $barbers->first()->id ?? 'null' }} }">
        <label for="barber" class="block text-gray-700 font-semibold mb-1">Escolha um barbeiro:</label>

        <!-- Campo visual -->
        <div @click="open = !open"
          class="flex items-center justify-between bg-gray-50 border border-gray-300 rounded-lg px-3 cursor-pointer hover:border-green-500 transition relative h-[52px]">
          @php $first = $barbers->first(); @endphp
          <div class="flex items-center gap-3">
            <img src="{{ $first->image ?? '/img/default-avatar.jpg' }}" alt="{{ $first->name ?? 'Barbeiro' }}"
              class="w-9 h-9 rounded-full object-cover border border-gray-300 shadow-sm"
              x-bind:src="$refs['barber_' + selected]?.dataset.img ?? '{{ $first->image ?? '/img/default-avatar.jpg' }}'">
            <span class="text-gray-800 font-semibold text-base"
              x-text="$refs['barber_' + selected]?.dataset.name ?? '{{ $first->name ?? 'Selecione um barbeiro' }}'">
            </span>
          </div>
          <i class="fas fa-chevron-down text-gray-500 text-sm"></i>
        </div>

        <!-- Dropdown -->
        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-150"
          x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          class="absolute left-0 top-full mt-1 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden">
          @foreach($barbers as $barber)
            <button type="button" x-ref="barber_{{ $barber->id }}"
              data-img="{{ $barber->image ?? '/img/default-avatar.jpg' }}" data-name="{{ $barber->name }}"
              @click="selected = {{ $barber->id }}; open = false;"
              class="flex items-center gap-3 w-full text-left px-4 py-3 hover:bg-green-50 transition">
              <img src="{{ $barber->image ?? '/img/default-avatar.jpg' }}" alt="{{ $barber->name }}"
                class="w-9 h-9 rounded-full object-cover border border-gray-200">
              <span class="text-gray-800 font-medium">{{ $barber->name }}</span>
            </button>
          @endforeach
        </div>

        <input type="hidden" name="barber" :value="selected">
      </div>


      <!-- Botões de Ação -->
      <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
        <button id="filter-btn"
          class="w-full md:w-auto h-[52px] px-5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
          <i class="fas fa-search"></i>
          <span>Filtrar</span>
        </button>

        <button id="refresh-btn"
          class="w-full md:w-auto h-[52px] px-5 bg-gray-700 text-white font-semibold rounded-lg hover:bg-gray-800 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
          <i class="fas fa-sync-alt"></i>
          <span>Atualizar</span>
        </button>
      </div>
    </div>

    <!-- Lista de Horários Disponíveis -->
    <div id="schedule-container" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <!-- Horários serão carregados aqui via AJAX -->
    </div>
  </section>

  <!-- Botão Flutuante "Agendar Agora" -->
  <a href="#schedule"
    class="fixed bottom-6 right-6 bg-green-600 text-white px-5 py-3 rounded-full shadow-lg text-lg font-bold hover:bg-green-700 transition duration-300 flex items-center gap-2">
    <i class="fas fa-calendar-alt"></i> Agendar Agora
  </a>




  <!-- Modal de Reservar -->
  <div id="reservation-modal"
    class="hidden fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">

      <!-- Cabeçalho -->
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between shrink-0">
        <h2 class="text-xl md:text-2xl font-bold text-gray-900">Reservar Horário</h2>
        <button type="button" onclick="closeModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
          <i class="fas fa-times text-gray-600"></i>
        </button>
      </div>

      <!-- Conteúdo -->
      <div class="flex-1 overflow-y-auto p-6">
        <form method="POST" action="{{ route('client.payment.showPaymentPage') }}" class="flex flex-col h-full">
          @csrf
          <input type="hidden" name="schedule_id" id="modal-schedule-id">
          <input type="hidden" name="start_time" id="modal-start-time">
          <input type="hidden" name="end_time" id="modal-end-time">
          <input type="hidden" name="service_id" id="selected-service-id">

          <!-- Selecionar Barbeiro -->
          <div class="mb-8">
            <label class="block text-sm font-semibold text-gray-700 mb-3">Escolha o barbeiro</label>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
              @foreach($barbers as $barber)
                <label class="cursor-pointer">
                  <input type="radio" name="barber_id" value="{{ $barber->id }}" class="sr-only peer">

                  <div
                    class="flex flex-col items-center justify-center rounded-2xl border-2 border-gray-200 hover:border-green-400 hover:shadow-md transition-all p-4 relative peer-checked:border-green-600 peer-checked:ring-4 peer-checked:ring-green-100">

                    <img src="{{ $barber->image ?? '/img/default-avatar.jpg' }}" alt="{{ $barber->name }}"
                      class="w-28 h-28 rounded-full object-cover shadow-md transition">

                    <div
                      class="absolute top-2 right-2 bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded-full hidden peer-checked:block shadow-sm">
                      ✔
                    </div>

                    <span class="mt-3 text-gray-800 font-semibold text-center peer-checked:text-green-700 transition">
                      {{ $barber->name }}
                    </span>
                  </div>
                </label>
              @endforeach
            </div>
          </div>

          <!-- Serviços -->
          <div class="mb-8">
            <div class="flex items-center justify-between mb-3">
              <label class="block text-sm font-semibold text-gray-700">Escolha um serviço</label>
              <span class="text-xs text-gray-500">1 opção</span>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              @foreach($services as $service)
                <label class="cursor-pointer">
                  <input type="radio" name="service" value="{{ $service->id }}" class="sr-only peer service-radio"
                    data-price="{{ $service->price }}" data-duration="{{ $service->duration }}">

                  <div
                    class="flex flex-col items-center text-center rounded-2xl border-2 border-gray-200 hover:border-green-400 hover:shadow-md transition-all p-4 relative peer-checked:border-green-600 peer-checked:ring-4 peer-checked:ring-green-100">

                    <img src="{{ asset('storage/' . $service->photo) }}" alt="{{ $service->name }}"
                      class="w-24 h-24 object-cover rounded-full shadow-md transition">

                    <div
                      class="absolute top-2 right-2 bg-green-600 text-white text-xs font-bold px-2 py-0.5 rounded-full hidden peer-checked:block shadow-sm">
                      ✔
                    </div>

                    <h4 class="mt-3 font-semibold text-gray-800 peer-checked:text-green-700 transition">
                      {{ $service->name }}</h4>
                    <p class="text-sm text-gray-500">{{ $service->duration }} min</p>
                    <p class="text-green-600 font-bold">R$ {{ number_format($service->price, 2, ',', '.') }}</p>
                  </div>
                </label>
              @endforeach
            </div>
          </div>


          <!-- Totais -->
          <div class="mt-auto">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
              <p class="text-sm text-gray-700">
                <strong>Serviço escolhido:</strong>
                <span id="chosen-service" class="text-gray-900">nenhum</span>
              </p>
              <div class="text-sm">
                <span class="mr-4"><strong>Duração:</strong> <span id="total-duration">0</span> min</span>
                <span><strong>Total:</strong> R$ <span id="total-price">0,00</span></span>
              </div>
            </div>

            <p id="time-warning" class="hidden text-red-500 text-sm mb-4">
              O tempo do serviço selecionado ultrapassa o limite do horário disponível.
            </p>

            <!-- Ações -->
            <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
              <button type="button" onclick="closeModal()"
                class="px-5 py-3 rounded-xl bg-gray-100 text-gray-800 hover:bg-gray-200 transition font-semibold">
                Cancelar
              </button>
              <button type="submit" id="confirm-button"
                class="px-5 py-3 rounded-xl bg-green-600 text-white hover:bg-green-700 transition font-bold disabled:opacity-50 disabled:cursor-not-allowed"
                disabled>
                Confirmar
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Script RADIO (mantém igual) -->
  <script>
    (function () {
      const radios = document.querySelectorAll('.service-radio');
      const selectedIdInput = document.getElementById('selected-service-id');
      const chosenLabel = document.getElementById('chosen-service');
      const totalPriceEl = document.getElementById('total-price');
      const totalDurationEl = document.getElementById('total-duration');
      const confirmBtn = document.getElementById('confirm-button');
      const timeWarning = document.getElementById('time-warning');

      // Limite de minutos do slot (definido quando abre o modal)
      const slotLimitMinutes = window.slotLimitMinutes ?? null;

      function formatBRL(value) {
        return (Number(value) || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }).replace('R$', '').trim();
      }

      function updateUIFromSelection(radio) {
        if (!radio) return;

        const price = parseFloat(radio.dataset.price || '0');
        const duration = parseInt(radio.dataset.duration || '0', 10);
        const label = radio.closest('label');
        const nameEl = label ? label.querySelector('h4') : null;
        const serviceName = nameEl ? nameEl.textContent.trim() : 'serviço';

        selectedIdInput.value = radio.value;
        chosenLabel.textContent = serviceName;
        totalPriceEl.textContent = formatBRL(price);
        totalDurationEl.textContent = isNaN(duration) ? '0' : duration;

        let validTime = true;
        if (slotLimitMinutes !== null) validTime = duration <= slotLimitMinutes;

        timeWarning.classList.toggle('hidden', validTime);
        confirmBtn.disabled = !validTime || !radio.checked;
      }

      // Exponho só se quiser usar fora (opcional)
      window.__updateServiceFromRadio = updateUIFromSelection;

      radios.forEach(radio => {
        radio.addEventListener('change', (e) => updateUIFromSelection(e.target));
      });

      const preSelected = Array.from(radios).find(r => r.checked);
      if (preSelected) updateUIFromSelection(preSelected);
    })();

    // Fechar modal (ok manter)
    function closeModal() {
      document.getElementById('reservation-modal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }
  </script>




  <script>
    // 🔁 RESTAURA as funções globais usadas no onclick do botão

    window.checkAndOpenModal = function (scheduleId, startTime, endTime) {
      // Overlay de loading
      let loadingOverlay = document.createElement("div");
      loadingOverlay.id = "loading-overlay";
      loadingOverlay.className = "fixed inset-0 flex flex-col items-center justify-center bg-gray-900 bg-opacity-75 text-white z-50";
      loadingOverlay.innerHTML = `
    <div class="flex flex-col items-center">
      <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white border-opacity-75"></div>
      <p class="mt-4 text-xl font-semibold">Verificando disponibilidade...</p>
    </div>`;
      document.body.appendChild(loadingOverlay);

      // tempo mínimo visual do loading (opcional)
      let minLoadingTime = new Promise(resolve => setTimeout(resolve, 5000));

      let checkAvailability = $.ajax({
        url: "{{ route('schedule.check') }}",
        type: "POST",
        data: {
          schedule_id: scheduleId,
          _token: "{{ csrf_token() }}"
        }
      });

      Promise.all([minLoadingTime, checkAvailability]).then(([_, response]) => {
        document.getElementById("loading-overlay").remove();

        if (response.status === "available") {
          window.openModal(scheduleId, startTime, endTime);
        } else {
          alert(response.message);
        }
      }).catch(() => {
        document.getElementById("loading-overlay").remove();
        alert("Erro ao verificar a disponibilidade. Tente novamente.");
      });
    };

    window.openModal = function (scheduleId, startTime, endTime) {
      // Preenche dados
      document.getElementById('modal-schedule-id').value = scheduleId;
      document.getElementById('modal-start-time').value = startTime;
      document.getElementById('modal-end-time').value = endTime;

      // Define limite de minutos para a validação do RADIO
      window.slotLimitMinutes = (function minutesBetween(s, e) {
        const [sh, sm] = s.split(':').map(Number);
        const [eh, em] = e.split(':').map(Number);
        const start = new Date(); start.setHours(sh, sm, 0, 0);
        const end = new Date(); end.setHours(eh, em, 0, 0);
        return (end - start) / 60000;
      })(startTime, endTime);

      // Limpa seleção anterior (se quiser)
      document.querySelectorAll('.service-radio').forEach(r => r.checked = false);
      const chosen = document.getElementById('chosen-service'); if (chosen) chosen.textContent = 'nenhum';
      const totalPriceEl = document.getElementById('total-price'); if (totalPriceEl) totalPriceEl.textContent = '0,00';
      const totalDurationEl = document.getElementById('total-duration'); if (totalDurationEl) totalDurationEl.textContent = '0';

      // Abre modal
      document.getElementById('reservation-modal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    };

    window.closeModal = function () {
      document.getElementById('reservation-modal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    };
  </script>

  <!-- Abra o modal definindo o limite de tempo do slot (opcional, ajuda na validação do radio) -->
  <script>
    function openModal(scheduleId, startTime, endTime) {
      document.getElementById('modal-schedule-id').value = scheduleId;
      document.getElementById('modal-start-time').value = startTime;
      document.getElementById('modal-end-time').value = endTime;

      // ✅ Define o limite de minutos pro script do RADIO validar duração
      window.slotLimitMinutes = (function minutesBetween(s, e) {
        const [sh, sm] = s.split(':').map(Number);
        const [eh, em] = e.split(':').map(Number);
        const start = new Date(); start.setHours(sh, sm, 0, 0);
        const end = new Date(); end.setHours(eh, em, 0, 0);
        return (end - start) / 60000;
      })(startTime, endTime);

      // (Opcional) Limpa seleção anterior ao abrir
      document.querySelectorAll('.service-radio').forEach(r => r.checked = false);
      const chosen = document.getElementById('chosen-service');
      if (chosen) chosen.textContent = 'nenhum';
      const totalPriceEl = document.getElementById('total-price');
      if (totalPriceEl) totalPriceEl.textContent = '0,00';
      const totalDurationEl = document.getElementById('total-duration');
      if (totalDurationEl) totalDurationEl.textContent = '0';

      document.getElementById('reservation-modal').classList.remove('hidden');
      document.body.classList.add('overflow-hidden');
    }
  </script>




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

                // Converter horários para comparar com o horário atual
                let currentTime = new Date();
                let startDateTime = new Date(`${schedule.date} ${startTime}`);
                let endDateTime = new Date(`${schedule.date} ${endTime}`);

                let buttonHtml = "";

                // Verifica se o horário já expirou
                if (currentTime > endDateTime) {
                  if (isMySchedule) {
                    buttonHtml = `<button class="mt-4 w-full px-4 py-2 bg-yellow-500 text-white font-semibold rounded-lg cursor-not-allowed">
                                    Meu Horário, Expirado ⏳
                                </button>`;
                  } else {
                    buttonHtml = `<button class="mt-4 w-full px-4 py-2 bg-gray-500 text-white font-semibold rounded-lg cursor-not-allowed opacity-75">
                                    Horário Expirado ⏰
                                </button>`;
                  }
                } else if (isMySchedule) {
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
                                    ${new Date(schedule.date + 'T00:00:00').toLocaleDateString('pt-BR')}
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

</x-layouts.client>