<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competency extends Model
{
    protected $fillable = ['sub_strand_id', 'type', 'description'];
    public $timestamps = false;

    public function subStrand()
    {
        return $this->belongsTo(SubStrand::class);
    }
}
