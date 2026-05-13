<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): \Illuminate\View\View
    {
        $gps = [
            'latitude' => '-7.250445',
            'longitude' => '112.768845',
            'altitude' => '75 m',
            'satellites' => 9,
            'status' => '3D FIX',
        ];

        $accelSamples = [
            ['time' => '10:00', 'x' => 0.05, 'y' => 0.12, 'z' => 0.08, 'magnitude' => 0.16],
            ['time' => '10:05', 'x' => 0.08, 'y' => 0.18, 'z' => 0.11, 'magnitude' => 0.24],
            ['time' => '10:10', 'x' => 0.10, 'y' => 0.23, 'z' => 0.16, 'magnitude' => 0.32],
            ['time' => '10:15', 'x' => 0.06, 'y' => 0.14, 'z' => 0.10, 'magnitude' => 0.21],
            ['time' => '10:20', 'x' => 0.12, 'y' => 0.29, 'z' => 0.19, 'magnitude' => 0.39],
            ['time' => '10:25', 'x' => 0.09, 'y' => 0.22, 'z' => 0.15, 'magnitude' => 0.31],
            ['time' => '10:30', 'x' => 0.11, 'y' => 0.24, 'z' => 0.21, 'magnitude' => 0.39],
        ];

        return view('dashboard', [
            'gps' => $gps,
            'accelSamples' => $accelSamples,
            'currentAccel' => end($accelSamples),
        ]);
    }
}
