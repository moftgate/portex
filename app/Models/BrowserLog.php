<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrowserLog extends Model
{
    use HasUuids;

    const UPDATED_AT = null;

    protected $fillable = [
        'tunnel_id',
        'type',
        'message',
        'url',
    ];

    public function tunnel(): BelongsTo
    {
        return $this->belongsTo(Tunnel::class);
    }
}
