<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Horários</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body class="bg-black bg-center bg-no-repeat"
    style="background-image: url('{{ asset('storage/img/bg.png') }}'); background-size: contain;">

    <!-- Navbar -->
    <nav class="bg-gray-900 shadow-md py-4 fixed top-0 w-full z-50">
        <div class="container mx-auto flex justify-between items-center px-6">

            <!-- Logo -->
            <a href="/" class="text-white text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-cut"></i> GS Barbearia
            </a>

            <!-- Botão Mobile -->
            <button id="mobile-menu-button" class="text-white text-2xl md:hidden focus:outline-none">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Menu (Desktop & Mobile) -->
            <div id="menu"
                class="hidden md:flex flex-col md:flex-row items-center gap-6 absolute md:static top-16 left-0 w-full md:w-auto bg-gray-900 md:bg-transparent p-6 md:p-0 md:shadow-none shadow-md">
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('dashboard') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition w-full md:w-auto text-center">
                            <i class="fas fa-cog"></i> Painel Admin
                        </a>
                    @endif

                    <span class="text-white text-lg text-center w-full md:w-auto">{{ auth()->user()->name }}</span>

                    <form method="POST" action="{{ route('logout') }}" class="w-full md:w-auto">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition w-full md:w-auto">
                            <i class="fas fa-sign-out-alt"></i> Sair
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                        class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition w-full md:w-auto text-center">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </a>
                @endauth
            </div>

        </div>
    </nav>

    <!-- Adicionando padding no conteúdo para não ficar coberto pela navbar fixa -->
    <div class="min-h-screen flex flex-col pt-20">
        <main class="flex-grow container mx-auto px-4 py-8">
            {{ $slot }}
        </main>
    </div>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            document.getElementById('menu').classList.toggle('hidden');
        });
    </script>

</body>

</html>