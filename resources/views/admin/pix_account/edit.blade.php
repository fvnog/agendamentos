<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-qrcode"></i> Configurar Meios de Pagamento
        </h2>
    </x-slot>

   <div class="py-6 min-h-screen">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-gray-900 shadow-lg rounded-lg p-8 text-white">
            
            @if(session('success'))
                <div class="bg-green-600 text-white p-3 rounded-lg mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- 🔹 Tabs de navegação -->
            <ul class="flex border-b border-gray-700 mb-6">
                <li class="mr-4">
                    <button class="tab-button text-gray-400 px-4 py-2 rounded-t-lg focus:outline-none focus:text-white active" data-target="tab-pix">
                        <i class="fas fa-university"></i> Conta Pix
                    </button>
                </li>
                <li>
                    <button class="tab-button text-gray-400 px-4 py-2 rounded-t-lg focus:outline-none focus:text-white" data-target="tab-stripe">
                        <i class="fas fa-credit-card"></i> Conta Stripe
                    </button>
                </li>
            </ul>

            <!-- 🔹 Tab Pix -->
            <div id="tab-pix" class="tab-content">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-university"></i> Minha Conta Pix</h3>

                @if($pixAccount)
                    <div id="pix-view" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-300">Banco</label>
                            <input type="text" value="{{ $pixAccount->bank_name }}" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-300">Chave Pix</label>
                            <input type="text" value="{{ substr($pixAccount->pix_key, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <button id="edit-pix-btn" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-exchange-alt"></i> Alterar Conta Pix
                        </button>
                    </div>
                @endif

<!-- 🔹 Formulário de Alteração Pix -->
<form id="pix-form" action="{{ route('pix_account.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 hidden">
    @csrf
    @method('PUT')

    <!-- Escolher o banco -->
    <div class="col-span-2">
        <label class="block text-sm font-medium text-gray-300 mb-2">Escolha o banco</label>
        <div class="flex space-x-6">
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="bank_name" value="Banco do Brasil" class="hidden peer" checked>
                <div class="px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white peer-checked:bg-blue-600">
                    Banco do Brasil
                </div>
            </label>
            <label class="flex items-center cursor-pointer">
                <input type="radio" name="bank_name" value="Sicoob" class="hidden peer">
                <div class="px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white peer-checked:bg-blue-600">
                    Sicoob
                </div>
            </label>
        </div>
    </div>

    <!-- 🔹 Campos dinâmicos do banco -->
    <div id="bank-fields"></div>

    <div class="col-span-2 flex justify-between">
        <button id="cancel-pix-btn" type="button" class="py-3 px-6 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
            <i class="fas fa-times"></i> Cancelar
        </button>
        <button type="submit" class="py-3 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <i class="fas fa-save"></i> Salvar Configuração
        </button>
    </div>
