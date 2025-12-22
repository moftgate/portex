<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string|null $agent_id
 * @property string $name
 * @property string $subdomain
 * @property int $local_port
 * @property string $protocol
 * @property string $status
 * @property string|null $custom_domain
 * @property bool $auth_enabled
 * @property string|null $auth_username
 * @property string|null $auth_password
 * @property int $max_connections
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Agent|null $agent
 * @property-read string $public_url
 * @property-read string $status_badge
 * @property-read int $total_requests
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TunnelRequest> $requests
 * @property-read int|null $requests_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel active()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel inactive()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereAuthEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereAuthPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereAuthUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereLocalPort($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereMaxConnections($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereProtocol($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereSubdomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Tunnel whereUserId($value)
 * @mixin \Eloquent
 */
class Tunnel extends Model
{
    use HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'agent_id',
        'name',
        'subdomain',
        'local_port',
        'protocol',
        'status',
        'custom_domain',
        'auth_enabled',
        'auth_username',
        'auth_password',
        'max_connections',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'auth_enabled' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'auth_password',
    ];

    /**
     * Get the user that owns the tunnel.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the agent that owns the tunnel.
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Get the requests for the tunnel.
     */
    public function requests(): HasMany
    {
        return $this->hasMany(TunnelRequest::class);
    }

    /**
     * Scope a query to only include active tunnels.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive tunnels.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active' => 'success',
            'inactive' => 'warning',
            'error' => 'error',
            default => 'info',
        };
    }

    /**
     * Get the full public URL.
     */
    public function getPublicUrlAttribute(): string
    {
        if ($this->custom_domain) {
            return "https://{$this->custom_domain}";
        }

        $baseDomain = config('app.tunnel_domain', 'portex.io');
        return "https://{$this->subdomain}.{$baseDomain}";
    }

    /**
     * Get the total request count.
     */
    public function getTotalRequestsAttribute(): int
    {
        return $this->requests()->count();
    }
}
