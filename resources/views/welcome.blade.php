<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Barbearia XYZ') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Cabeçalho com imagem de fundo e título -->
            <header class="relative bg-cover bg-center h-96" style="background-image: url('/images/barbershop-banner.jpg');">
                <div class="absolute inset-0 bg-gray-900 bg-opacity-50"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-center text-white">
                    <h1 class="text-5xl font-extrabold">Bem-vindo à Barbearia XYZ</h1>
                    <p class="mt-4 text-xl">Corte de cabelo, barba e muito mais. Agende seu horário agora!</p>
                    <a href="{{ route('schedule.index') }}" class="mt-6 px-6 py-3 bg-indigo-600 text-white text-lg font-medium rounded-md hover:bg-indigo-700 transition duration-200">Agendar Agora</a>
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <!-- Serviços -->
                <section class="py-16 bg-white">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-gray-800 text-center mb-8">Nossos Serviços</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="bg-gray-100 p-6 rounded-lg shadow-md text-center">
                                <h3 class="text-xl font-semibold text-indigo-600">Corte de Cabelo</h3>
                                <p class="mt-4 text-gray-600">Um corte de cabelo moderno e estiloso para todos os gostos.</p>
                            </div>
                            <div class="bg-gray-100 p-6 rounded-lg shadow-md text-center">
                                <h3 class="text-xl font-semibold text-indigo-600">Barba</h3>
                                <p class="mt-4 text-gray-600">Deixe sua barba bem cuidada com nossos especialistas.</p>
                            </div>
                            <div class="bg-gray-100 p-6 rounded-lg shadow-md text-center">
                                <h3 class="text-xl font-semibold text-indigo-600">Pacote Completo</h3>
                                <p class="mt-4 text-gray-600">Corte de cabelo e barba para um look impecável.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Horários Disponíveis -->
                <section class="py-16 bg-gray-50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 class="text-3xl font-semibold text-gray-800 text-center mb-8">Horários Disponíveis</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($schedules as $schedule)
                                <div class="bg-white shadow-lg rounded-lg overflow-hidden">
                                    <div class="p-5">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-xl font-semibold text-indigo-600">
                                                {{ $schedule->date->format('d M, Y') }}
                                            </h3>
                                            <span class="text-gray-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3M16 7V3M3 11h18M5 20h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v11a2 2 0 002 2z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <p class="mt-2 text-gray-700">Horário: 
                                            <span class="font-semibold">{{ $schedule->start_time }} - {{ $schedule->end_time }}</span>
                                        </p>

                                        <!-- Botão de Agendamento -->
                                        <form action="{{ route('schedule.book', $schedule->id) }}" method="POST" class="mt-4">
                                            @csrf
                                            <button type="submit" class="w-full px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition duration-200">
                                                Agendar
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center">
                                    <p class="text-gray-600 text-lg font-medium">Nenhum horário disponível no momento. Por favor, volte mais tarde.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
