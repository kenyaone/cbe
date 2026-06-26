<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Value extends Model
{
    protected $fillable = ['sub_strand_id', 'name', 'description'];
    public $timestamps = false;

    public function subStrand()
    {
        return $this->belongsTo(SubStrand::class);
    }
}
