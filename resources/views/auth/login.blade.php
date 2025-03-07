<x-guest-layout>
    <div class="min-h-screen flex flex-col items-center justify-center bg-black">
        <!-- Logo -->
        <div class="mb-8">
            <img src="{{ asset('storage/img/gs2.png') }}" alt="GS Barbearia" class="w-48">
        </div>

        <!-- Formulário de Login -->
        <div class="w-full sm:max-w-md px-8 py-6 bg-gray-900 text-white shadow-lg rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-6">Bem-vindo à GS Barbearia</h2>

            <!-- Status da Sessão -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-300 text-sm font-semibold">E-mail</label>
                    <input id="email" type="email" name="email" required autofocus
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none" 
                        value="{{ old('email') }}" autocomplete="username">
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Senha -->
                <div class="mb-4">
                    <label for="password" class="block text-gray-300 text-sm font-semibold">Senha</label>
                    <input id="password" type="password" name="password" required 
                        class="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                        autocomplete="current-password">
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
                </div>

                <!-- Lembrar-me -->
                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center text-sm text-gray-300">
                        <input type="checkbox" name="remember" class="text-green-500 focus:ring-green-500">
                        <span class="ml-2">Lembrar-me</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-green-400 text-sm hover:underline">
                            Esqueceu a senha?
                        </a>
                    @endif
                </div>

                <!-- Botão de Login -->
                <div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition duration-300">
                        Entrar
                    </button>
                </div>
            </form>

            <!-- Criar Conta -->
            @if (Route::has('register'))
                <p class="text-center text-sm text-gray-400 mt-4">
                    Ainda não tem uma conta?
                    <a href="{{ route('register') }}" class="text-green-400 hover:underline">Cadastre-se</a>
                </p>
            @endif
        </div>
    </div>
</x-guest-layout>
