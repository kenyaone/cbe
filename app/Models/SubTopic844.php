<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubTopic844 extends Model
{
    use SoftDeletes;

    protected $table = 'sub_topics_844';
    protected $fillable = ['topic_844_id', 'code', 'name', 'description', 'order'];

    public function topic()
    {
        return $this->belongsTo(Topic844::class, 'topic_844_id');
    }

    public function contentFiles()
    {
        return $this->morphMany(ContentFile::class, 'contentable');
    }
}
