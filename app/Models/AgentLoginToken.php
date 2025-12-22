<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $agent_id
 * @property string $token
 * @property \Illuminate\Support\Carbon $expires_at
 * @property \Illuminate\Support\Carbon|null $used_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Agent $agent
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AgentLoginToken whereUsedAt($value)
 * @mixin \Eloquent
 */
class AgentLoginToken extends Model
{
    use HasUuids;

    protected $fillable = [
        'agent_id',
        'token',
        'expires_at',
        'used_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function isValid(): bool
    {
        return $this->used_at === null && $this->expires_at->isFuture();
    }

    public function markAsUsed(): void
    {
        $this->update(['used_at' => now()]);
    }
}
