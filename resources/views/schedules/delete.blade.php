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
                <button class="w-full bg-red-600 py-2 rounded-lg hover:bg-red-700 delete-btn" 
                        data-form="delete-future-form" data-message="Tem certeza que deseja excluir todos os horários futuros?">
                    Excluir Todos os Horários Futuros
                </button>
                <form id="delete-future-form" method="POST" action="{{ route('schedules.delete.future') }}" class="hidden">
                    @csrf
                </form>

                @foreach($paginatedSchedules as $date => $schedulesGroup)
                    <div class="mt-4 bg-gray-800 p-4 rounded-lg">
                        <button class="toggle-schedules w-full text-left text-lg font-semibold text-white flex justify-between items-center">
                            <span>{{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>

                        <!-- Excluir todos os horários deste dia -->
                        <button class="w-full bg-red-500 text-white py-1 rounded-lg hover:bg-red-600 delete-btn mt-2"
                                data-form="delete-date-{{ $date }}" data-message="Tem certeza que deseja excluir todos os horários de {{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}?">
                            Excluir Todos os Horários de {{ \Carbon\Carbon::parse($date)->translatedFormat('d/m/Y') }}
                        </button>
                        <form id="delete-date-{{ $date }}" method="POST" action="{{ route('schedules.delete.byDate') }}" class="hidden">
                            @csrf
                            <input type="hidden" name="date" value="{{ $date }}">
                        </form>

                        <div class="schedule-list hidden mt-2">
                            @foreach($schedulesGroup as $schedule)
                                <div class="flex justify-between items-center bg-gray-700 p-2 rounded-lg mb-2">
                                    <span class="text-white">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                    <button class="bg-red-500 px-3 py-1 rounded hover:bg-red-600 delete-btn"
                                            data-form="delete-schedule-{{ $schedule->id }}" data-message="Tem certeza que deseja remover este horário?">
                                        Remover
                                    </button>
                                    <form id="delete-schedule-{{ $schedule->id }}" method="POST" action="{{ route('schedules.delete.single') }}" class="hidden">
                                        @csrf
                                        <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center">
        <div class="bg-gray-900 p-6 rounded-lg shadow-lg w-96 text-white">
            <h3 class="text-lg font-semibold mb-4">Confirmação</h3>
            <p id="confirmMessage"></p>
            <div class="mt-4 flex justify-end space-x-3">
                <button id="cancelBtn" class="px-4 py-2 bg-gray-600 rounded-lg hover:bg-gray-700">Cancelar</button>
                <button id="confirmBtn" class="px-4 py-2 bg-red-600 rounded-lg hover:bg-red-700">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Alternar exibição dos horários
        document.querySelectorAll('.toggle-schedules').forEach(button => {
            button.addEventListener('click', function () {
                let scheduleList = this.parentElement.querySelector('.schedule-list');
                if (scheduleList) {
                    scheduleList.classList.toggle('hidden');
                }
            });
        });

        // Modal de confirmação
        const modal = document.getElementById('confirmModal');
        const confirmMessage = document.getElementById('confirmMessage');
        let formToSubmit = null;

        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function () {
                confirmMessage.textContent = this.dataset.message;
                formToSubmit = document.getElementById(this.dataset.form);
                modal.classList.remove('hidden');
            });
        });

        // Cancelar ação
        document.getElementById('cancelBtn').addEventListener('click', function () {
            modal.classList.add('hidden');
            formToSubmit = null;
        });

        // Confirmar ação e submeter formulário
        document.getElementById('confirmBtn').addEventListener('click', function () {
            if (formToSubmit) {
                formToSubmit.submit();
            }
            modal.classList.add('hidden');
        });
    });
</script>

</x-app-layout>
