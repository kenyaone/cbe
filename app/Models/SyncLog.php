<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncLog extends Model
{
    protected $fillable = [
        'device_id', 'user_id', 'last_sync_at', 'records_synced',
        'sync_direction', 'status', 'error_message'
    ];

    protected $casts = [
        'last_sync_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
