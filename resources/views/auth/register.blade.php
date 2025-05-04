<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-black">
        <!-- Logo -->
        <div class="mb-8">
            <img src="{{ asset('storage/img/gs2.png') }}" alt="GS Barbearia" class="w-48">
        </div>

        <!-- Formulário de Registro -->
        <div class="w-full sm:max-w-md px-8 py-6 bg-gray-900 text-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6">Criar Conta na GS Barbearia</h2>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nome -->
                <div class="mb-4">
                    <label for="name" class="block text-gray-300 text-sm font-semibold">Nome</label>
                    <input id="name" type="text" name="name" required autofocus
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none" 
                        value="{{ old('name') }}" autocomplete="name">
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Telefone com Máscara -->
                <div class="mb-4">
                    <label for="telefone" class="block text-gray-300 text-sm font-semibold">Telefone</label>
                    <input id="telefone" type="tel" name="telefone" required
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                        placeholder="(xx) xxxxx-xxxx" value="{{ old('telefone') }}" autocomplete="tel">
                    <x-input-error :messages="$errors->get('telefone')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-300 text-sm font-semibold">E-mail</label>
                    <input id="email" type="email" name="email" required 
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none" 
                        value="{{ old('email') }}" autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Senha -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-300 text-sm font-semibold">Senha</label>
                    <input id="password" type="password" name="password" required 
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                        autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Confirmar Senha -->
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-gray-300 text-sm font-semibold">Confirmar Senha</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required 
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                        autocomplete="new-password">
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Botão de Cadastro -->
                <div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition duration-300">
                        Criar Conta
                    </button>
                </div>
            </form>

            <!-- Link para Login -->
            <p class="text-center text-sm text-gray-400 mt-4">
                Já tem uma conta?
                <a href="{{ route('login') }}" class="text-green-400 hover:underline">Faça login</a>
            </p>
        </div>
    </div>

<!-- Importando jQuery e jQuery Mask -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function(){
        $('#telefone').mask('(00) 00000-0000'); // Aplica a máscara ao telefone
    });
</script>

</x-guest-layout>
