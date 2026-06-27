<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudDeviceStats extends Model
{
    protected $fillable = [
        'device_id',
        'date',
        'active_learners',
        'lessons_completed',
        'quiz_attempts',
        'avg_score',
        'certs_issued',
    ];

    protected $casts = [
        'date' => 'date',
        'avg_score' => 'float',
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

    public function device()
    {
        return $this->belongsTo(CloudDevice::class, 'device_id', 'device_id');
    }
}
