<?php

use Livewire\Volt\Volt;

// Public routes
Volt::route('/login', 'auth.login')->name('login');
Route::get('/register', \App\Livewire\Auth\Register::class)->name('register');

// Protected routes
Route::middleware(['auth'])->group(function () {
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
        return redirect('/login');
    })->name('logout');
});
