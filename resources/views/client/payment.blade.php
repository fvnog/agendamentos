<x-layouts.payment>
<div class="flex flex-col md:flex-row items-center justify-center min-h-screen bg-white">
        <div class="shadow-xl bg-white flex flex-col md:flex-row overflow-hidden w-full max-w-5xl">


            <!-- Resumo da Reserva -->
            <div class="w-full md:w-2/5 p-6 md:p-20 bg-gray-800 text-white relative">
                <!-- Botão de Voltar -->
                <button onclick="window.history.back()" 
                    class="absolute top-6 left-6 bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-xl transition duration-300 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                </button>

                <h5 class="font-semibold mt-12 md:mt-16 mb-8 text-xl text-center md:text-left">Resumo da Reserva</h5>

                <div class="mb-8 text-center md:text-left">
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
            <div class="w-full md:w-3/5 p-6 md:p-20 bg-gray-100 rounded-lg">
                <h3 class="text-xl md:text-2xl mt-4 md:mt-8 font-semibold text-gray-900 mb-6 text-center md:text-left">
                    Escolha a Forma de Pagamento
                </h3>

  <!-- Tabs de Seleção de Pagamento -->
  <div class="flex border-b">
                    <button id="tab-pix" class="w-1/2 py-3 text-center font-semibold text-gray-700 border-b-4 border-transparent hover:border-green-500 transition">
                        <i class="fa-brands fa-pix text-xl text-green-500"></i> Pix
                    </button>
                    <button id="tab-cartao" class="w-1/2 py-3 text-center font-semibold text-gray-700 border-b-4 border-transparent hover:border-blue-500 transition">
                        <i class="fas fa-credit-card text-xl text-blue-500"></i> Cartão de Crédito
                    </button>
                </div>

<!-- Área do Pagamento PIX -->
<div id="pix-payment-area" class="mt-6 p-8 bg-white shadow-lg rounded-xl text-center border border-gray-300">
    
    <!-- Botão Gerar QR Code -->
    <button id="pix-btn" class="mb-4 px-5 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition w-full">
        <i class="fa-brands fa-pix mr-2"></i> Gerar QR Code
    </button>

    <!-- Título -->
    <h2 class="text-2xl font-bold text-gray-900">Pague com Pix</h2>
    <p class="text-gray-500 text-sm">Escaneie o QR Code abaixo para realizar o pagamento</p>

    <!-- QR Code -->
    <div class="flex justify-center mt-5">
        <div id="pix-qrcode" class="bg-gray-100 p-4 rounded-lg shadow-md"></div>
    </div>

    <!-- Código Pix Copia e Cola -->
    <div class="mt-6 bg-gray-50 p-4 rounded-lg border border-gray-200 relative">
        <p class="text-gray-600 font-medium">Ou copie e cole o código abaixo:</p>
        <textarea id="pix-code" class="w-full p-3 border rounded-md bg-gray-100 text-gray-800 text-center" readonly></textarea>

        <!-- Botão de Copiar Código -->
        <button id="copy-pix" class="absolute top-2 right-2 px-3 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
            <i class="fas fa-copy"></i>
        </button>
    </div>

    <!-- Tempo Restante -->
    <p id="pix-expiration" class="text-red-500 font-semibold mt-4"></p>

    <hr>
            <div class="mb-4 text-center mt-4">
                    <strong class="mb-3 text-lg">Total a pagar:</strong><br>
                    <h1 class="font-bold mt-4">
                        <span class="text-2xl">R$</span>
                        <span class="text-3xl" id="total-price">{{ number_format($totalPrice, 2, ',', '.') }}</span>
                    </h1>
                </div>
                <hr>
                
                
    <!-- Botão de Confirmar Pagamento -->
    <button type="button" id="confirm-payment" class="mt-4 px-5 py-3 flex items-center bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition w-full">
        <i class="fas fa-check-circle mr-2"></i> Confirmar Pagamento
    </button>
</div>




    <!-- Área do Pagamento Cartão -->
    <div id="cartao-payment-area" class="hidden mt-6 p-8 bg-white shadow-lg rounded-xl border border-gray-300">
        <h2 class="text-2xl font-bold text-gray-900">Pagamento com Cartão</h2>
        <hr class="mt-3 mb-3">
        <p id="card-fee-warning" class="hidden text-red-500 text-sm font-semibold mt-2">
    ⚠️ O pagamento com cartão possui uma taxa adicional.
</p>

