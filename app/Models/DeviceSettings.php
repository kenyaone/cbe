<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSettings extends Model
{
    protected $fillable = [
        'device_id',
        'device_name',
        'latitude',
        'longitude',
        'last_checkin',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'last_checkin' => 'datetime',
    ];
}
