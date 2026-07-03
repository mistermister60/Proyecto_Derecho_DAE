<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Botón de instalación
    |--------------------------------------------------------------------------
    */
    'install-button' => true,

    /*
    |--------------------------------------------------------------------------
    | Configuración del Manifiesto PWA
    |--------------------------------------------------------------------------
    | Ejecutar: php artisan erag:update-manifest
    */
    'manifest' => [
        'name' => 'Procurador Legal - Sistema de Gestión',
        'short_name' => 'Procurador Legal',
        'background_color' => '#ffffff',
        'display' => 'standalone',
        'description' => 'Sistema de gestión de casos y procuradores para el Despacho de Abogados DAE.',
        'theme_color' => '#6777ef',
        'start_url' => '/',
        'scope' => '/',
        'orientation' => 'portrait-primary',
        'lang' => 'es-HN',
        'categories' => ['business', 'productivity', 'legal'],
        'icons' => [
            ['src' => '/logo.svg', 'sizes' => '512x512', 'type' => 'image/svg+xml', 'purpose' => 'any maskable'],
            ['src' => '/logo.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any maskable'],
        ],
        'shortcuts' => [
            ['name' => 'Dashboard', 'url' => '/dashboard', 'description' => 'Panel principal'],
            ['name' => 'Casos', 'url' => '/casos', 'description' => 'Gestión de expedientes'],
            ['name' => 'Clientes', 'url' => '/clientes', 'description' => 'Directorio de clientes'],
            ['name' => 'Agenda', 'url' => '/agenda', 'description' => 'Calendario de audiencias'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Notificaciones Push (VAPID)
    |--------------------------------------------------------------------------
    | Generar claves: https://www.attheminute.com/vapid-key-generator/
    | O con web-push: npx web-push generate-vapid-keys
    */
    'vapid' => [
        'subject' => env('VAPID_SUBJECT', 'mailto:sistema@derechodae.com'),
        'public_key' => env('VAPID_PUBLIC_KEY', ''),
        'private_key' => env('VAPID_PRIVATE_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Cache y Offline
    |--------------------------------------------------------------------------
    */
    'offline' => [
        'enabled' => true,
        'strategy' => 'network-first', // network-first | cache-first | stale-while-revalidate
        'cache_version' => 'v1',
        'max_age_days' => 7,
        'routes_to_cache' => [
            '/dashboard',
            '/casos',
            '/clientes',
            '/agenda',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    */
    'debug' => env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Livewire Integration
    |--------------------------------------------------------------------------
    */
    'livewire-app' => false,
];