<hr class="mt-3 mb-3">

        <form id="payment-form" class="relative">
                <!-- Overlay de Loading -->
    <div id="loading-overlay" class="hidden absolute inset-0 bg-white bg-opacity-75 flex flex-col items-center justify-center rounded-lg">
        <div class="animate-spin rounded-full h-10 w-10 border-t-4 border-blue-500"></div>
        <p class="text-gray-700 mt-2">Processando pagamento...</p>
    </div>
    
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
                <div id="card-element" class="w-full p-3 border rounded-lg bg-white shadow-sm"></div>
            </div>

            <hr>
            <div class="mb-4 text-center mt-4">
                    <strong class="mb-3 text-lg">Total a pagar:</strong><br>
                    <h1 class="font-bold mt-4">
                        <span class="text-2xl">R$</span>
                        <span class="text-3xl" id="total-price">{{ number_format($totalPrice, 2, ',', '.') }}</span>
                    </h1>
                </div>
                <hr>
                
            <button type="submit" id="pay-btn" class="mt-4 px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-full">
                <i class="fas fa-credit-card mr-2"></i> Pagar Agora
            </button>
        </form>
    </div>
</div>



<!-- Script para Gerar QR Code, Copiar Código e Contador -->
<script>
document.addEventListener("DOMContentLoaded", function () {

    const tabPix = document.getElementById("tab-pix");
const tabCartao = document.getElementById("tab-cartao");
const pixArea = document.getElementById("pix-payment-area");
const cartaoArea = document.getElementById("cartao-payment-area");
const totalPriceElements = document.querySelectorAll("[id='total-price']"); // Pega todos os elementos com ID 'total-price'
const cardFeeWarning = document.getElementById("card-fee-warning");

let originalPrice = parseFloat("{{ $totalPrice }}".replace(",", ".")); // Preço original (sem taxa)

// 🔹 Função para calcular o valor a ser cobrado garantindo valor líquido
function calcularPrecoComTaxa(valorLiquido) {
    let taxaPercentual = 0.0399; // 3,99%
    let taxaFixa = 0.39; // R$ 0,39 fixo

    return ((valorLiquido + taxaFixa) / (1 - taxaPercentual));
}

// 🔹 Atualizar ambos os preços ao mesmo tempo
function atualizarPreco(valor) {
    totalPriceElements.forEach(element => {
        element.textContent = valor.toFixed(2).replace(".", ",");
    });
}

// 🔹 Alternar entre os métodos de pagamento e atualizar preço
function togglePaymentMethod(selected) {
    if (selected === "pix") {
        pixArea.classList.remove("hidden");
        cartaoArea.classList.add("hidden");
        cardFeeWarning.classList.add("hidden"); // Esconder aviso da taxa

        tabPix.classList.add("border-green-500", "bg-green-100", "border-b-4");
        tabCartao.classList.remove("border-blue-500", "bg-blue-100", "border-b-4");

        atualizarPreco(originalPrice); // Restaurar preço original

    } else {
        cartaoArea.classList.remove("hidden");
        pixArea.classList.add("hidden");
        cardFeeWarning.classList.remove("hidden"); // Exibir aviso da taxa

        tabCartao.classList.add("border-blue-500", "bg-blue-100", "border-b-4");
        tabPix.classList.remove("border-green-500", "bg-green-100", "border-b-4");

        let valorCobrado = calcularPrecoComTaxa(originalPrice);
        atualizarPreco(valorCobrado); // Atualizar os dois preços com a taxa do cartão
    }
}

// 🔹 Evento de clique para alternar
tabPix.addEventListener("click", function () {
    togglePaymentMethod("pix");
    localStorage.setItem("selectedPaymentMethod", "pix");
});

tabCartao.addEventListener("click", function () {
    togglePaymentMethod("credit_card");
    localStorage.setItem("selectedPaymentMethod", "credit_card");
});

// 🔹 Ativar o último método de pagamento selecionado (caso tenha sido salvo)
const savedPaymentMethod = localStorage.getItem("selectedPaymentMethod");
togglePaymentMethod(savedPaymentMethod === "credit_card" ? "credit_card" : "pix");


    // 🔹 Evento para Gerar QR Code PIX
    document.getElementById("pix-btn").addEventListener("click", function () {
        let valor = originalPrice;
        let nome = "{{ auth()->user()->name ?? 'Nome Fictício' }}";
        let cpf = "123.456.789-00";

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Gerando...';

        fetch("/gerar-pix", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
            body: JSON.stringify({ valor, nome, cpf })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                console.log("✅ PIX criado com sucesso:", data);

                // Exibir QR Code
                document.getElementById("pix-qrcode").innerHTML = "";
                new QRCode(document.getElementById("pix-qrcode"), { text: data.location, width: 200, height: 200 });

                // Preencher código Pix Copia e Cola
                document.getElementById("pix-code").value = data.pix_copiaecola;

                // Armazenar TXID para verificação
                document.getElementById("pix-txid").value = data.txid;

                // Iniciar Contagem Regressiva de 5 minutos
                startCountdown(300);
            } else {
                alert("Erro ao gerar QR Code Pix.");
            }
        })
        .catch(() => alert("Erro ao conectar com o servidor."))
        .finally(() => {
            this.disabled = false;
            this.innerHTML = '<i class="fa-brands fa-pix mr-2"></i> Gerar QR Code';
        });
    });

    // 🔹 Evento para Copiar Código PIX
    document.getElementById("copy-pix").addEventListener("click", function () {
        let pixCode = document.getElementById("pix-code");
        pixCode.select();
        document.execCommand("copy");
        alert("✅ Código Pix copiado!");
    });

    // 🔹 Função para Contagem Regressiva
    function startCountdown(seconds) {
        let timerDisplay = document.getElementById("pix-expiration");
        let countdown = setInterval(() => {
            let minutes = Math.floor(seconds / 60);
            let remainingSeconds = seconds % 60;
            timerDisplay.innerText = `⏳ Expira em: ${minutes}m ${remainingSeconds}s`;

            if (seconds <= 0) {
                clearInterval(countdown);
                timerDisplay.innerText = "⏳ Expirado! Gere um novo QR Code.";
                document.getElementById("pix-payment-area").classList.add("opacity-50");
            }
            seconds--;
        }, 1000);
    }
});
</script>





