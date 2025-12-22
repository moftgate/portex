<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Portex Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Portex tunnel service
    |
    */

    // Base domain for tunnels (e.g., portex.io)
    'tunnel_domain' => env('PORTEX_TUNNEL_DOMAIN', 'portex.local'),

    // Go server URL for backend communication
    'server_url' => env('PORTEX_SERVER_URL', 'http://localhost:8080'),

    // WebSocket URL for agent connections
    'websocket_url' => env('PORTEX_WEBSOCKET_URL', 'ws://localhost:8080/ws'),

    // Server API key for authenticating Go server requests
    'server_api_key' => env('PORTEX_SERVER_API_KEY', 'server_' . bin2hex(random_bytes(16))),
];
