<?php

use App\Http\Controllers\Api\AgentController;
use App\Http\Controllers\Api\ServerController;
use App\Http\Middleware\AgentAuthentication;
use App\Http\Middleware\ServerAuthentication;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Agent registration (no authentication required)
Route::post('/agent/register', [AgentController::class, 'registerAnonymous']);

// Agent authentication (no middleware)
Route::post('/agent/auth', [AgentController::class, 'authenticate']);

// Agent routes (authenticated)
Route::middleware(AgentAuthentication::class)->prefix('agent')->group(function () {
    Route::get('/config', [AgentController::class, 'getConfig']);
    Route::post('/heartbeat', [AgentController::class, 'heartbeat']);
    Route::get('/tunnels', [AgentController::class, 'getTunnels']);
    Route::post('/tunnels', [AgentController::class, 'createTunnel']);
    Route::post('/create-login-token', [AgentController::class, 'createLoginToken']);
});

// Server routes (server API key auth)
Route::middleware(ServerAuthentication::class)->prefix('server')->group(function () {
    Route::get('/tunnels', [ServerController::class, 'getTunnels']);
    Route::post('/tunnel/{tunnel}/request', [ServerController::class, 'logRequest']);
});
