<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryQuestion extends Model
{
    protected $fillable = ['sub_strand_id', 'question', 'order'];
    public $timestamps = false;

    public function subStrand()
    {
        return $this->belongsTo(SubStrand::class);
    }
}
