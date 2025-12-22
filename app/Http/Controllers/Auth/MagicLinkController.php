<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AgentLoginToken;

class MagicLinkController extends Controller
{
    public function login(string $token)
    {
        $loginToken = AgentLoginToken::firstWhere('token', $token);

        if (! $loginToken) {
            return to_route('home')->with('error', 'Invalid or expired login link.');
        }

        if (! $loginToken->isValid()) {
            return to_route('home')->with('error', 'This login link has expired or already been used.');
        }

        $loginToken->markAsUsed();

        $agent = $loginToken->agent;
        $user = $agent->user;

        auth()->login($user);

        request()->session()->regenerate();

        return to_route('dashboard')->with('success', "Welcome! You've been logged in via magic link.");
    }
}
