<?php

return [

    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        'hservicio_img' => [
            'driver' => 'local',
            'root' => public_path('hservicio_img'),
            'url' => env('APP_URL').'/hservicio_img',
            'visibility' => 'public',
            'throw' => false,
        ],

        'productos_img' => [
            'driver' => 'local',
            'root' => public_path('productos_img'),
            'url' => env('APP_URL').'/productos_img',
            'visibility' => 'public',
            'throw' => false,
        ],

        'productos_inventario' => [
            'driver' => 'local',
            'root' => public_path('productos_inventario'),
            'url' => env('APP_URL').'/productos_inventario',
            'visibility' => 'public',
            'throw' => false,
        ],

        'informes_clientes' => [
            'driver' => 'local',
            'root' => public_path('informes_clientes'),
            'url' => env('APP_URL').'/informes_clientes',
            'visibility' => 'public',
            'throw' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
        ],

    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
