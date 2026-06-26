<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Strand extends Model
{
    use SoftDeletes;

    protected $fillable = ['learning_area_id', 'code', 'name', 'description', 'order'];

    public function learningArea()
    {
        return $this->belongsTo(LearningArea::class);
    }

    public function subStrands()
    {
        return $this->hasMany(SubStrand::class);
    }
}
