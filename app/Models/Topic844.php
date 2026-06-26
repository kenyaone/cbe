<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic844 extends Model
{
    use SoftDeletes;

    protected $table = 'topics_844';
    protected $fillable = ['curriculum_type_id', 'code', 'name', 'description', 'form_level', 'subject', 'order'];

    public function curriculumType()
    {
        return $this->belongsTo(CurriculumType::class);
    }

    public function subTopics()
    {
        return $this->hasMany(SubTopic844::class, 'topic_844_id');
    }
}
