<x-layouts.payment>
    <div class="flex items-center justify-center min-h-screen bg-white">
        <div class="shadow-xl bg-white flex overflow-hidden">
<!-- Resumo da Reserva -->
<div class="w-screen h-screen p-20 bg-gray-800 text-white relative">
    <!-- Botão de Voltar -->
    <button onclick="window.history.back()" 
        class="absolute top-12 left-12 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-xl transition duration-300 flex items-center">
        <i class="fas fa-arrow-left mr-2"></i> Voltar
    </button>

    <h5 class="font-semibold mt-12 mb-8 text-xl">Resumo da Reserva</h5>

    <div class="mb-8">
        <strong class="mb-5 text-lg">Total a pagar:</strong><br>
        <h1 class="font-bold mt-4">
            <span class="text-4xl">R$</span>
            <span class="text-6xl" id="total-price">{{ number_format($totalPrice, 2, ',', '.') }}</span>
        </h1>
    </div>

    <div class="mb-6 space-y-3">
        <p><i class="fas fa-calendar-day mr-2"></i><strong>Data:</strong> {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</p>
        <p><i class="fas fa-clock mr-2"></i><strong>Horário:</strong> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
        <p><i class="fas fa-user mr-2"></i><strong>Barbeiro:</strong> {{ $barber->name }}</p>
    </div>

    <hr class="border-gray-600">

    <div class="mt-4">
        <strong class="text-lg">Serviços Selecionados:</strong>
        <div class="mt-5 space-y-2">
            @foreach($selectedServices as $service)
                <div class="flex justify-between text-lg">
                    <span>{{ $service->name }} ({{ $service->duration }} min)</span>
                    <span class="font-semibold">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    </div>

</div>


            <!-- Opções de Pagamento -->
            <div class="w-4/6 pr-20 pl-20 bg-gray-100 rounded-lg">
                <h3 class="text-2xl mt-8 font-semibold text-gray-900 mb-6 flex items-center">
                    Escolha a Forma de Pagamento
                </h3>

    <div class="mb-6">
        <label class="block text-gray-700 font-medium mb-2">
            <i class="fas fa-list mr-2 text-gray-500"></i> Escolha a forma de pagamento:
        </label>

        <div class="flex space-x-4">
            <!-- Opção: Pix -->
            <label for="pix" class="w-1/2 p-4 border-2 border-gray-300 rounded-lg flex items-center justify-center cursor-pointer transition duration-300 
            hover:border-green-500 hover:bg-green-50 peer-checked:border-green-600 peer-checked:bg-green-100"
            id="label-pix">
                <input type="radio" name="payment_method" value="pix" class="hidden peer" id="pix">
                <i class="fa-brands fa-pix text-xl text-green-500"></i>
                <span class="ml-2 font-medium text-gray-700">Pix</span>
            </label>

            <!-- Opção: Cartão de Crédito -->
            <label for="credit_card" class="w-1/2 p-4 border-2 border-gray-300 rounded-lg flex items-center justify-center cursor-pointer transition duration-300 
            hover:border-blue-500 hover:bg-blue-50 peer-checked:border-blue-600 peer-checked:bg-blue-100"
            id="label-credit_card">
                <input type="radio" name="payment_method" value="credit_card" class="hidden peer" id="credit_card">
                <i class="fas fa-credit-card text-xl text-blue-500"></i>
                <span class="ml-2 font-medium text-gray-700">Cartão de Crédito</span>
            </label>
        </div>
    </div>

    <!-- Campos específicos do Cartão de Crédito -->
    <div id="credit-card-fields" class="hidden">
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Nome no Cartão:</label>
            <input type="text" id="card-holder-name" class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500 uppercase" placeholder="Nome completo">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">CPF:</label>
            <input type="text" id="cpf" class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="000.000.000-00">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-2">Número do Cartão:</label>
            <input type="text" id="card-number" class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="0000 0000 0000 0000">
        </div>

        <div class="flex space-x-4 mb-4">
            <div class="w-1/2">
                <label class="block text-gray-700 font-medium mb-2">Data de Vencimento:</label>
                <input type="text" id="expiry-date" class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="MM/AA">
            </div>
            <div class="w-1/2">
                <label class="block text-gray-700 font-medium mb-2">CVC:</label>
                <input type="text" id="cvc" class="w-full rounded-lg border-gray-300 shadow-sm p-3 focus:ring-blue-500 focus:border-blue-500" placeholder="000">
            </div>
        </div>
    </div>

    <!-- Botões de Pagamento -->
    <div class="flex justify-between gap-4 mt-6">
        <button type="button" id="pix-btn" class="px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition w-full hidden">
            <i class="fa-brands fa-pix mr-2"></i> Pagar com Pix
        </button>


                    
        <button type="button" id="credit-card-btn" class="px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-full hidden">
            <i class="fas fa-credit-card mr-2"></i> Pagar com Cartão
        </button>
    </div>



  <!-- Loading -->
  <div id="loading" class="hidden flex justify-center items-center mt-4">
                        <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-blue-500"></div>
                        <p class="ml-3 text-gray-700">Gerando Pix...</p>
                    </div>

<!-- Área do QR Code PIX -->
<div id="pix-payment-area" class="hidden mt-6 p-8 bg-white shadow-lg rounded-xl text-center border border-gray-300">
    
    <!-- Título -->
    <div class="flex flex-col items-center">
        <h2 class="text-2xl font-bold text-gray-900">Pague com Pix</h2>
        <p class="text-gray-500 text-sm">Escaneie o QR Code abaixo para realizar o pagamento</p>
    </div>

    <!-- QR Code -->
    <div class="flex justify-center mt-5">
        <div id="pix-qrcode" class="bg-gray-100 p-4 rounded-lg shadow-md"></div>
    </div>

    <!-- Código Pix Copia e Cola -->
    <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
        <p class="text-gray-600 font-medium">Ou copie e cole o código abaixo:</p>
        <div class="relative mt-2">
            <textarea id="pix-code" class="w-full p-3 border rounded-md bg-gray-100 text-gray-800 text-center" readonly></textarea>
            <button id="copy-pix" class="absolute top-2 right-2 px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                <i class="fas fa-copy"></i>
            </button>
        </div>
    </div>

    <!-- Armazena o TXID do PIX -->
<input type="hidden" id="pix-txid">

    <!-- Tempo Restante -->
    <p id="pix-expiration" class="text-red-500 font-semibold mt-4"></p>

    <!-- Botões de Ação -->
    <div class="flex justify-center gap-4 mt-5">
        <button id="copy-pix-btn" class="px-5 py-3 flex items-center bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-copy mr-2"></i> Copiar Código Pix
        </button>
        <button type="button" id="confirm-payment" class="px-5 py-3 flex items-center bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
    <i class="fas fa-check-circle mr-2"></i> Confirmar Pagamento
</button>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Modal de Sucesso -->
<div id="success-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <h2 class="text-2xl font-semibold text-green-600">✅ Pagamento Confirmado!</h2>
        <p class="text-gray-700 mt-2">Você será redirecionado em <span id="redirect-timer" class="font-bold">5</span>s...</p>
        <button id="close-success" class="mt-4 px-5 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
            Fechar Agora
        </button>
    </div>
</div>

<!-- Modal de Erro -->
<div id="error-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-xl p-6 text-center">
        <h2 class="text-2xl font-semibold text-red-600">⚠️ Pagamento Não Realizado</h2>
        <p class="text-gray-700 mt-2">Tente novamente em alguns instantes.</p>
        <button id="close-error" class="mt-4 px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
            Fechar
        </button>
    </div>
</div>

<!-- Input Oculto para armazenar o ID do Usuário -->
<input type="hidden" id="user-id" value="{{ auth()->user()->id }}">

<!-- Input Oculto para armazenar os serviços selecionados -->
<input type="hidden" id="selected-services" value='@json($selectedServices)'>

<!-- Input Oculto para armazenar o Schedule ID -->
<input type="hidden" id="schedule-id" value="{{ $schedule->id }}">


<!-- Script de Verificação de Pagamento -->
<script>
$(document).on('click', '#confirm-payment', function (e) {
    e.preventDefault();
    
    let txid = $('#pix-txid').val().trim(); // Obtém o TXID correto
    let scheduleId = $('#schedule-id').val(); // Obtém o horário selecionado
    let userId = $('#user-id').val(); // Obtém o ID do usuário logado
    let services = JSON.parse($('#selected-services').val()); // Obtém os serviços selecionados

    console.log("🔹 TXID capturado para verificação:", txid);
    console.log("🔹 Schedule ID:", scheduleId);
    console.log("🔹 User ID:", userId);
    console.log("🔹 Serviços Selecionados:", services);

    if (!txid || !scheduleId || !userId) {
        $('#error-modal').removeClass('hidden'); // Exibe modal de erro
        return;
    }

    $.ajax({
        url: "/verificar-pagamento",
        type: "GET",
        data: {
            txid: txid,
            schedule_id: scheduleId,
            user_id: userId,
            services: services
        },
        beforeSend: function () {
            console.log("🔹 Enviando requisição para verificar pagamento...");
            $('#confirm-payment').html('<i class="fas fa-spinner fa-spin mr-2"></i> Verificando...').attr('disabled', true);
        },
        success: function (response) {
            console.log("✅ Resposta da API:", response);
            if (response.success) {
                $('#success-modal').removeClass('hidden'); // Exibe modal de sucesso
                
                let countdown = 5;
                let timer = setInterval(function () {
                    countdown--;
                    $('#redirect-timer').text(countdown);
                    if (countdown <= 0) {
                        clearInterval(timer);
                        window.location.href = "https://agendamentos.test/agendar";
                    }
                }, 1000);
            } else {
                $('#error-modal').removeClass('hidden'); // Exibe modal de erro
            }
        },
        error: function (xhr, status, error) {
            console.error("❌ Erro ao verificar pagamento:", error);
            $('#error-modal').removeClass('hidden'); // Exibe modal de erro
        },
        complete: function () {
            console.log("🔹 Requisição concluída.");
            $('#confirm-payment').html('<i class="fas fa-check-circle mr-2"></i> Confirmar Pagamento').attr('disabled', false);
        }
    });
});


// Fechar modais manualmente
$('#close-success').on('click', function () {
    $('#success-modal').addClass('hidden');
    window.location.href = "https://agendamentos.test/agendar";
});
$('#close-error').on('click', function () {
    $('#error-modal').addClass('hidden');
});
</script>



    </div>
</div>




                

            </div>

            
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Seletores principais
        const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
        const creditCardFields = document.getElementById('credit-card-fields');
        const creditCardBtn = document.getElementById('credit-card-btn');
        const pixBtn = document.getElementById('pix-btn');
        const pixPaymentArea = document.getElementById('pix-payment-area');

        // Recuperar seleção do usuário do localStorage
        const savedPaymentMethod = localStorage.getItem('selectedPaymentMethod');

        if (savedPaymentMethod) {
            document.getElementById(savedPaymentMethod).checked = true;
            applySelectionEffect(savedPaymentMethod);
            togglePaymentFields(savedPaymentMethod);
        }

        // Monitorar mudanças de seleção
        paymentRadios.forEach(radio => {
            radio.addEventListener("change", function () {
                localStorage.setItem('selectedPaymentMethod', this.id); // Salvar escolha no localStorage
                applySelectionEffect(this.id);
                togglePaymentFields(this.value);
            });
        });

        // Aplicar efeito visual à opção selecionada
        function applySelectionEffect(selectedId) {
            document.getElementById('label-credit_card').classList.remove('border-blue-600', 'bg-blue-100');
            document.getElementById('label-pix').classList.remove('border-green-600', 'bg-green-100');

            if (selectedId === 'credit_card') {
                document.getElementById('label-credit_card').classList.add('border-blue-600', 'bg-blue-100');
            } else if (selectedId === 'pix') {
                document.getElementById('label-pix').classList.add('border-green-600', 'bg-green-100');
            }
        }

        // Alternar exibição de campos com base no método de pagamento selecionado
        function togglePaymentFields(value) {
            if (value === 'credit_card') {
                creditCardFields.classList.remove('hidden');
                creditCardBtn.classList.remove('hidden');
                pixBtn.classList.add('hidden');
                pixPaymentArea.classList.add('hidden'); // Esconder área do Pix
            } else if (value === 'pix') {
                creditCardFields.classList.add('hidden');
                creditCardBtn.classList.add('hidden');
                pixBtn.classList.remove('hidden');
            } else {
                creditCardFields.classList.add('hidden');
                creditCardBtn.classList.add('hidden');
                pixBtn.classList.add('hidden');
            }
        }

        $('#pix-btn').on('click', function () {
    let valor = {{ $totalPrice }};
    let nome = "{{ auth()->user()->name ?? 'Nome Fictício' }}";
    let cpf = "123.456.789-00";

    $('#pix-btn').attr('disabled', true);
    $('#loading').removeClass('hidden'); // Exibir loading

    $.ajax({
        url: "/gerar-pix",
        type: "POST",
        data: {
            valor: valor,
            nome: nome,
            cpf: cpf,
            _token: "{{ csrf_token() }}"
        },
        success: function (response) {
            if (response.success) {
                console.log("✅ PIX criado com sucesso:", response);

                // Exibir a área do pagamento
                $('#pix-payment-area').removeClass('hidden');
                
                // Gerar QR Code localmente
                $('#pix-qrcode').empty();
                new QRCode(document.getElementById("pix-qrcode"), {
                    text: response.location,  // 🔥 URL encurtada do Banco do Brasil
                    width: 200,
                    height: 200
                });

                // Inserir Pix Copia e Cola no campo
                $('#pix-code').val(response.pix_copiaecola);

                // 🔹 Armazena o TXID no input hidden para posterior verificação
                $('#pix-txid').val(response.txid);
                console.log("🔹 TXID correto armazenado:", response.txid);

                // Iniciar contagem regressiva (5 minutos)
                startCountdown(300);
            } else {
                alert('Erro ao gerar QR Code Pix.');
            }
        },
        error: function () {
            alert('Erro ao conectar com o servidor.');
        },
        complete: function () {
            $('#pix-btn').attr('disabled', false);
            $('#loading').addClass('hidden'); // Esconder loading
        }
    });
});


            $('#copy-pix').on('click', function () {
                let pixCode = document.getElementById("pix-code");
                pixCode.select();
                document.execCommand("copy");
                alert("Código Pix copiado!");
            });

            function startCountdown(seconds) {
                let timerDisplay = document.getElementById("pix-expiration");
                let countdown = setInterval(function () {
                    let minutes = Math.floor(seconds / 60);
                    let remainingSeconds = seconds % 60;
                    timerDisplay.innerText = `Tempo restante: ${minutes}m ${remainingSeconds}s`;

                    if (seconds <= 0) {
                        clearInterval(countdown);
                        timerDisplay.innerText = "Expirado! Gere um novo QR Code.";
                        $('#pix-payment-area').addClass('opacity-50');
                    }
                    seconds--;
                }, 1000);
            }
    });
</script>


                      
</x-layouts.payment>
