<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Horários Disponíveis</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <main class="py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">Horários Disponíveis</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($schedules as $schedule)
                        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                            <div class="p-5">
                                <h3 class="text-xl font-semibold text-indigo-600">{{ $schedule->date->format('d M, Y') }}</h3>
                                <p class="text-gray-700 mt-2">Horário: <span class="font-semibold">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span></p>
                                <p class="text-gray-500 mt-2">Barbeiro: {{ $schedule->user->name }}</p>

                                <p class="text-gray-500 mt-2">
                                    Status: 
                                    <span class="{{ $schedule->is_booked ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $schedule->is_booked ? 'Reservado' : 'Disponível' }}
                                    </span>
                                </p>

                                @if(!$schedule->is_booked)
                                    <form action="{{ route('schedule.book', $schedule->id) }}" method="POST" class="mt-4">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition duration-200">
                                            Agendar
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>
</body>
</html>
