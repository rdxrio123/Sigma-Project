<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GPSData extends Model
{
    protected $table = 'gps_data';

    protected $fillable = [
        'latitude',
        'longitude',
        'altitude',
        'satellites',
        'status',
        'recorded_at',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'altitude' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];
}
