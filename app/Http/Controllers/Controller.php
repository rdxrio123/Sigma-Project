<?php

namespace App\Http\Controllers;

use App\Models\AccelerometerData;
use App\Models\GPSData;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

abstract class Controller
{
    protected function dashboardTimezone(): string
    {
        return 'Asia/Jakarta';
    }

    protected function formatWibTimestamp(?\DateTimeInterface $dateTime, string $format): string
    {
        if ($dateTime === null) {
            return '--';
        }

        return $dateTime->format($format) . ' WIB';
    }

    protected function dashboardPayload(int $sampleLimit = 12): array
    {
        $latestGps = GPSData::query()->latest('recorded_at')->first();
        $latestAccelerometer = AccelerometerData::query()->latest('recorded_at')->first();
        $accelerometerSamples = AccelerometerData::query()
            ->latest('recorded_at')
            ->limit($sampleLimit)
            ->get();

        $accelerometerSamples = $accelerometerSamples->sortBy('recorded_at')->values();

        return [
            'gps' => $this->formatGpsData($latestGps),
            'currentAccel' => $this->formatAccelerometerData($latestAccelerometer),
            'accelSamples' => $this->formatAccelerometerSamples($accelerometerSamples),
            'summary' => $this->buildAccelerometerSummary($accelerometerSamples),
            'lastUpdatedAt' => $this->resolveLastUpdatedAt($latestGps, $latestAccelerometer),
        ];
    }

    protected function formatGpsData(?GPSData $gps): array
    {
        if ($gps === null) {
            return [
                'latitude' => 0.0,
                'longitude' => 0.0,
                'altitude' => 0.0,
                'satellites' => 0,
                'status' => 'NO FIX',
                'recorded_at' => '--',
            ];
        }

        return [
            'latitude' => (float) $gps->latitude,
            'longitude' => (float) $gps->longitude,
            'altitude' => $gps->altitude === null ? 0.0 : (float) $gps->altitude,
            'satellites' => (int) $gps->satellites,
            'status' => $gps->status,
            'recorded_at' => $this->formatWibTimestamp($gps->recorded_at?->timezone($this->dashboardTimezone()), 'd M Y H:i:s'),
        ];
    }

    protected function formatAccelerometerData(?AccelerometerData $accelerometer): array
    {
        if ($accelerometer === null) {
            return [
                'x' => 0.0,
                'y' => 0.0,
                'z' => 0.0,
                'magnitude' => 0.0,
                'time' => '--',
                'sensor_time' => '--',
            ];
        }

        return [
            'x' => (float) $accelerometer->x,
            'y' => (float) $accelerometer->y,
            'z' => (float) $accelerometer->z,
            'magnitude' => (float) $accelerometer->magnitude,
            'time' => $this->formatWibTimestamp($accelerometer->created_at?->timezone($this->dashboardTimezone()), 'H:i:s'),
            'sensor_time' => $this->formatWibTimestamp($accelerometer->recorded_at?->timezone($this->dashboardTimezone()), 'H:i:s'),
        ];
    }

    protected function formatAccelerometerSamples(EloquentCollection $samples): array
    {
        return $samples->map(function (AccelerometerData $sample): array {
            return [
                'time' => $this->formatWibTimestamp($sample->recorded_at?->timezone($this->dashboardTimezone()), 'H:i:s'),
                'x' => (float) $sample->x,
                'y' => (float) $sample->y,
                'z' => (float) $sample->z,
                'magnitude' => (float) $sample->magnitude,
            ];
        })->all();
    }

    protected function buildAccelerometerSummary(EloquentCollection $samples): array
    {
        if ($samples->isEmpty()) {
            return [
                'maximum' => 0.0,
                'average' => 0.0,
                'count' => 0,
            ];
        }

        return [
            'maximum' => round((float) $samples->max(fn (AccelerometerData $sample): float => (float) $sample->magnitude), 4),
            'average' => round((float) $samples->avg(fn (AccelerometerData $sample): float => (float) $sample->magnitude), 4),
            'count' => $samples->count(),
        ];
    }

    protected function resolveLastUpdatedAt(?GPSData $gps, ?AccelerometerData $accelerometer): ?string
    {
        $timestamps = array_filter([
            $gps?->recorded_at,
            $accelerometer?->recorded_at,
        ]);

        if ($timestamps === []) {
            return null;
        }

        $latestTimestamp = collect($timestamps)
            ->sortByDesc(fn ($timestamp) => $timestamp->timestamp)
            ->first();

        return $this->formatWibTimestamp($latestTimestamp?->timezone($this->dashboardTimezone()), 'd M Y H:i:s');
    }
}
