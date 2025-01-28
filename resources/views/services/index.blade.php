<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Serviços') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <a href="{{ route('services.create') }}" class="px-4 py-2 bg-blue-500 text-white rounded-lg">Adicionar Serviço</a>
                    <table class="w-full mt-6 border">
                        <thead>
                            <tr class="bg-gray-200">
                                <th class="px-4 py-2">Foto</th>
                                <th class="px-4 py-2">Nome</th>
                                <th class="px-4 py-2">Duração</th>
                                <th class="px-4 py-2">Preço</th>
                                <th class="px-4 py-2">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($services as $service)
                                <tr>
                                    <td class="border px-4 py-2">
                                        @if($service->photo)
                                            <img src="{{ asset('storage/' . $service->photo) }}" alt="{{ $service->name }}" class="w-16 h-16 object-cover">
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2">{{ $service->name }}</td>
                                    <td class="border px-4 py-2">{{ $service->duration }} min</td>
                                    <td class="border px-4 py-2">R$ {{ number_format($service->price, 2, ',', '.') }}</td>
                                    <td class="border px-4 py-2">
                                        <a href="{{ route('services.edit', $service) }}" class="px-4 py-2 bg-yellow-500 text-white rounded-lg">Editar</a>
                                        <form method="POST" action="{{ route('services.destroy', $service) }}" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg">Excluir</button>
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
