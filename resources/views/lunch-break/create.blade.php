<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-plus"></i> Cadastrar Horário de Almoço
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6">
                    <i class="fas fa-utensils"></i> Defina o horário do seu almoço
                </h3>

                <form method="POST" action="{{ route('lunch-break.store') }}">
                    @csrf

                    <!-- Hora de Início -->
                    <div class="mb-4">
                        <label for="start_time" class="block text-sm font-medium text-gray-300">
                            <i class="far fa-clock"></i> Hora de Início do Almoço
                        </label>
                        <input type="time" name="start_time" id="start_time" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-yellow-500 focus:outline-none @error('start_time') border-red-500 @enderror"
                               value="{{ old('start_time') }}" required>
                        @error('start_time')
                            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora de Término -->
                    <div class="mb-6">
                        <label for="end_time" class="block text-sm font-medium text-gray-300">
                            <i class="far fa-clock"></i> Hora de Término do Almoço
                        </label>
                        <input type="time" name="end_time" id="end_time" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-yellow-500 focus:outline-none @error('end_time') border-red-500 @enderror"
                               value="{{ old('end_time') }}" required>
                        @error('end_time')
                            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botão de Criar -->
                    <button type="submit" 
                            class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Cadastrar Horário de Almoço
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
