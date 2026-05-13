<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlarmController extends Controller
{
    public function index()
    {
        $alarms = [
            [
                'title' => 'Deteksi getaran kuat',
                'description' => 'Sensor accelerometer mendeteksi lonjakan getaran di zona utara.',
                'status' => 'Aktif',
            ],
            [
                'title' => 'GPS tidak stabil',
                'description' => 'Koordinat melompat dalam data GPS NEO-6M selama 12 menit terakhir.',
                'status' => 'Peringatan',
            ],
            [
                'title' => 'Koneksi sensor intermittend',
                'description' => 'Rekaman data dari perangkat sensor terputus dan tersambung kembali.',
                'status' => 'Perlu perhatian',
            ],
        ];

        return view('alarm', compact('alarms'));
    }
}
