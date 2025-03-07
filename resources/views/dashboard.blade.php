<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Painel Administrativo
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card de Boas-vindas -->
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-4">Bem-vindo, {{ auth()->user()->name }}!</h3>
                <p class="text-gray-300">Você está logado como administrador.</p>
            </div>

            <!-- Métricas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-gray-800 shadow-lg rounded-lg p-6 text-white">
                    <h4 class="text-sm font-medium text-gray-400">Total de Usuários</h4>
                    <p class="mt-2 text-3xl font-bold">{{ $totalUsers }}</p>
                </div>
                <div class="bg-gray-800 shadow-lg rounded-lg p-6 text-white">
                    <h4 class="text-sm font-medium text-gray-400">Agendamentos Hoje</h4>
                    <p class="mt-2 text-3xl font-bold">{{ $appointmentsToday }}</p>
                </div>
                <div class="bg-gray-800 shadow-lg rounded-lg p-6 text-white">
                    <h4 class="text-sm font-medium text-gray-400">Horários Livres de Hoje</h4>
                    <p class="mt-2 text-3xl font-bold">{{ $pendingAppointments }}</p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
