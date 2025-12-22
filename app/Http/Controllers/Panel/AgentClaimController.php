<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Mary\Traits\Toast;

class AgentClaimController extends Controller
{
    use Toast;

    public function claim(Request $request)
    {
        $apiKey = $request->query('api_key');
        $apiSecret = $request->query('api_secret');

        if (!$apiKey || !$apiSecret) {
            return redirect()->route('dashboard')->with('error', 'Invalid claim parameters.');
        }

        // Find the agent by credentials
        $agent = Agent::where('api_key', $apiKey)->first();

        if (!$agent || !password_verify($apiSecret, $agent->api_secret)) {
            return redirect()->route('dashboard')->with('error', 'Agent not found or credentials invalid.');
        }

        // If agent is already claimed by someone else (not the anonymous user)
        $anonymousUser = \App\Models\User::where('email', 'anonymous@portex.io')->first();
        
        if ($agent->user_id !== $anonymousUser?->id && $agent->user_id !== auth()->id()) {
            return redirect()->route('dashboard')->with('error', 'This agent is already claimed by another user.');
        }

        // Link agent to current user
        $agent->user_id = auth()->id();
        $agent->save();

        return redirect()->route('agents.index')->with('success', "Agent '{$agent->name}' has been successfully linked to your account!");
    }
}
