<?php

use Livewire\Volt\Volt;

// Landing page (public)
Route::get('/', function () {
    return view('landing');
})->name('home');

// Public routes
Volt::route('/login', 'auth.login')->name('login');
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');

// Protected routes under /panel
Route::middleware(['auth'])->prefix('panel')->group(function () {
    Volt::route('/', 'dashboard.index')->name('dashboard');
    Route::get('/agents/claim', [\App\Http\Controllers\Panel\AgentClaimController::class, 'claim'])->name('agents.claim');
    
    Volt::route('/tunnels', 'tunnels.index')->name('tunnels.index');
    Volt::route('/tunnels/create', 'tunnels.create')->name('tunnels.create');
    Volt::route('/tunnels/{tunnel}', 'tunnels.show')->name('tunnels.show');
    Volt::route('/agents', 'agents.index')->name('agents.index');
    Volt::route('/activity', 'activity.index')->name('activity.index');
    
    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});
