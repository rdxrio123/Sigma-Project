<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccelerometerData extends Model
{
    protected $table = 'accelerometer_data';

    protected $fillable = [
        'device_id',
        'x',
        'y',
        'z',
        'magnitude',
        'recorded_at',
    ];

    protected $casts = [
        'x' => 'float',
        'y' => 'float',
        'z' => 'float',
        'magnitude' => 'float',
        'recorded_at' => 'datetime',
    ];
}