<!-- Modal de Bloqueio -->
<div id="modal-bloqueio" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <h2 class="text-2xl font-bold text-red-600">⚠️ Tempo Expirado!</h2>
        <p class="text-gray-700 mt-3">Seu horário foi liberado devido à inatividade.</p>
        <button id="btn-voltar-inicio" class="mt-5 px-5 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            Voltar ao Início
        </button>
    </div>
</div>

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


<!-- Modal de Pagamento -->
<div id="payment-modal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
    <div class="bg-white p-6 rounded-lg shadow-lg text-center">
        <h2 id="modal-title" class="text-2xl font-bold"></h2>
        <p id="modal-message" class="text-gray-600 mt-2"></p>
        <button onclick="document.getElementById('payment-modal').classList.add('hidden')" 
                class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            OK
        </button>
    </div>
</div>


<!-- Armazena o TXID do PIX -->
<input type="hidden" id="pix-txid">

<!-- Input Oculto para armazenar o ID do Usuário -->
<input type="hidden" id="user-id" value="{{ auth()->user()->id }}">

<!-- Input Oculto para armazenar os serviços selecionados -->
<input type="hidden" id="selected-services" value='@json($selectedServices)'>

<!-- Input Oculto para armazenar o Schedule ID -->
<input type="hidden" id="schedule-id" value="{{ $schedule->id }}">



    </div>
</div>

            </div>

            
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    // Captura o botão de fechar e o modal
    const closeErrorBtn = document.getElementById("close-error");
    const errorModal = document.getElementById("error-modal");

    // Adiciona o evento de clique para fechar o modal
    closeErrorBtn.addEventListener("click", function () {
        errorModal.classList.add("hidden");
    });

    // Opcional: Fechar ao clicar fora do modal
    errorModal.addEventListener("click", function (event) {
        if (event.target === errorModal) {
            errorModal.classList.add("hidden");
        }
    });

    const stripe = Stripe("{{ config('services.stripe.key') }}");
    const elements = stripe.elements();
    const card = elements.create("card");
    card.mount("#card-element");

    // Seletores
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const creditCardFields = document.getElementById('credit-card-fields');
    const creditCardBtn = document.getElementById('credit-card-btn');
    const pixBtn = document.getElementById('pix-btn');
    const pixPaymentArea = document.getElementById('pix-payment-area');

    // Bloquear horário na entrada
    let scheduleId = $('#schedule-id').val();
    let inactivityTimer;
    let timeLimit = 300000; // 5 minutos
    let isBlocked = false;

    bloquearHorario();


    // Monitorar saída da aba
    document.addEventListener("visibilitychange", function () {
        if (document.hidden) {
            inactivityTimer = setTimeout(liberarHorario, timeLimit);
        } else {
            clearTimeout(inactivityTimer);
        }
    });

    // Detectar saída da página
    window.addEventListener("beforeunload", function () {
        liberarHorario();
    });

    // Função para bloquear horário
    function bloquearHorario() {
        if (scheduleId) {
            $.post('/lock-schedule', { schedule_id: scheduleId, _token: "{{ csrf_token() }}" });
        }
    }

    // Função para liberar horário
    function liberarHorario() {
        if (scheduleId && !isBlocked) {
            $.post('/unlock-schedule', { schedule_id: scheduleId, _token: "{{ csrf_token() }}" }, function () {
                isBlocked = true;
                $("#modal-bloqueio").removeClass("hidden").addClass("flex");
            });
        }
    }

