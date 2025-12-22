<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $tunnel_id
 * @property string $method
 * @property string $path
 * @property int $status_code
 * @property int $response_time_ms
 * @property string $ip_address
 * @property string|null $user_agent
 * @property \Illuminate\Support\Carbon $created_at
 * @property-read string $formatted_response_time
 * @property-read string $status_color
 * @property-read \App\Models\Tunnel $tunnel
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereIpAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereResponseTimeMs($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereStatusCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereTunnelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TunnelRequest whereUserAgent($value)
 * @mixin \Eloquent
 */
class TunnelRequest extends Model
{
    use HasUuids;
    /**
     * Disable updated_at timestamp.
     */
    public const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'tunnel_id',
        'method',
        'path',
        'status_code',
        'response_time_ms',
        'ip_address',
        'user_agent',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the tunnel that owns the request.
     */
    public function tunnel(): BelongsTo
    {
        return $this->belongsTo(Tunnel::class);
    }

    /**
     * Get formatted response time.
     */
    public function getFormattedResponseTimeAttribute(): string
    {
        if ($this->response_time_ms < 1000) {
            return "{$this->response_time_ms}ms";
        }

        return round($this->response_time_ms / 1000, 2) . 's';
    }

    /**
     * Get status code color.
     */
    public function getStatusColorAttribute(): string
    {
        return match (true) {
            $this->status_code >= 200 && $this->status_code < 300 => 'success',
            $this->status_code >= 300 && $this->status_code < 400 => 'info',
            $this->status_code >= 400 && $this->status_code < 500 => 'warning',
            $this->status_code >= 500 => 'error',
            default => 'secondary',
        };
    }
}
