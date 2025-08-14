<x-layouts.payment>
    <div class="min-h-screen p-4 flex items-center justify-center bg-gradient-to-br from-red-100 via-red-50 to-red-200 text-black">
        <div class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md w-full animate-fade-in">
            
            <!-- Ícone animado -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-red-100 flex items-center justify-center animate-pulse">
                    <svg class="w-8 h-8 text-red-500 animate-shake" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <path class="opacity-75" stroke="currentColor" stroke-width="2" d="M6 6l12 12M6 18L18 6" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold text-red-600 mb-2">Pagamento Falhou</h1>
            <p class="text-gray-700 text-lg">Houve um problema ao processar seu pagamento.<br>Tente novamente.</p>

            <a href="/" 
               class="mt-6 inline-block px-6 py-3 bg-red-600 text-white rounded-lg shadow-lg hover:bg-red-700 transform hover:scale-105 transition-all duration-200">
               Voltar
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

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-4px); }
            40%, 80% { transform: translateX(4px); }
        }
        .animate-shake {
            animation: shake 0.5s ease-in-out infinite;
        }
    </style>
</x-layouts.payment>
