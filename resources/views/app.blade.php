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
    
    <body class="antialiased bg-white h-full">
        @inertia
        <div id="app" class="overflow-hidden"></div>
        {{-- <div id="contenido"></div> --}}
        
        {{-- <div class="min-h-[calc(100%-148px)] overflow-hidden"> --}}
        <div class="overflow-hidden">
        </div>
        <footer class="ml-auto bg-[#F7BE15] dark:bg-[#F7BE15] border-t border-gray-300 dark:border-gray-700">
            <div class="mx-auto">
                <div class="h-20 flex items-center justify-center"><h3 class="text-gray-900 dark:text-white text-center text-sm w-64 md:w-full">Todos los derechos reservados. Diseñado y desarrollado por <b>Industrias Balinsa.</b></h3></div>
                <!-- <div class="h-20 flex items-center justify-center"><h3 class="text-gray-900 dark:text-gray-300 text-center text-sm w-64 md:w-full">Todos los derechos reservados. Diseñado y desarrollado por <b>Leonardo Garcia Jimenez</b></h3></div> -->
            </div>
        </footer>
    </body>

    {{-- <body class="font-sans antialiased box-border">
        @inertia
        
    </body> --}}
</html>
