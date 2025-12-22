<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mary\Traits\Toast;

class AgentClaimController extends Controller
{
    use Toast;

    public function claim(Request $request)
    {
        $apiKey = $request->query('api_key');
        $apiSecret = $request->query('api_secret');

        if (! $apiKey || ! $apiSecret) {
            return to_route('home')->with('error', 'Invalid claim parameters.');
        }

        $agent = Agent::firstWhere('api_key', $apiKey);

        if (! $agent || ! password_verify($apiSecret, $agent->api_secret)) {
            return to_route('home')->with('error', 'Agent not found or credentials invalid.');
        }

        // Get the device-specific user
        $deviceUser = $agent->user;

        // If user is already logged in as the device user, go to dashboard
        if (auth()->check() && auth()->id() === $deviceUser->id) {
            return to_route('dashboard')->with('success', "Welcome back! Agent '{$agent->name}' is active.");
        }

        // If logged in as different user, show error
        if (auth()->check()) {
            return to_route('dashboard')->with('error', 'This agent belongs to a different device account.');
        }

        // Auto-login as device user
        auth()->login($deviceUser);

        request()->session()->regenerate();

        return to_route('dashboard')->with('info', "Welcome! You've been automatically logged in via your agent.");
    }
}
