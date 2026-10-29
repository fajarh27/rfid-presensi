<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = ['name', 'location', 'api_key', 'is_active', 'last_seen_at'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'last_seen_at' => 'datetime'];
    }

    public function getIsOnlineAttribute(): bool
    {
        return $this->last_seen_at && $this->last_seen_at->gt(now()->subMinutes(5));
    }
}
