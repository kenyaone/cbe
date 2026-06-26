<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = ['name', 'location', 'latitude', 'longitude', 'server_ip'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
