<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningExperience extends Model
{
    protected $fillable = ['sub_strand_id', 'description', 'order'];
    public $timestamps = false;

    public function subStrand()
    {
        return $this->belongsTo(SubStrand::class);
    }
}
