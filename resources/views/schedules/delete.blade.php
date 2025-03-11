<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-calendar-times"></i> Excluir Horários
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-trash"></i> Remover Horários</h3>

                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-600 text-white rounded-md">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Filtro de data e Reset -->
                <form method="GET" action="{{ route('schedules.delete') }}" class="mb-4 flex items-center space-x-3">
                    <input type="date" name="date" value="{{ request('date') }}" 
                           class="px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Filtrar
                    </button>
                    <a href="{{ route('schedules.delete') }}" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        Resetar
                    </a>
                </form>

                <!-- Excluir todos os horários futuros -->
                <form method="POST" action="{{ route('schedules.delete.future') }}" class="mb-4">
                    @csrf
                    <button type="submit" class="w-full bg-red-600 py-2 rounded-lg hover:bg-red-700">
                        Excluir Todos os Horários Futuros
                    </button>
                </form>

                @foreach($paginatedSchedules as $date => $schedulesGroup)
                    <div class="mt-4 bg-gray-800 p-4 rounded-lg">
                        <button class="toggle-schedules w-full text-left text-lg font-semibold text-white flex justify-between items-center">
                            <span>{{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <!-- Excluir todos os horários deste dia -->
                        <form method="POST" action="{{ route('schedules.delete.byDate') }}" class="mt-2">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                            <button type="submit" class="w-full bg-red-500 text-white py-1 rounded-lg hover:bg-red-600">
                                Excluir Todos os Horários de {{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}
                            </button>
                        </form>

                        <div class="schedule-list hidden mt-2">
                            @foreach($schedulesGroup as $schedule)
                                <div class="flex justify-between items-center bg-gray-700 p-2 rounded-lg mb-2">
                                    <span class="text-white">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                    <form method="POST" action="{{ route('schedules.delete.single') }}">
                                        @csrf
                                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                        <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
                                            Remover
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-schedules').forEach(button => {
                button.addEventListener('click', function () {
                    this.nextElementSibling.nextElementSibling.classList.toggle('hidden');
                });
            });
        });
    </script>
</x-app-layout>
