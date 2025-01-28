<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Adicionar Serviço') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('services.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Nome do Serviço</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full" value="{{ old('name') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="duration" class="block text-sm font-medium text-gray-700">Duração (minutos)</label>
                            <input type="number" name="duration" id="duration" class="mt-1 block w-full" value="{{ old('duration') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="price" class="block text-sm font-medium text-gray-700">Preço (R$)</label>
                            <input type="number" step="0.01" name="price" id="price" class="mt-1 block w-full" value="{{ old('price') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="photo" class="block text-sm font-medium text-gray-700">Foto do Serviço</label>
                            <input type="file" name="photo" id="photo" class="mt-1 block w-full">
                        </div>

                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Salvar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
