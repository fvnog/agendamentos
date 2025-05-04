<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-calendar-plus"></i> Criar Horários
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-clock"></i> Definir Horários</h3>

                <form method="POST" action="{{ route('schedules.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf

                    <!-- 🔹 Seletor de Tipo de Criação -->
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Criar para</label>
                        <select name="schedule_type" id="schedule_type" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                            <option value="today">Hoje</option>
                            <option value="day">Dia Específico</option>
                            <option value="week">Toda Semana</option>
                            <option value="month">Todo Mês</option>
                        </select>
                    </div>

                    <!-- 🔹 Campo de Data (aparece apenas para "Dia Específico") -->
                    <div id="date_field" class="hidden">
                        <label class="block text-sm font-medium text-gray-300">Data Específica</label>
                        <input type="date" name="specific_date" id="specific_date" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-300">Intervalo (min)</label>
                        <input type="number" name="interval" value="30" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Hora de Início</label>
                        <input type="time" name="start_time" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Hora de Término</label>
                        <input type="time" name="end_time" class="w-full px-4 py-2 bg-gray-800 border border-gray-600 rounded-lg text-white">
                    </div>
                    
                    <button type="submit" class="col-span-2 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Criar Horários
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('schedule_type').addEventListener('change', function () {
            let dateField = document.getElementById('date_field');
            if (this.value === 'day') {
                dateField.classList.remove('hidden');
            } else {
                dateField.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
