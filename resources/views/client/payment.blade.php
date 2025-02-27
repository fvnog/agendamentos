<x-layouts.payment>
    <div class="shadow-md flex">
        <!-- Resumo da Reserva à Esquerda -->
        <div class="w-2/3 p-20 bg-gray-800 text-white">
            <h5 class="font-semibold mb-8">Resumo da Reserva</h5>

            <div class="mb-8">
    <strong class="mb-3">Total a pagar:</strong><br>
    <h1 class="font-bold mt-3">
        <span class="text-4xl">R$</span>
        <span class="text-6xl" id="total-price">{{ number_format($totalPrice, 2, ',', '.') }}</span>
    </h1>
</div>


            <div class="mb-6 ">
                <p><i class="fas fa-calendar-day mr-2 mb-3"></i><strong>Data:</strong> {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</p>
                <p><i class="fas fa-clock mr-2 mb-3"></i><strong>Horário:</strong> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                <p><i class="fas fa-user mr-2"></i><strong>Barbeiro:</strong> {{ $barber->name }}</p>


            </div>
            <hr>
            <div class="mt-4">
    <strong>Serviços Selecionados:</strong>
    <div class="mt-5 space-y-2">
        @foreach($selectedServices as $service)
            <div class="flex justify-between ">
                <span>{{ $service->name }} ({{ $service->duration }} min)</span>
                <span class="font-semibold">R$ {{ number_format($service->price, 2, ',', '.') }}</span>
            </div>
        @endforeach
    </div>
</div>


        </div>

        <!-- Opções de Pagamento à Direita -->
        <div class="w-2/3 p-20 bg-slate-200">
            <h3 class="text-xl font-semibold text-gray-800 mb-4"><i class="fas fa-credit-card mr-2"></i>Escolha a Forma de Pagamento</h3>
            <form id="payment-form">
                <div class="mb-6">
                    <label for="payment-method" class="block text-gray-700 font-medium mb-2"><i class="fas fa-list mr-2"></i>Escolha a forma de pagamento:</label>
                    <select id="payment-method" name="payment_method" class="w-full rounded-md border-gray-300 shadow-sm p-2">
    <option value="">Selecione uma opção</option>
    <option value="credit_card" {{ old('payment_method') === 'credit_card' ? 'selected' : '' }}>Cartão de Crédito</option>
    <option value="pix" {{ old('payment_method') === 'pix' ? 'selected' : '' }}>Pix</option>
</select>

                </div>

                <!-- Exibir campos específicos do cartão -->
                <div id="credit-card-fields" class="hidden">
                    <div class="mb-4">
                        <label for="cpf" class="block text-gray-700 font-medium mb-2"><i class="fas fa-id-card mr-2"></i>CPF:</label>
                        <input type="text" id="cpf" name="cpf" class="w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="Digite seu CPF">
                    </div>
                    <div class="mb-4">
                        <label for="card-number" class="block text-gray-700 font-medium mb-2"><i class="fas fa-credit-card mr-2"></i>Número do Cartão:</label>
                        <input type="text" id="card-number" name="card_number" class="w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="Digite o número do seu cartão">
                    </div>
                    <div class="flex space-x-4 mb-4">
                        <div class="w-1/2">
                            <label for="expiry-date" class="block text-gray-700 font-medium mb-2"><i class="fas fa-calendar-alt mr-2"></i>Data de Vencimento:</label>
                            <input type="text" id="expiry-date" name="expiry_date" class="w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="MM/AA">
                        </div>
                        <div class="w-1/2">
                            <label for="cvc" class="block text-gray-700 font-medium mb-2"><i class="fas fa-lock mr-2"></i>CVC:</label>
                            <input type="text" id="cvc" name="cvc" class="w-full rounded-md border-gray-300 shadow-sm p-2" placeholder="CVC">
                        </div>
                    </div>

                    <!-- Parcelamento -->
                    <div class="mb-4">
                        <label for="installments" class="block text-gray-700 font-medium mb-2"><i class="fas fa-credit-card mr-2"></i>Número de Parcelas (até 3x):</label>
                        <select id="installments" name="installments" class="w-full rounded-md border-gray-300 shadow-sm p-2">
                            <option value="1">1x</option>
                            <option value="2">2x</option>
                            <option value="3">3x</option>
                        </select>
                        <div id="installment-fee" class="text-sm text-gray-700 mt-2">
                            <p><strong><i class="fas fa-percentage mr-2"></i>Taxa por Parcela:</strong> 1,5% do valor total por parcela</p>
                        </div>
                    </div>
                </div>

                <!-- Resumo e Botões -->
                <div id="payment-summary" class="hidden">
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h3 class="font-semibold text-gray-800"><i class="fas fa-book mr-2"></i>Resumo da Reserva</h3>
                        <p><strong>Barbeiro:</strong> {{ $barber->name }}</p>
                        <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}</p>
                        <p><strong>Horário:</strong> {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</p>
                        <p><strong>Serviços:</strong>
                            <ul class="list-disc pl-5">
                                @foreach($selectedServices as $service)
                                    <li>{{ $service->name }} ({{ $service->duration }} min) - R$ {{ number_format($service->price, 2, ',', '.') }}</li>
                                @endforeach
                            </ul>
                        </p>
                        <p><strong>Total:</strong> R$ <span id="total-price-summary">{{ number_format($totalPrice, 2, ',', '.') }}</span></p>
                    </div>

                    <!-- Botões de Pagamento -->
                    <div class="flex justify-between gap-4">
                        <!-- Cartão de Crédito -->
                        <button type="button" id="credit-card-btn" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 w-1/2">
                            <i class="fas fa-credit-card mr-2"></i> Pagar com Cartão
                        </button>

                        <!-- Pix -->
                        <button type="button" id="pix-btn" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 w-1/2">
                            <i class="fas fa-qrcode mr-2"></i> Pagar com Pix
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Código Pix -->
    <div id="pix-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-xl font-semibold mb-4"><i class="fas fa-qrcode mr-2"></i>Código Pix</h2>
            <p class="text-gray-700 mb-4">Aguarde enquanto geramos seu código Pix.</p>
            <div id="pix-code" class="text-xl font-semibold text-gray-800 mb-4"></div>
            <button type="button" onclick="closePixModal()" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg">
                Fechar
            </button>
        </div>
    </div>

    <script>
    function togglePaymentFields(value) {
        const creditCardFields = document.getElementById('credit-card-fields');
        
        if (value === 'credit_card') {
            creditCardFields.classList.remove('hidden');
        } else {
            creditCardFields.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethod = document.getElementById('payment-method');
        togglePaymentFields(paymentMethod.value); // Exibe campos se já houver seleção

        paymentMethod.addEventListener('change', function() {
            togglePaymentFields(this.value);
        });
    });
</script>


    <script>
        document.getElementById('payment-method').addEventListener('change', function() {
            const creditCardFields = document.getElementById('credit-card-fields');
            const installmentFee = document.getElementById('installment-fee');
            if (this.value === 'credit_card') {
                creditCardFields.classList.remove('hidden');
                installmentFee.classList.remove('hidden');
            } else {
                creditCardFields.classList.add('hidden');
                installmentFee.classList.add('hidden');
            }
        });

        document.getElementById('installments').addEventListener('change', function() {
            let installmentCount = parseInt(this.value);
            let totalPrice = parseFloat(document.getElementById('total-price').innerText.replace('R$', '').replace(',', '.'));
            let fee = 1.5 / 100;
            let newTotal = totalPrice * (1 + fee * installmentCount);
            document.getElementById('total-price-summary').innerText = 'R$ ' + newTotal.toFixed(2).replace('.', ',');
        });
    </script>
</x-layouts.payment>
