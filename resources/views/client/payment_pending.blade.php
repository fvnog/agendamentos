<x-layouts.payment>
    <div class="min-h-screen p-4 flex items-center justify-center bg-gradient-to-br from-yellow-100 via-yellow-50 to-yellow-200 text-black">
        <div class="bg-white p-8 rounded-2xl shadow-2xl text-center max-w-md w-full animate-fade-in">
            
            <!-- Ícone animado -->
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 rounded-full bg-yellow-100 flex items-center justify-center animate-pulse">
                    <svg class="w-8 h-8 text-yellow-500 animate-spin-slow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25 text-yellow-400" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75 text-yellow-600" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16v-4l-3 3 3 3v-4a8 8 0 01-8-8z"></path>
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl font-extrabold text-yellow-600 mb-2">Pagamento Pendente</h1>
            <p class="text-gray-700 text-lg">Estamos aguardando a confirmação do seu pagamento...</p>

            <!-- Barra de progresso animada -->
            <div class="mt-6 w-full bg-yellow-100 rounded-full h-2 overflow-hidden">
                <div class="bg-yellow-500 h-2 animate-progress"></div>
            </div>
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

        @keyframes spin-slow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 3s linear infinite;
        }

        @keyframes progress {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-progress {
            animation: progress 1.5s linear infinite;
        }
    </style>

    <script>
        const paymentId = new URLSearchParams(window.location.search).get('payment_id');

        const checkStatus = async () => {
            if (!paymentId) return;

            try {
                const response = await fetch(`/payment/check-status?payment_id=${paymentId}`);
                const data = await response.json();

                if (data.status === 'approved') {
                    window.location.href = "{{ route('payment.success') }}";
                }
            } catch (error) {
                console.error('Erro ao verificar status:', error);
            }
        };

        setInterval(checkStatus, 5000);
    </script>
</x-layouts.payment>
