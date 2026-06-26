<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearnerProgress extends Model
{
    protected $fillable = [
        'user_id', 'sub_strand_id', 'sub_topic_844_id', 'content_file_id',
        'progress_percentage', 'status', 'last_accessed_at', 'completed_at',
        'device_id', 'sync_status', 'synced_at'
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
        'synced_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subStrand()
    {
        return $this->belongsTo(SubStrand::class);
    }

    public function subTopic844()
    {
        return $this->belongsTo(SubTopic844::class, 'sub_topic_844_id');
    }

    public function contentFile()
    {
        return $this->belongsTo(ContentFile::class);
    }
}
