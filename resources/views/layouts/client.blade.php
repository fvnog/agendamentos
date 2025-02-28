<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Horários</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-black bg-cover bg-center bg-no-repeat" style="background-image: url('{{ asset('storage/img/bg.png') }}');">
    <div class="min-h-screen flex flex-col">
        <main class="flex-grow container mx-auto px-4 py-8">
            {{ $slot }}
        </main>
    </div>
</body>

</html>
