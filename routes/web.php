<?php

use App\Http\Controllers\Auth\MagicLinkController;
use App\Http\Controllers\Panel\AgentClaimController;
use Livewire\Volt\Volt;

// Landing page (public)
Route::get('/', function () {
    return view('landing');
})->name('home');

Route::prefix('solutions')->group(function () {
    Route::view('/webhooks', 'solutions.webhooks')->name('solutions.webhooks');
    Route::view('/mobile', 'solutions.mobile')->name('solutions.mobile');
    Route::view('/static', 'solutions.static')->name('solutions.static');
    Route::view('/ci-cd', 'solutions.ci-cd')->name('solutions.ci-cd');
    Route::view('/api-dev', 'solutions.api-dev')->name('solutions.api-dev');
    Route::view('/remote-access', 'solutions.remote-access')->name('solutions.remote-access');
});

Route::prefix('docs')->group(function () {
    Route::view('/', 'docs.index')->name('docs.index');
    Route::view('/installation', 'docs.installation')->name('docs.installation');
    Route::view('/uninstall', 'docs.uninstall')->name('docs.uninstall');
    Route::view('/commands', 'docs.commands')->name('docs.commands');
    Route::view('/security', 'docs.security')->name('docs.security');
    Route::view('/dashboard', 'docs.dashboard')->name('docs.dashboard');
    Route::view('/architecture', 'docs.architecture')->name('docs.architecture');
    Route::view('/use-cases', 'docs.use-cases')->name('docs.use-cases');
    Route::view('/troubleshooting', 'docs.troubleshooting')->name('docs.troubleshooting');
    Route::view('/roadmap', 'docs.roadmap')->name('docs.roadmap');
    Route::view('/changelog', 'docs.changelog')->name('docs.changelog');
    Route::view('/pricing', 'docs.pricing')->name('docs.pricing');
});
// Main docs redirect to index
Route::get('/docs-old', fn() => redirect()->route('docs.index'))->name('docs');

// Public routes
Volt::route('/login', 'auth.login')->name('login');
Volt::route('/tunnels/pin/{tunnel}', 'tunnels.pin-entry')->name('tunnels.pin');
Route::get('/auth/magic/{token}', [MagicLinkController::class, 'login'])->name('auth.magic');
//Route::get('/panel/agents/claim', [AgentClaimController::class, 'claim'])->name('agents.claim');

// Protected routes under /panel
Route::middleware(['auth'])->prefix('panel')->group(function () {
    Volt::route('/', 'dashboard.index')->name('dashboard');

    Volt::route('/tunnels', 'tunnels.index')->name('tunnels.index');
    //Volt::route('/tunnels/create', 'tunnels.create')->name('tunnels.create');
    Volt::route('/tunnels/{tunnel}', 'tunnels.show')->name('tunnels.show');
    Volt::route('/agents', 'agents.index')->name('agents.index');
    Volt::route('/activity', 'activity.index')->name('activity.index');
    //Volt::route('/upgrade', 'panel.upgrade')->name('panel.upgrade');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect('/');
    })->name('logout');
});
