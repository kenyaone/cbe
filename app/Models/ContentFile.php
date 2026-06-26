<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContentFile extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'contentable_type', 'contentable_id', 'content_type_id',
        'title', 'description', 'file_path', 'file_size', 'duration', 'order', 'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function contentable()
    {
        return $this->morphTo();
    }

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }

    public function learnerProgress()
    {
        return $this->hasMany(LearnerProgress::class);
    }
}
