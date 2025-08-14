<x-layouts.payment>
    <div class="min-h-screen p-4 flex items-center justify-center bg-gradient-to-br from-green-100 via-green-50 to-green-200 text-black">
        <div class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md w-full animate-fade-in">
            
            <!-- Ícone animado -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center animate-pulse">
                    <svg class="w-8 h-8 text-green-500 animate-bounce" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path class="opacity-75" stroke="currentColor" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold text-green-600 mb-2">Pagamento Aprovado</h1>
            <p class="text-gray-700 text-lg">Seu pagamento foi confirmado com sucesso.<br>Seu horário foi agendado!</p>

            <a href="/" 
               class="mt-6 inline-block px-6 py-3 bg-green-600 text-white rounded-lg shadow-lg hover:bg-green-700 transform hover:scale-105 transition-all duration-200">
               Voltar à página inicial
            </a>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.6s ease-in-out;
        }
    </style>
</x-layouts.payment>
