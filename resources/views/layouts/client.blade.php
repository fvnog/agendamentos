<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Horários</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-blue-500 text-white py-4 shadow-md">
            <div class="container mx-auto px-4">
                <h1 class="text-xl font-semibold">Agendar Horários</h1>
            </div>
        </header>

        <main class="flex-grow container mx-auto px-4 py-8">
            {{ $slot }}
        </main>

        <footer class="bg-gray-800 text-white py-4 mt-8">
            <div class="container mx-auto px-4 text-center">
                <p>&copy; {{ date('Y') }} - Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>
</body>
</html>
