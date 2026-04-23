<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'description',
        'latitude',
        'longitude',
        'radius',
    ];

    // Accessor untuk format radius
    public function getRadiusInKmAttribute()
    {
        return round($this->radius / 1000, 2);
    }

    // Method untuk validasi jarak
    public function isWithinRadius($userLat, $userLng)
    {
        $distance = $this->calculateDistance($userLat, $userLng, $this->latitude, $this->longitude);
        return $distance <= $this->radius;
    }

    // Method hitung jarak
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta/2) * sin($latDelta/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta/2) * sin($lonDelta/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return $earthRadius * $c;
    }

    public function placements()
{
    return $this->hasMany(Placement::class);
}

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}