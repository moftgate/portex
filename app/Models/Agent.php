<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $id
 * @property string $user_id
 * @property string $name
 * @property string $api_key
 * @property string $api_secret
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property string $status
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $last_seen_human
 * @property-read string $status_badge
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tunnel> $tunnels
 * @property-read int|null $tunnels_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent offline()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent online()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereApiKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereApiSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Agent whereUserId($value)
 * @mixin \Eloquent
 */
class Agent extends Model
{
    use HasUuids;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'api_key',
        'api_secret',
        'last_seen_at',
        'status',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_seen_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'api_secret',
    ];

    /**
     * Get the user that owns the agent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tunnels for the agent.
     */
    public function tunnels(): HasMany
    {
        return $this->hasMany(Tunnel::class);
    }

    /**
     * Scope a query to only include online agents.
     */
    public function scopeOnline($query)
    {
        return $query->where('status', 'online');
    }

    /**
     * Scope a query to only include offline agents.
     */
    public function scopeOffline($query)
    {
        return $query->where('status', 'offline');
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'online' => 'success',
            'offline' => 'warning',
            'error' => 'error',
            default => 'info',
        };
    }

    /**
     * Get human-readable last seen time.
     */
    public function getLastSeenHumanAttribute(): ?string
    {
        return $this->last_seen_at?->diffForHumans();
    }
}
