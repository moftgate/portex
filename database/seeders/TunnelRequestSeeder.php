<?php

namespace Database\Seeders;

use App\Models\Tunnel;
use App\Models\TunnelRequest;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TunnelRequestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        if (!$user) return;

        $tunnels = Tunnel::where('user_id', $user->id)->get();
        if ($tunnels->isEmpty()) {
            // Create a demo tunnel if none exist
            $tunnel = Tunnel::create([
                'user_id' => $user->id,
                'name' => 'Demo API',
                'subdomain' => 'demo-api-' . Str::random(4),
                'local_port' => 3000,
                'protocol' => 'http',
                'status' => 'active',
            ]);
            $tunnels = collect([$tunnel]);
        }

        $methods = ['GET', 'POST', 'PUT', 'DELETE'];
        $paths = ['/api/v1/users', '/api/v1/projects', '/api/v1/auth/login', '/dashboard', '/settings', '/api/v1/data'];
        $statusCodes = [200, 200, 200, 201, 204, 400, 401, 403, 404, 500];
        $userAgents = [
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 17_1_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.1.2 Mobile/15E148 Safari/604.1',
            'PostmanRuntime/7.36.0',
            'curl/8.4.0'
        ];

        // Create requests for the last 24 hours
        for ($i = 0; $i < 500; $i++) {
            $tunnel = $tunnels->random();
            $createdAt = now()->subMinutes(rand(0, 1440));
            
            TunnelRequest::create([
                'tunnel_id' => $tunnel->id,
                'method' => $methods[array_rand($methods)],
                'path' => $paths[array_rand($paths)],
                'status_code' => $statusCodes[array_rand($statusCodes)],
                'response_time_ms' => rand(15, 800),
                'ip_address' => rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255),
                'user_agent' => $userAgents[array_rand($userAgents)],
                'created_at' => $createdAt,
            ]);
        }
    }
}
