<x-layouts.client>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Pagamento Pix</h2>
        
        <div class="mb-4">
            <p><strong>Total:</strong> R$ {{ number_format($total_value, 2, ',', '.') }}</p>
        </div>

        <div class="mb-4">
            <p><strong>QR Code:</strong></p>
            <img src="{{ $pix_qrcode }}" alt="QR Code Pix" class="w-1/2 mx-auto mb-4">
        </div>

        <div class="mb-4">
            <p><strong>Código Copia e Cola:</strong></p>
            <textarea readonly class="w-full p-2 border rounded-md">{{ $pix_url }}</textarea>
        </div>

        <form action="{{ route('client.payment.checkStatus', $reservation->pix_txid) }}" method="GET">
            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Verificar Pagamento
            </button>
        </form>
    </div>
</x-layouts.client>
