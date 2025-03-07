<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-calendar-plus"></i> Criar Horários
        </h2>
    </x-slot>

    <div class="py-6  min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6">
                    <i class="fas fa-clock"></i> Defina os horários disponíveis
                </h3>

                <form method="POST" action="{{ route('schedules.store') }}">
                    @csrf

                    <!-- Intervalo (Minutos) -->
                    <div class="mb-4">
                        <label for="interval" class="block text-sm font-medium text-gray-300">
                            <i class="fas fa-stopwatch"></i> Intervalo (em minutos)
                        </label>
                        <input type="number" name="interval" id="interval" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none @error('interval') border-red-500 @enderror"
                               value="{{ old('interval', 30) }}" required>
                        @error('interval')
                            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Período -->
                    <div class="mb-4">
                        <label for="time_frame" class="block text-sm font-medium text-gray-300">
                            <i class="far fa-calendar-alt"></i> Período
                        </label>
                        <select name="time_frame" id="time_frame" 
                                class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none @error('time_frame') border-red-500 @enderror" required>
                            <option value="day" {{ old('time_frame') == 'day' ? 'selected' : '' }}>Hoje</option>
                            <option value="week" {{ old('time_frame') == 'week' ? 'selected' : '' }}>Semana</option>
                            <option value="month" {{ old('time_frame') == 'month' ? 'selected' : '' }}>Mês</option>
                        </select>
                        @error('time_frame')
                            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora de Início -->
                    <div class="mb-4">
                        <label for="start_time" class="block text-sm font-medium text-gray-300">
                            <i class="far fa-clock"></i> Hora de Início
                        </label>
                        <input type="time" name="start_time" id="start_time" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none @error('start_time') border-red-500 @enderror"
                               required>
                        @error('start_time')
                            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Hora de Término -->
                    <div class="mb-6">
                        <label for="end_time" class="block text-sm font-medium text-gray-300">
                            <i class="far fa-clock"></i> Hora de Término
                        </label>
                        <input type="time" name="end_time" id="end_time" 
                               class="mt-1 block w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-green-500 focus:outline-none @error('end_time') border-red-500 @enderror"
                               required>
                        @error('end_time')
                            <div class="text-sm text-red-500 mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botão Criar -->
                    <button type="submit" 
                            class="w-full py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition duration-300 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Criar Horários
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

