<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar Horários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('schedules.store') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="interval" class="block text-sm font-medium text-gray-700">Intervalo (em minutos)</label>
                            <input type="number" name="interval" id="interval" class="mt-1 block w-full @error('interval') border-red-500 @enderror" value="{{ old('interval', 30) }}" required>
                            @error('interval')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="time_frame" class="block text-sm font-medium text-gray-700">Período</label>
                            <select name="time_frame" id="time_frame" class="mt-1 block w-full @error('time_frame') border-red-500 @enderror" required>
                                <option value="day" {{ old('time_frame') == 'day' ? 'selected' : '' }}>Hoje</option>
                                <option value="week" {{ old('time_frame') == 'week' ? 'selected' : '' }}>Semana</option>
                                <option value="month" {{ old('time_frame') == 'month' ? 'selected' : '' }}>Mês</option>
                            </select>
                            @error('time_frame')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Hora de Início</label>
                            <input type="time" name="start_time" id="start_time" class="mt-1 block w-full @error('start_time') border-red-500 @enderror" required>
                            @error('start_time')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700">Hora de Término</label>
                            <input type="time" name="end_time" id="end_time" class="mt-1 block w-full @error('end_time') border-red-500 @enderror" required>
                            @error('end_time')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Criar Horários</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
