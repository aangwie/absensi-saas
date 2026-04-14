<?php

namespace App\Services;

use App\Models\Location;
use Illuminate\Database\Eloquent\Collection;

class HaversineService
{
    /**
     * Earth radius in meters
     */
    private const EARTH_RADIUS = 6371000;

    /**
     * Calculate distance between two GPS coordinates using Haversine Formula
     *
     * @param float $lat1 Latitude point 1
     * @param float $lon1 Longitude point 1
     * @param float $lat2 Latitude point 2
     * @param float $lon2 Longitude point 2
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) ** 2 +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round(self::EARTH_RADIUS * $c, 2);
    }

    /**
     * Find the nearest location from a collection of locations
     *
     * @param float $latitude User latitude
     * @param float $longitude User longitude
     * @param Collection $locations Collection of Location models
     * @return array|null ['location' => Location, 'distance' => float]
     */
    public function findNearestLocation(float $latitude, float $longitude, Collection $locations): ?array
    {
        if ($locations->isEmpty()) {
            return null;
        }

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($locations as $location) {
            $distance = $this->calculateDistance(
                $latitude,
                $longitude,
                (float) $location->latitude,
                (float) $location->longitude
            );

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = [
                    'location' => $location,
                    'distance' => $distance,
                ];
            }
        }

        return $nearest;
    }

    /**
     * Check if coordinates are within radius of any location
     *
     * @param float $latitude
     * @param float $longitude
     * @param Collection $locations
     * @return array ['within_radius' => bool, 'location' => ?Location, 'distance' => float, 'max_radius' => int]
     */
    public function checkWithinRadius(float $latitude, float $longitude, Collection $locations): array
    {
        $nearest = $this->findNearestLocation($latitude, $longitude, $locations);

        if (!$nearest) {
            return [
                'within_radius' => false,
                'location' => null,
                'distance' => 0,
                'max_radius' => 80,
            ];
        }

        $maxRadius = $nearest['location']->radius_max ?? 80;

        return [
            'within_radius' => $nearest['distance'] <= $maxRadius,
            'location' => $nearest['location'],
            'distance' => $nearest['distance'],
            'max_radius' => $maxRadius,
        ];
    }
}