// Fechar modal de bloqueio e redirecionar para a rota de agendamento
$("#btn-voltar-inicio").on("click", function () {
    window.location.href = "{{ route('client.schedule.index') }}";
});


    // Gerar PIX
    $('#pix-btn').on('click', function () {
        let valor = {{ $totalPrice }};
        let nome = "{{ auth()->user()->name ?? 'Nome Fictício' }}";
        let cpf = "123.456.789-00";

        $('#pix-btn').attr('disabled', true);
        $('#loading').removeClass('hidden');

        $.ajax({
            url: "/gerar-pix",
            type: "POST",
            data: { valor: valor, nome: nome, cpf: cpf, _token: "{{ csrf_token() }}" },
            success: function (response) {
                if (response.success) {
                    $('#pix-payment-area').removeClass('hidden');
                    $('#pix-qrcode').empty();
                    new QRCode(document.getElementById("pix-qrcode"), { text: response.location, width: 200, height: 200 });
                    $('#pix-code').val(response.pix_copiaecola);
                    $('#pix-txid').val(response.txid);
                    startCountdown(300);
                } else {
                    alert('Erro ao gerar QR Code Pix.');
                }
            },
            error: function () { alert('Erro ao conectar com o servidor.'); },
            complete: function () {
                $('#pix-btn').attr('disabled', false);
                $('#loading').addClass('hidden');
            }
        });
    });


    // Confirmação de pagamento PIX
    $('#confirm-payment').on('click', function (e) {
        e.preventDefault();
        
        let txid = $('#pix-txid').val().trim();
        let scheduleId = $('#schedule-id').val();
        let userId = $('#user-id').val();
        let services = JSON.parse($('#selected-services').val());

        $.ajax({
            url: "/verificar-pagamento",
            type: "GET",
            data: { txid: txid, schedule_id: scheduleId, user_id: userId, services: services },
            beforeSend: function () {
                $('#confirm-payment').html('<i class="fas fa-spinner fa-spin mr-2"></i> Verificando...').attr('disabled', true);
            },
            success: function (response) {
                if (response.success) {
                    $('#success-modal').removeClass('hidden');
                    let countdown = 5;
                    let timer = setInterval(function () {
                        countdown--;
                        $('#redirect-timer').text(countdown);
                        if (countdown <= 0) {
                            clearInterval(timer);
                            window.location.href = "/agendar";
                        }
                    }, 1000);
                } else {
                    $('#error-modal').removeClass('hidden');
                }
            },
            error: function () { $('#error-modal').removeClass('hidden'); },
            complete: function () { $('#confirm-payment').html('<i class="fas fa-check-circle mr-2"></i> Confirmar Pagamento').attr('disabled', false); }
        });
    });

    document.getElementById("payment-form").addEventListener("submit", async function (event) {
    event.preventDefault();

    let totalPrice = parseFloat(document.getElementById("total-price").textContent.trim().replace(".", "").replace(",", ".")) * 100;

    // Capturar valores do formulário
    let nomeCliente = document.getElementById("card-holder-name").value.trim();
    let cpfCliente = document.getElementById("cpf").value.trim();
    let userId = document.getElementById("user-id").value;
    let scheduleId = document.getElementById("schedule-id").value;
    let services = JSON.parse(document.getElementById("selected-services").value);

    if (!nomeCliente || !cpfCliente) {
        showModal("❌ Erro", "Por favor, preencha o Nome e o CPF.");
        return;
    }

    // 🔹 Mostra "Processando..." e desativa o botão
    let payButton = document.getElementById("pay-btn");
    payButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processando...';
    payButton.disabled = true;

    // Criar Token do Stripe
    const { token, error } = await stripe.createToken(card);

    if (error) {
        resetPaymentButton();
        showModal("❌ Erro no pagamento", error.message);
    } else {
        fetch("{{ route('checkout.cartao') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                stripeToken: token.id,
                valor: totalPrice,
                user_id: userId,
                schedule_id: scheduleId,
                services: services,
                nome: nomeCliente,  // 🔹 Incluindo nome do cliente
                cpf: cpfCliente      // 🔹 Incluindo CPF do cliente
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showModal("✅ Pagamento confirmado!", "Redirecionando...");
                setTimeout(() => { window.location.href = "/agendar"; }, 5000);
            } else {
                resetPaymentButton();
                showModal("❌ Erro no pagamento", "Tente novamente.");
            }
        })
        .catch(() => {
            resetPaymentButton();
            showModal("❌ Erro no servidor", "Falha ao processar o pagamento.");
        });
    }
});

// 🔹 Função para resetar o botão em caso de erro
function resetPaymentButton() {
    let payButton = document.getElementById("pay-btn");
    payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Pagar Agora';
    payButton.disabled = false;
}

// 🔹 Função para exibir modal de erro/sucesso
function showModal(title, message) {
    $('#modal-title').html(title);
    $('#modal-message').html(message);
    $('#payment-modal').removeClass('hidden');
}
});

</script>

                      
</x-layouts.payment>
