<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        <!-- Scripts -->
        @routes
        @vite(['resources/css/app.css', 'resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
        {{-- Boton para cambiar modo --}}
        <div class="modo_oscuro w-full flex justify-between px-4 py-2 border-t border-gray-300 dark:border-gray-700">
            <div class="info h-8 flex items-center w-36 text-gray-700 dark:text-gray-400 gap-4 overflow-hidden">
                <i class='bx bx-moon h-6 w-6 text-2xl' ></i>
                <span class="font-normal text-sm">Modo Oscuro</span>
            </div>
            <div id="swith_modo_oscuro" class="w-14 h-8 flex items-center justify-center cursor-pointer">
                <div class="base_swith w-9 h-5 rounded-full bg-slate-700 relative flex items-center">
                    <div class="circulo_swith duration-300 w-4 h-4 rounded-full bg-gray-100 absolute left-0.5"></div>
                </div>
            </div>
        </div>
        {{-- Boton para cambiar modo --}}
    </body>
</html>
