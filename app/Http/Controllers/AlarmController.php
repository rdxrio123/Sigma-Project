<?php

namespace App\Http\Controllers;

use App\Models\AccelerometerData;
use App\Models\GPSData;
use Illuminate\View\View;

class AlarmController extends Controller
{
    public function index(): View
    {
        $latestGps = GPSData::query()->latest('recorded_at')->first();
        $latestAccel = AccelerometerData::query()->latest('recorded_at')->first();

        $alarms = [];

        if ($latestAccel !== null && (float) $latestAccel->magnitude >= 10.0) {
            $alarms[] = [
                'title' => 'Deteksi getaran kuat',
                'description' => 'Magnitudo accelerometer melebihi ambang aman dan perlu dipantau.',
                'status' => 'Aktif',
            ];
        }

        if ($latestGps === null) {
            $alarms[] = [
                'title' => 'GPS belum mengirim data',
                'description' => 'Belum ada koordinat GPS yang tersimpan di database.',
                'status' => 'Peringatan',
            ];
        } elseif ((float) $latestGps->satellites < 4) {
            $alarms[] = [
                'title' => 'GPS tidak stabil',
                'description' => 'Jumlah satelit rendah sehingga akurasi koordinat dapat turun.',
                'status' => 'Peringatan',
            ];
        }

        if ($latestAccel !== null && $latestAccel->created_at !== null && $latestAccel->created_at->diffInMinutes(now()) >= 5) {
            $alarms[] = [
                'title' => 'Koneksi sensor terputus',
                'description' => 'Data accelerometer terakhir sudah cukup lama dan perlu dicek koneksinya.',
                'status' => 'Perlu perhatian',
            ];
        }

        if ($alarms === []) {
            $alarms[] = [
                'title' => 'Sistem normal',
                'description' => 'Belum ada alarm aktif dari sensor yang terdeteksi.',
                'status' => 'Aman',
            ];
        }

        return view('alarm', compact('alarms'));
    }
}
