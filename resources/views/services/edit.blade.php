<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-edit"></i> Editar Serviço
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6">
                    <i class="fas fa-cut"></i> Atualize os detalhes do serviço
                </h3>

                <form method="POST" action="{{ route('services.update', $service) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Nome do Serviço -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-300">
                            Nome do Serviço
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $service->name) }}"
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duração -->
                    <div class="mb-4">
                        <label for="duration" class="block text-sm font-medium text-gray-300">
                            Duração (minutos)
                        </label>
                        <input type="number" name="duration" id="duration" value="{{ old('duration', $service->duration) }}"
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                               required>
                        @error('duration')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Preço -->
                    <div class="mb-4">
                        <label for="price" class="block text-sm font-medium text-gray-300">
                            Preço (R$)
                        </label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $service->price) }}"
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none"
                               required>
                        @error('price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Foto do Serviço -->
                    <div class="mb-4">
                        <label for="photo" class="block text-sm font-medium text-gray-300">
                            Foto do Serviço (opcional)
                        </label>
                        <input type="file" name="photo" id="photo"
                               class="mt-1 block w-full bg-gray-800 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-green-500 focus:outline-none">
                        @if ($service->photo)
                            <div class="mt-3">
                                <img src="{{ asset('storage/' . $service->photo) }}" alt="{{ $service->name }}" class="w-32 h-32 object-cover rounded-lg">
                            </div>
                        @endif
                        @error('photo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Botão de Atualizar -->
                    <button type="submit" class="w-full py-3 bg-green-500 text-white font-semibold rounded-lg hover:bg-green-600 transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Atualizar Serviço
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
