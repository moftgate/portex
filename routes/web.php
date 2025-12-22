<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Panel\AgentClaimController;
use App\Livewire\Auth\Register;
use Livewire\Volt\Volt;

// Landing page (public)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Public routes
Volt::route('/login', 'auth.login')->name('login');
Route::get('/register', Register::class)->name('register');
Route::get('/auth/magic/{token}', [MagicLinkController::class, 'login'])->name('auth.magic');
//Route::get('/panel/agents/claim', [AgentClaimController::class, 'claim'])->name('agents.claim');

// Protected routes under /panel
Route::middleware(['auth'])->prefix('panel')->group(function () {
    Volt::route('/', 'dashboard.index')->name('dashboard');

    Volt::route('/tunnels', 'tunnels.index')->name('tunnels.index');
    Volt::route('/tunnels/create', 'tunnels.create')->name('tunnels.create');
    Volt::route('/tunnels/{tunnel}', 'tunnels.show')->name('tunnels.show');
    Volt::route('/agents', 'agents.index')->name('agents.index');
    Volt::route('/activity', 'activity.index')->name('activity.index');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
