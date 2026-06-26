<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurriculumType extends Model
{
    protected $fillable = ['name', 'description'];

    public function learningAreas()
    {
        return $this->hasMany(LearningArea::class);
    }

    public function topics844()
    {
        return $this->hasMany(Topic844::class);
    }
}
