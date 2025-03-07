<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            <i class="fas fa-cut"></i> Serviços Cadastrados
        </h2>
    </x-slot>

    <div class="py-6 min-h-screen">
        <div class="max-w mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-900 shadow-lg rounded-lg p-6 text-white">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold">Lista de Serviços</h3>
                    <a href="{{ route('services.create') }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-300 flex items-center gap-2">
                        <i class="fas fa-plus"></i> Adicionar Serviço
                    </a>
                </div>

                <!-- Tabela de Serviços -->
                <div class="overflow-x-auto">
                    <table class="w-full border border-gray-700 text-white">
                        <thead>
                            <tr class="bg-gray-800 text-gray-300">
                                <th class="px-4 py-3 text-left">Foto</th>
                                <th class="px-4 py-3 text-left">Nome</th>
                                <th class="px-4 py-3 text-left"><i class="far fa-clock"></i> Duração</th>
                                <th class="px-4 py-3 text-left"><i class="fas fa-dollar-sign"></i> Preço</th>
                                <th class="px-4 py-3 text-left"><i class="fas fa-cogs"></i> Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr class="border-b border-gray-700 hover:bg-gray-800 transition">
                                    <td class="px-4 py-3">
                                        @if($service->photo)
                                            <img src="{{ asset('storage/' . $service->photo) }}" 
                                                 alt="{{ $service->name }}" 
                                                 class="w-16 h-16 object-cover rounded-full border border-gray-700">
                                        @else
                                            <i class="fas fa-image text-gray-500 text-2xl"></i>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $service->name }}</td>
                                    <td class="px-4 py-3">{{ $service->duration }} min</td>
                                    <td class="px-4 py-3">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                                    <td class="px-4 py-3 flex gap-2">
                                        <a href="{{ route('services.edit', $service) }}" 
                                           class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition flex items-center gap-2">
                                            <i class="fas fa-edit"></i> Editar
                                        </a>
                                        <form method="POST" action="{{ route('services.destroy', $service) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition flex items-center gap-2">
                                                <i class="fas fa-trash-alt"></i> Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

