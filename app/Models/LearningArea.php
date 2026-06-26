<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LearningArea extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'curriculum_type_id', 'code', 'name', 'description',
        'lessons_per_week', 'grade_level', 'order'
    ];

    public function curriculumType()
    {
        return $this->belongsTo(CurriculumType::class);
    }

    public function strands()
    {
        return $this->hasMany(Strand::class);
    }
}
