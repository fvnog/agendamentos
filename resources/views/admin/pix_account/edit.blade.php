<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-qrcode"></i> Configurar Minha Conta Pix
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-8 text-white">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-university"></i> Minha Conta Pix</h3>

                @if(session('success'))
                    <div class="bg-green-600 text-white p-3 rounded-lg mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($pixAccount)
                    <!-- 🔹 Exibição da Conta Atual -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-300">Banco</label>
                            <input type="text" value="{{ $pixAccount->bank_name }}" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>

                        @if($pixAccount->bank_name === "Banco do Brasil")
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Client ID</label>
                                <input type="text" value="{{ substr($pixAccount->bb_client_id, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300">Client Secret</label>
                                <input type="text" value="{{ substr($pixAccount->bb_client_secret, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                            </div>

                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-300">GW-APP-KEY</label>
                                <input type="text" value="{{ substr($pixAccount->bb_gw_app_key, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                            </div>
                        @endif

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-300">Tipo da Chave Pix</label>
                            <input type="text" value="{{ ucfirst($pixAccount->pix_key_type) }}" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-300">Chave Pix</label>
                            <input type="text" value="{{ substr($pixAccount->pix_key, 0, 5) }}****" readonly class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-400">
                        </div>
                    </div>

                    <div class="mt-6 text-center">
                        <button id="open-modal" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            <i class="fas fa-exchange-alt"></i> Alterar Minha Conta Pix
                        </button>
                    </div>
                @endif

                <!-- 🔹 Formulário de Cadastro/Alteração -->
                <form id="pix-form" action="{{ route('pix_account.update') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 {{ $pixAccount ? 'hidden' : '' }}">
                    @csrf
                    @method('PUT')

                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-300">Banco</label>
                        <select id="bank_name" name="bank_name" required class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                            <option value="Banco do Brasil" selected>Banco do Brasil</option>
                            <option value="Itaú">Itaú</option>
                            <option value="Bradesco">Bradesco</option>
                            <option value="Santander">Santander</option>
                            <option value="Caixa Econômica Federal">Caixa Econômica Federal</option>
                        </select>
                    </div>

<!-- Campos Específicos do Banco do Brasil -->
<div id="bb-fields" class="col-span-2 bg-gray-800 p-4 rounded-lg border border-gray-700">
    <h4 class="text-lg font-semibold text-yellow-400 mb-3"><i class="fas fa-info-circle"></i> Configuração do Banco do Brasil</h4>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-300">Client ID</label>
            <input type="text" id="bb-client-id" name="bb_client_id" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-300">Client Secret</label>
            <input type="text" id="bb-client-secret" name="bb_client_secret" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-300">GW-APP-KEY</label>
            <input type="text" id="bb-gw-app-key" name="bb_gw_app_key" class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-300">Tipo da Chave Pix</label>
            <select name="pix_key_type" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
                <option value="cpf">CPF</option>
                <option value="cnpj">CNPJ</option>
                <option value="email">E-mail</option>
                <option value="telefone">Telefone</option>
                <option value="aleatoria">Chave Aleatória</option>
            </select>
        </div>

        <div class="col-span-2">
            <label class="block text-sm font-medium text-gray-300">Chave Pix</label>
            <input type="text" id="pix-key" name="pix_key" required class="w-full px-4 py-2 bg-gray-900 border border-gray-700 rounded-lg text-white">
        </div>
    </div>

    <!-- 🔹 Tutorial para obter as credenciais -->
    <div class="mt-6 bg-gray-900 p-4 rounded-lg border border-gray-700 text-sm text-gray-300">
        <h5 class="text-yellow-400 font-semibold mb-2">📌 Passos para obter as credenciais do Banco do Brasil:</h5>
        <ol class="list-decimal pl-6 space-y-2">
            <li>Acesse o portal <a href="https://developers.bb.com.br" target="_blank" class="text-blue-400 underline">BB Developers</a> e faça login.</li>
            <li>Crie uma nova aplicação e vincule seu **CNPJ** ao sistema.</li>
            <li>Solicite acesso à API de **Pix Cobrança** e aguarde a aprovação.</li>
            <li>Após aprovado, acesse sua aplicação para obter:
                <ul class="list-disc pl-5">
                    <li><strong>Client ID</strong></li>
                    <li><strong>Client Secret</strong></li>
                    <li><strong>GW-APP-KEY</strong></li>
                </ul>
            </li>
            <li>Copie as credenciais e cole nos campos acima.</li>
            <li>Salve as configurações para ativar o Pix pelo Banco do Brasil.</li>
        </ol>
    </div>
</div>


                    <button id="save-btn" type="submit" class="col-span-2 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                        <i class="fas fa-save"></i> Salvar Configuração
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const bankSelect = document.getElementById("bank_name");
            const bbFields = document.getElementById("bb-fields");
            const saveButton = document.getElementById("save-btn");
            const openModalBtn = document.getElementById("open-modal");
            const pixForm = document.getElementById("pix-form");

            bankSelect.addEventListener("change", function () {
                if (this.value === "Banco do Brasil") {
                    bbFields.classList.remove("hidden");
                } else {
                    bbFields.classList.add("hidden");
                }
            });

            openModalBtn.addEventListener("click", function () {
                pixForm.classList.remove("hidden");
                openModalBtn.classList.add("hidden");
            });
        });
    </script>
</x-app-layout>
