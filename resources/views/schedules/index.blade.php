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
                    <!-- Se não houver horários, exibe uma mensagem -->
                    @if($schedules->isEmpty())
                        <p class="text-gray-600">Você não tem horários agendados.</p>
                    @else
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            @foreach($schedules as $schedule)
                                <div class="bg-blue-100 p-4 rounded-lg shadow-md">
                                    <div class="font-semibold text-xl text-gray-800">
                                        {{ $schedule->date->format('d/m/Y') }}
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