</form>

            </div>

            <!-- 🔹 Tab Stripe -->
            <div id="tab-stripe" class="tab-content hidden">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-credit-card"></i> Minha Conta Stripe</h3>

                @if(isset($stripeAccount) && $stripeAccount)
                    <div id="stripe-view" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Public Key</label>
                            <input type="text" value="{{ substr($stripeAccount->stripe_public_key, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300">Secret Key</label>
                            <input type="text" value="{{ substr($stripeAccount->stripe_secret_key, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <button id="edit-stripe-btn" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-exchange-alt"></i> Alterar Conta Stripe
                        </button>
                    </div>
                @endif

                <!-- 🔹 Formulário de Alteração Stripe -->
                <form id="stripe-form" action="{{ route('stripe_account.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 hidden">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-sm font-medium text-gray-300">Public Key</label>
                        <input type="text" name="stripe_public_key" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite a Public Key">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300">Secret Key</label>
                        <input type="text" name="stripe_secret_key" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite a Secret Key">
                    </div>

                    <div class="col-span-2 flex justify-between">
                        <button id="cancel-stripe-btn" type="button" class="py-3 px-6 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                            <i class="fas fa-times"></i> Cancelar
                        </button>
                        <button type="submit" class="py-3 px-6 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-save"></i> Salvar Configuração
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function () {
    // Alternar entre as abas
    document.querySelectorAll(".tab-button").forEach(button => {
        button.addEventListener("click", function () {
            document.querySelectorAll(".tab-button").forEach(btn => btn.classList.remove("text-white", "bg-gray-800"));
            this.classList.add("text-white", "bg-gray-800");

            document.querySelectorAll(".tab-content").forEach(tab => tab.classList.add("hidden"));
            document.getElementById(this.dataset.target).classList.remove("hidden");
        });
    });

    // Editar e cancelar edição da conta Pix
    const editPixBtn = document.getElementById("edit-pix-btn");
    const pixForm = document.getElementById("pix-form");
    const pixView = document.getElementById("pix-view");
    const cancelPixBtn = document.getElementById("cancel-pix-btn");
    const bankFields = document.getElementById("bank-fields");

    if (editPixBtn) {
        editPixBtn.addEventListener("click", function () {
            pixForm.classList.remove("hidden");
            pixView.classList.add("hidden");
            editPixBtn.classList.add("hidden");

            // 🚀 Atualiza os campos do banco ao abrir a edição
            setTimeout(() => {
                const selectedBank = document.querySelector("input[name='bank_name']:checked");
                if (selectedBank) {
                    selectedBank.dispatchEvent(new Event("change"));
                }
            }, 100);
        });
    }

    if (cancelPixBtn) {
        cancelPixBtn.addEventListener("click", function () {
            pixForm.classList.add("hidden");
            pixView.classList.remove("hidden");
            editPixBtn.classList.remove("hidden");
        });
    }

    // Editar e cancelar edição da conta Stripe
    const editStripeBtn = document.getElementById("edit-stripe-btn");
    const stripeForm = document.getElementById("stripe-form");
    const stripeView = document.getElementById("stripe-view");
    const cancelStripeBtn = document.getElementById("cancel-stripe-btn");

    if (editStripeBtn) {
        editStripeBtn.addEventListener("click", function () {
            stripeForm.classList.remove("hidden");
            stripeView.classList.add("hidden");
            editStripeBtn.classList.add("hidden");
        });
    }

    if (cancelStripeBtn) {
        cancelStripeBtn.addEventListener("click", function () {
            stripeForm.classList.add("hidden");
            stripeView.classList.remove("hidden");
            editStripeBtn.classList.remove("hidden");
        });
    }

    // Alternar campos conforme banco selecionado
    document.querySelectorAll("input[name='bank_name']").forEach(radio => {
        radio.addEventListener("change", function () {
            updateBankFields(this.value);
        });
    });

    function updateBankFields(bank) {
        bankFields.innerHTML = "";
        let bankSpecificFields = "";

        if (bank === "Banco do Brasil") {
            bankSpecificFields = `
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300">Client ID</label>
                    <input type="text" name="bb_client_id" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite o Client ID">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300">Client Secret</label>
                    <input type="text" name="bb_client_secret" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite o Client Secret">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300">GW-APP-KEY</label>
                    <input type="text" name="bb_gw_app_key" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite o GW-APP-KEY">
                </div>
            `;
        } else if (bank === "Sicoob") {
            bankSpecificFields = `
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300">Client ID</label>
                    <input type="text" name="sicoob_client_id" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite o Client ID">
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-300">Access Token</label>
                    <input type="text" name="sicoob_access_token" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite o Access Token">
                </div>
            `;
        }

        // Campos fixos (Tipo da Chave Pix e Chave Pix)
        bankFields.innerHTML = `
            ${bankSpecificFields}

            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-300">Tipo da Chave Pix</label>
                <select name="pix_key_type" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
                    <option value="cpf">CPF</option>
                    <option value="cnpj">CNPJ</option>
                    <option value="email">E-mail</option>
                    <option value="telefone">Telefone</option>
                    <option value="aleatoria">Chave Aleatória</option>
                </select>
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-300">Chave Pix</label>
                <input type="text" name="pix_key" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white" placeholder="Digite sua Chave Pix">
            </div>
        `;

        // Adicionar o tutorial abaixo dos campos
        let tutorial = "";

        if (bank === "Banco do Brasil") {
            tutorial = `
                <div class="mt-6 bg-gray-900 p-4 rounded-lg border border-gray-700 text-sm text-gray-300">
                    <h5 class="text-yellow-400 font-semibold mb-2">📌 Como obter as credenciais do Banco do Brasil:</h5>
                    <ol class="list-decimal pl-6 space-y-2">
                        <li>Acesse o portal <a href="https://developers.bb.com.br" target="_blank" class="text-blue-400 underline">BB Developers</a> e faça login.</li>
                        <li>Crie uma nova aplicação e vincule seu **CNPJ** ao sistema.</li>
                        <li>Solicite acesso à API de **Pix Cobrança** e aguarde a aprovação.</li>
                        <li>Copie as credenciais e cole nos campos acima.</li>
                    </ol>
                </div>
            `;
        } else if (bank === "Sicoob") {
            tutorial = `
                <div class="mt-6 bg-gray-900 p-4 rounded-lg border border-gray-700 text-sm text-gray-300">
                    <h5 class="text-yellow-400 font-semibold mb-2">📌 Como obter as credenciais do Sicoob:</h5>
                    <ol class="list-decimal pl-6 space-y-2">
                        <li>Acesse o portal <a href="https://sandbox.sicoob.com.br" target="_blank" class="text-blue-400 underline">Sicoob Developers</a> e faça login.</li>
                        <li>Crie uma aplicação e vincule seu **CNPJ** ao sistema.</li>
                        <li>Solicite acesso à API de **Pix Recebimentos** e aguarde a aprovação.</li>
                        <li>Copie as credenciais e cole nos campos acima.</li>
                    </ol>
                </div>
            `;
        }

        bankFields.innerHTML += tutorial;
    }
});
</script>

</x-app-layout>