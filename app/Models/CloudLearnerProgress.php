<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudLearnerProgress extends Model
{
    protected $fillable = [
        'device_id',
        'learner_username',
        'learner_name',
        'subject',
        'content_title',
        'progress_percentage',
        'status',
        'last_accessed_at',
        'completed_at',
        'synced_at',
    ];

    protected $casts = [
        'progress_percentage' => 'integer',
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public $timestamps = false;

    public function device()
    {
        return $this->belongsTo(CloudDevice::class, 'device_id', 'device_id');
    }
}
