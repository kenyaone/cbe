<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudDevice extends Model
{
    protected $fillable = [
        'device_id',
        'device_name',
        'region',
        'county',
        'latitude',
        'longitude',
        'total_students',
        'total_lessons',
        'total_certs',
        'avg_score',
        'last_sync_at',
        'is_online',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'avg_score' => 'float',
        'last_sync_at' => 'datetime',
        'is_online' => 'boolean',
    ];

    public function learnerProgress()
    {
        return $this->hasMany(CloudLearnerProgress::class, 'device_id', 'device_id');
    }

    public function stats()
    {
        return $this->hasMany(CloudDeviceStats::class, 'device_id', 'device_id');
    }
}
