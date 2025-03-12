<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-money-bill-wave"></i> Meu Relatório Financeiro
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <h3 class="text-lg font-semibold mb-6"><i class="fas fa-chart-line"></i> Resumo Financeiro</h3>

                <!-- Cards de Estatísticas -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-gray-800 shadow-md rounded-lg p-6 text-center">
                        <h4 class="text-lg font-bold text-white"><i class="fas fa-coins"></i> Total Geral</h4>
                        <p class="text-green-400 text-2xl font-bold mt-2">R$ {{ number_format($totalGanhos, 2, ',', '.') }}</p>
                    </div>

                    <div class="bg-gray-800 shadow-md rounded-lg p-6 text-center">
                        <h4 class="text-lg font-bold text-white"><i class="fas fa-calendar-alt"></i> Este Mês</h4>
                        <p class="text-yellow-400 text-2xl font-bold mt-2">R$ {{ number_format($ganhosMes, 2, ',', '.') }}</p>
                    </div>

                    <div class="bg-gray-800 shadow-md rounded-lg p-6 text-center">
                        <h4 class="text-lg font-bold text-white"><i class="fas fa-calendar-week"></i> Esta Semana</h4>
                        <p class="text-blue-400 text-2xl font-bold mt-2">R$ {{ number_format($ganhosSemana, 2, ',', '.') }}</p>
                    </div>

                    <div class="bg-gray-800 shadow-md rounded-lg p-6 text-center">
                        <h4 class="text-lg font-bold text-white"><i class="fas fa-calendar-day"></i> Hoje</h4>
                        <p class="text-red-400 text-2xl font-bold mt-2">R$ {{ number_format($ganhosDia, 2, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Tabela de Pagamentos -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold mb-4"><i class="fas fa-list"></i> Histórico de Pagamentos</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full border border-gray-700 text-white">
                            <thead>
                                <tr class="bg-gray-800 text-gray-300">
                                    <th class="px-4 py-3 text-left">Cliente</th>
                                    <th class="px-4 py-3 text-left">Valor</th>
                                    <th class="px-4 py-3 text-left">Serviços</th>
                                    <th class="px-4 py-3 text-left">Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagamentos as $pagamento)
                                    <tr class="border-b border-gray-700 hover:bg-gray-800 transition">
                                    <td class="px-4 py-3">
    {{ $pagamento->user->name ?? ($pagamento->schedule->client_name ?? 'Não cadastrado') }}
</td>

                                        <td class="px-4 py-3">R$ {{ number_format($pagamento->amount, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3">
@php
    $services = is_string($pagamento->services) ? json_decode($pagamento->services, true) : $pagamento->services;
@endphp

                                            @if($services)
                                                <ul class="list-disc ml-4 text-sm text-gray-400">
                                                    @foreach($services as $service)
                                                        <li>{{ $service['name'] }}</li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                <span class="text-gray-400">Nenhum serviço</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($pagamento->created_at)->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Se não houver pagamentos -->
                @if($pagamentos->isEmpty())
                    <p class="text-gray-400 text-center mt-6">Nenhum pagamento registrado.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
