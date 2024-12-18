<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cadastrar Horário de Almoço') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('lunch-break.store') }}">
                        @csrf

                        <!-- Hora de Início -->
                        <div class="mb-4">
                            <label for="start_time" class="block text-sm font-medium text-gray-700">Hora de Início do Almoço</label>
                            <input type="time" name="start_time" id="start_time" class="mt-1 block w-full @error('start_time') border-red-500 @enderror" 
                                value="{{ old('start_time', $lunchBreak ? $lunchBreak->start_time : '') }}" required>
                            @error('start_time')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Hora de Término -->
                        <div class="mb-4">
                            <label for="end_time" class="block text-sm font-medium text-gray-700">Hora de Término do Almoço</label>
                            <input type="time" name="end_time" id="end_time" class="mt-1 block w-full @error('end_time') border-red-500 @enderror" 
                                value="{{ old('end_time', $lunchBreak ? $lunchBreak->end_time : '') }}" required>
                            @error('end_time')
                                <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        @if ($lunchBreak)
                            <p class="text-green-600 mb-4">Você já tem um horário de almoço cadastrado!</p>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Atualizar Horário de Almoço</button>
                        @else
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Cadastrar Horário de Almoço</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
