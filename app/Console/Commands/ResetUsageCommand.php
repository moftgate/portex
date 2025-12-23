<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\Tunnel;
use Illuminate\Console\Command;

class ResetUsageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portex:reset-usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset daily usage statistics for all tunnels and agents';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Resetting daily usage statistics...');

        // Reset tunnel usage
        Tunnel::active()->update([
            'usage_seconds_today' => 0,
            'bytes_uploaded' => 0,
            'bytes_downloaded' => 0,
            'total_requests' => 0,
        ]);

        // Reset agent usage
        Agent::query()->update([
            'total_usage_seconds' => 0,
            'bytes_uploaded' => 0,
            'bytes_downloaded' => 0,
            'total_requests' => 0,
        ]);

        $this->info('Daily usage statistics have been reset successfully.');
    }
}
