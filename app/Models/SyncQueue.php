<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncQueue extends Model
{
    protected $fillable = [
        'entity_type',
        'entity_id',
        'action',
        'data',
        'synced',
        'synced_at',
    ];

    protected $casts = [
        'data' => 'array',
        'synced' => 'boolean',
        'synced_at' => 'datetime',
    ];

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (!isset($model->created_at)) {
                $model->created_at = now();
            }
        });
    }
}
