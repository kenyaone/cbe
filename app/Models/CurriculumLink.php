<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumLink extends Model
{
    protected $fillable = ['sub_strand_id', 'linked_sub_strand_id', 'description'];
    public $timestamps = false;

    public function subStrand()
    {
        return $this->belongsTo(SubStrand::class);
    }

    public function linkedSubStrand()
    {
        return $this->belongsTo(SubStrand::class, 'linked_sub_strand_id');
    }
}
