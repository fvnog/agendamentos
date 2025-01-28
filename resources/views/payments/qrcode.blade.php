<x-layouts.client>
    <div class="bg-white p-6 rounded-lg shadow-md text-center">
        <h2 class="text-xl font-semibold mb-4">Pagamento Pix</h2>
        <p class="text-sm text-gray-600 mb-4">Use o QR Code abaixo para realizar o pagamento.</p>
        <img src="data:image/png;base64,{{ $qrcode }}" alt="QR Code Pix" class="mx-auto">
        <p class="mt-4 text-sm text-gray-600">Ou copie o código abaixo:</p>
        <textarea readonly class="w-full p-2 border rounded">{{ $payload }}</textarea>
        <p class="mt-4 text-red-500">Este QR Code é válido por {{ $expiration }} minutos.</p>

        <form method="GET" action="{{ route('payment.check') }}" class="mt-6">
            <input type="hidden" name="txid" value="{{ $txid }}">
            <button 
                type="submit" 
                class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Verificar Pagamento
            </button>
        </form>
    </div>
</x-layouts.client>
