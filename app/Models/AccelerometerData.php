<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccelerometerData extends Model
{
    protected $table = 'accelerometer_data';

    protected $fillable = [
        'x',
        'y',
        'z',
        'magnitude',
        'recorded_at',
    ];

    protected $casts = [
        'x' => 'decimal:4',
        'y' => 'decimal:4',
        'z' => 'decimal:4',
        'magnitude' => 'decimal:4',
        'recorded_at' => 'datetime',
    ];
}
