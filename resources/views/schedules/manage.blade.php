<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-calendar-plus"></i> Gerenciamento de Horários
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <i class="fas fa-clock"></i> Configurar Horários
                </h3>

                <!-- Tabs -->
                <ul class="flex space-x-4 border-b mb-6">
                    <li><button class="tab-btn px-4 py-2 bg-gray-700 rounded-t-md hover:bg-green-500 transition" data-tab="create">Criar</button></li>
                    <li><button class="tab-btn px-4 py-2 bg-gray-700 rounded-t-md hover:bg-red-500 transition" data-tab="delete">Excluir</button></li>
                    <li><button class="tab-btn px-4 py-2 bg-gray-700 rounded-t-md hover:bg-blue-500 transition" data-tab="fixed">Fixos</button></li>
                </ul>

                <!-- Criar Horários -->
                <div id="tab-create" class="tab-content">
                    <form method="POST" action="{{ route('schedules.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="fas fa-stopwatch"></i> Intervalo (min)</label>
                            <input type="number" name="interval" value="30" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="far fa-clock"></i> Hora de Início</label>
                            <input type="time" name="start_time" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="far fa-clock"></i> Hora de Término</label>
                            <input type="time" name="end_time" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="far fa-calendar-alt"></i> Criar para</label>
                            <select name="time_frame" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                                <option value="day">Hoje</option>
                                <option value="week">Semana</option>
                                <option value="month">Mês</option>
                            </select>
                        </div>

                        <button type="submit" class="col-span-2 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-save"></i> Criar Horários
                        </button>
                    </form>
                </div>

                <!-- Excluir Horários -->
                <div id="tab-delete" class="tab-content hidden">
                    <h4 class="text-lg font-semibold mt-6"><i class="fas fa-list"></i> Horários Criados</h4>
                    
                    @foreach($paginatedSchedules as $date => $schedulesGroup)
                        <div class="mt-4 bg-gray-800 p-4 rounded-lg">
                            <button class="toggle-schedules w-full text-left text-lg font-semibold text-white flex justify-between items-center">
                                <span><i class="far fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>

                            <div class="schedule-list hidden mt-2">
                                @if(!empty($schedulesGroup) && is_iterable($schedulesGroup))
                                    @foreach($schedulesGroup as $schedule)
                                        <div class="flex justify-between items-center bg-gray-700 p-2 rounded-lg mb-2">
                                            <span class="text-white">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                            <form method="POST" action="{{ route('schedules.delete.single') }}">
                                                @csrf
                                                <input type="hidden" name="schedule_id" value="{{ $schedule->id }}">
                                                <button type="submit" class="bg-red-500 px-3 py-1 rounded hover:bg-red-600">
                                                    <i class="fas fa-trash"></i> Remover
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-gray-400">Nenhum horário encontrado.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-6">
                        {{ $paginatedSchedules->links() }}
                    </div>
                </div>

                <!-- Criar Horários Fixos -->
                <div id="tab-fixed" class="tab-content hidden">
                    <form method="POST" action="{{ route('schedules.fixed.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="fas fa-user"></i> Cliente</label>
                            <select name="client_id" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="fas fa-user-tie"></i> Barbeiro</label>
                            <select name="barber_id" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                                @foreach($barbers as $barber)
                                    <option value="{{ $barber->id }}">{{ $barber->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-300"><i class="far fa-calendar-alt"></i> Dia da Semana</label>
                            <select name="weekday" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                                <option value="0">Domingo</option>
                                <option value="1">Segunda-feira</option>
                                <option value="2">Terça-feira</option>
                                <option value="3">Quarta-feira</option>
                                <option value="4">Quinta-feira</option>
                                <option value="5">Sexta-feira</option>
                                <option value="6">Sábado</option>
                            </select>
                        </div>

                        <button type="submit" class="col-span-2 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-calendar-check"></i> Criar Horário Fixo
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.tab-btn').forEach(button => {
            button.addEventListener('click', function () {
                document.querySelectorAll('.tab-content').forEach(content => content.classList.add('hidden'));
                document.getElementById('tab-' + this.dataset.tab).classList.remove('hidden');
            });
        });
    </script>
</x-app-layout>
