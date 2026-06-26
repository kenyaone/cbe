<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubStrand extends Model
{
    use SoftDeletes;

    protected $fillable = ['strand_id', 'code', 'name', 'description', 'lesson_count', 'order'];

    public function strand()
    {
        return $this->belongsTo(Strand::class);
    }

    public function contentFiles()
    {
        return $this->morphMany(ContentFile::class, 'contentable');
    }

    public function learningOutcomes()
    {
        return $this->hasMany(LearningOutcome::class);
    }

    public function competencies()
    {
        return $this->hasMany(Competency::class);
    }

    public function inquiryQuestions()
    {
        return $this->hasMany(InquiryQuestion::class);
    }

    public function learningExperiences()
    {
        return $this->hasMany(LearningExperience::class);
    }

    public function values()
    {
        return $this->hasMany(Value::class);
    }

    public function curriculumLinks()
    {
        return $this->hasMany(CurriculumLink::class);
    }

    public function linkedSubStrands()
    {
        return $this->belongsToMany(
            SubStrand::class,
            'curriculum_links',
            'sub_strand_id',
            'linked_sub_strand_id'
        );
    }
}
