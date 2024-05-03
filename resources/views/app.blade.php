<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" type="image/x-icon" href="{{ asset('img/balinsa.png') }}">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.6/b-3.0.2/b-html5-3.0.2/r-3.0.2/datatables.min.css" rel="stylesheet">

        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
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
        <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.6/b-3.0.2/b-html5-3.0.2/r-3.0.2/datatables.min.js"></script>
    </body>

    {{-- <body class="font-sans antialiased box-border">
        @inertia
        
    </body> --}}
</html>
