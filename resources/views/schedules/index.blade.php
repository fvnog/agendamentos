<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Horários') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Filtro por data -->
                    @php
                        $filterDate = $filterDate ?? now()->format('Y-m-d');
                    @endphp

                    <form method="GET" action="{{ route('schedules.index') }}" class="mb-6">
                        <label for="date" class="block text-gray-700 font-medium mb-2">Escolha uma data:</label>
                        <div class="flex items-center gap-4">
                            <input 
                                type="date" 
                                id="date" 
                                name="date" 
                                value="{{ $filterDate }}" 
                                class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <button 
                                type="submit" 
                                class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600">
                                Filtrar
                            </button>
                        </div>
                    </form>

                    <!-- Exibe mensagem caso não haja horários -->
                    @if($schedules->isEmpty())
                        <p class="text-gray-600">Nenhum horário encontrado para a data selecionada.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($schedules as $schedule)
                                <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                                    <div class="font-semibold text-xl text-gray-800">
                                        {{ \Carbon\Carbon::parse($schedule->date)->format('d/m/Y') }}
                                    </div>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p><strong>Início:</strong> {{ $schedule->start_time }}</p>
                                        <p><strong>Fim:</strong> {{ $schedule->end_time }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
