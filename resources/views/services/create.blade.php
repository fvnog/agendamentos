<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            ✂️ Adicionar Serviço
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6">Cadastre um novo serviço para a GS Barbearia</h3>

                <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Nome do Serviço -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300">Nome do Serviço</label>
                        <input type="text" name="name" id="name" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                               value="{{ old('name') }}" required>
                    </div>

                    <!-- Duração do Serviço -->
                    <div class="mb-4">
                        <label for="duration" class="block text-sm font-medium text-gray-300">Duração (minutos)</label>
                        <input type="number" name="duration" id="duration" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                               value="{{ old('duration') }}" required>
                    </div>

                    <!-- Preço do Serviço -->
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-300">Preço (R$)</label>
                        <input type="number" step="0.01" name="price" id="price" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                               value="{{ old('price') }}" required>
                    </div>

                    <!-- Foto do Serviço -->
                    <div class="mb-6">
                        <label for="photo" class="block text-sm font-medium text-gray-300">Foto do Serviço</label>
                        <input type="file" name="photo" id="photo" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none">
                    </div>

                    <!-- Botão de Salvar -->
                    <button type="submit" 
                            class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300">
                        ✅ Salvar Serviço
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
