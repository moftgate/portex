<?php

namespace App\Services;

use App\Models\Agent;
use App\Models\Tunnel;
use App\Models\User;

class UsageTrackingService
{
    /**
     * Check if user has exceeded daily usage limit
     */
    public function hasExceededDailyLimit(User $user): bool
    {
        if ($user->tier === 'premium') {
            return false; // Premium users have no limit
        }

        $stats = $this->getUsageStats($user);

        // Check time limit
        if ($stats['used_seconds'] >= $user->daily_usage_limit_seconds) {
            return true;
        }

        // Check bandwidth limit
        if ($stats['total_bandwidth'] >= $user->bandwidth_limit_bytes) {
            return true;
        }

        return false;
    }

    /**
     * Get today's total usage in seconds for a user
     */
    public function getTodayUsageSeconds(User $user): int
    {
        return Agent::firstWhere('user_id', $user->id)?->total_usage_seconds ?? 0;
    }

    /**
     * Get remaining time for today in seconds
     */
    public function getRemainingSeconds(User $user): int
    {
        if ($user->tier === 'premium') {
            return PHP_INT_MAX; // Unlimited
        }

        $used = $this->getTodayUsageSeconds($user);

        $remaining = $user->daily_usage_limit_seconds - $used;

        return max(0, $remaining);
    }

    /**
     * Track bandwidth usage for a tunnel
     */
    public function trackBandwidth(Tunnel $tunnel, int $bytesUploaded, int $bytesDownloaded): void
    {
        $tunnel->increment('bytes_uploaded', $bytesUploaded);
        $tunnel->increment('bytes_downloaded', $bytesDownloaded);
        $tunnel->increment('total_requests');
        $tunnel->update(['last_activity_at' => now()]);

        if ($tunnel->agent) {
            $tunnel->agent->increment('bytes_uploaded', $bytesUploaded);
            $tunnel->agent->increment('bytes_downloaded', $bytesDownloaded);
            $tunnel->agent->increment('total_requests');
        }
    }

    /**
     * Track usage time for a tunnel
     */
    public function trackUsageTime(Tunnel $tunnel, int $seconds): void
    {
        $tunnel->increment('usage_seconds_today', $seconds);

        $tunnel->update([
            'last_activity_at' => now(),
            'status' => 'active',
        ]);

        if ($tunnel->agent) {
            $tunnel->agent->increment('total_usage_seconds', $seconds);
        }
    }

    /**
     * Get formatted usage stats for a user
     */
    public function getUsageStats(User $user): array
    {
        $usedSeconds = $this->getTodayUsageSeconds($user);

        $limitSeconds = $user->daily_usage_limit_seconds;
        $remainingSeconds = $this->getRemainingSeconds($user);

        $totalBandwidth = $user->agents()
            ->with('tunnels')
            ->get()
            ->flatMap->tunnels
            ->sum(fn ($t) => $t->bytes_uploaded + $t->bytes_downloaded);

        return [
            'tier' => $user->tier,
            'used_seconds' => $usedSeconds,
            'limit_seconds' => $limitSeconds,
            'remaining_seconds' => $remainingSeconds,
            'used_formatted' => $this->formatSeconds($usedSeconds),
            'limit_formatted' => $this->formatSeconds($limitSeconds),
            'remaining_formatted' => $this->formatSeconds($remainingSeconds),
            'percentage_used' => $user->tier === 'premium' ? 0 : round(($usedSeconds / $limitSeconds) * 100, 2),
            'total_bandwidth' => $totalBandwidth,
            'total_bandwidth_formatted' => $this->formatBytes($totalBandwidth),
            'is_premium' => $user->tier === 'premium',
        ];
    }

    /**
     * Format seconds to human-readable format
     */
    protected function formatSeconds(int $seconds): string
    {
        if ($seconds >= 3600) {
            $hours = floor($seconds / 3600);
            $minutes = floor(($seconds % 3600) / 60);

            return "{$hours}h {$minutes}m";
        }

        if ($seconds >= 60) {
            $minutes = floor($seconds / 60);
            $secs = $seconds % 60;

            return "{$minutes}m {$secs}s";
        }

        return "{$seconds}s";
    }

    /**
     * Format bytes to human-readable format
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = $bytes > 0 ? floor(log($bytes, 1024)) : 0;

        return round($bytes / pow(1024, $power), 2).' '.$units[$power];
    }
}
