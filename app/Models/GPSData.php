<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GPSData extends Model
{
    protected $table = 'gps_data';

    protected $fillable = [
        'device_id',
        'latitude',
        'longitude',
        'altitude',
        'satellites',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'altitude' => 'float',
        'recorded_at' => 'datetime',
    ];
}
