<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Adicionando FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/js/all.min.js"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        /* Fundo da logo em toda a tela */
        .bg-logo {
            background: url("{{ asset('storage/img/gs2.png') }}") no-repeat center center;
            background-size: 40%;
            opacity: 0.08; /* Opacidade da logo */
            position: fixed;
            top: 0;
            left: 64px;
            width: 100%;
            height: 100%;
            z-index: -1; /* Fica no fundo de tudo */
        }
    </style>
</head>
<body class="font-sans antialiased bg-black text-white relative">

    <!-- Fundo da Logo -->
    <div class="bg-logo"></div>

    <!-- Container principal que ocupa toda a tela -->
    <div class="flex h-screen relative">

        <!-- Sidebar (Navbar Lateral Esquerda) -->
        <aside class="w-64 bg-gray-900 h-full fixed">
            @include('layouts.navigation') 
        </aside>

        <!-- Conteúdo Principal -->
        <div class="flex-grow ml-64 relative">

            <!-- Conteúdo -->
            <main class="relative z-10">
                {{ $slot }}
            </main>

        </div>

    </div>

</body>
</html>
