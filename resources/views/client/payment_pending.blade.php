<x-layouts.client>
    <div class="min-h-screen flex items-center justify-center bg-yellow-100 text-black">
        <div class="bg-white p-6 rounded shadow text-center">
            <h1 class="text-2xl font-bold text-yellow-600 mb-2">Pagamento Pendente ⏳</h1>
            <p class="text-gray-700">Estamos aguardando a confirmação do seu pagamento...</p>
            <p id="status" class="mt-2 text-sm text-gray-600">Verificando status a cada 5 segundos...</p>
        </div>
    </div>

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


</x-layouts.client>
