<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-white leading-tight flex items-center gap-2">
            <i class="fas fa-chart-line"></i> Painel Administrativo
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto px-6 lg:px-8 space-y-6">

            <!-- Card de Boas-vindas -->
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white flex items-center gap-4">
                <i class="fas fa-user-shield text-4xl text-yellow-400"></i>
                <div>
                    <h3 class="text-lg font-semibold">Bem-vindo, {{ auth()->user()->name }}!</h3>
                    <p class="text-gray-400">Você está logado como administrador.</p>
                </div>
            </div>

            <!-- Grid de Métricas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Total de Usuários -->
                <div class="bg-blue-600 shadow-lg rounded-lg p-6 text-white flex items-center gap-4 hover:bg-blue-700 transition">
                    <i class="fas fa-users text-4xl"></i>
                    <div>
                        <h4 class="text-sm font-medium">Total de Usuários</h4>
                        <p class="mt-2 text-3xl font-bold">{{ $totalUsers }}</p>
                    </div>
                </div>

                <!-- Agendamentos Hoje -->
                <div class="bg-green-600 shadow-lg rounded-lg p-6 text-white flex items-center gap-4 hover:bg-green-700 transition">
                    <i class="fas fa-calendar-check text-4xl"></i>
                    <div>
                        <h4 class="text-sm font-medium">Agendamentos Hoje</h4>
                        <p class="mt-2 text-3xl font-bold">{{ $appointmentsToday }}</p>
                    </div>
                </div>

                <!-- Horários Livres de Hoje -->
                <div class="bg-red-600 shadow-lg rounded-lg p-6 text-white flex items-center gap-4 hover:bg-red-700 transition">
                    <i class="fas fa-clock text-4xl"></i>
                    <div>
                        <h4 class="text-sm font-medium">Horários Livres</h4>
                        <p class="mt-2 text-3xl font-bold">{{ $pendingAppointments }}</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
