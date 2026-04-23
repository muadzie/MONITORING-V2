<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|image|max:2048'
        ]);

        $user = $request->user();
        $company = $user->company;

        if (!$company) {
            return response()->json(['message' => 'Perusahaan tidak ditemukan'], 400);
        }

        // Hitung jarak
        $distance = $company->calculateDistance(
            $company->latitude, $company->longitude,
            $request->latitude, $request->longitude
        );

        $isValidLocation = $distance <= $company->radius;

        if (!$isValidLocation) {
            return response()->json([
                'message' => "Anda berada di luar radius absensi! Jarak Anda {$distance}m, radius perusahaan {$company->radius}m",
                'distance' => round($distance),
                'radius' => $company->radius
            ], 400);
        }

        $today = Carbon::today();
        $attendance = Attendance::firstOrNew(['user_id' => $user->id, 'date' => $today]);

        if ($attendance->check_in) {
            return response()->json(['message' => 'Anda sudah check in hari ini'], 400);
        }

        $checkInTime = Carbon::now();
        $status = $checkInTime->format('H:i') > '08:00' ? 'late' : 'present';

        // Upload foto jika ada
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('attendance-photos', 'public');
        }

        $attendance->fill([
            'company_id' => $company->id,
            'check_in' => $checkInTime,
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
            'status' => $status,
            'photo' => $photoPath,
            'is_valid_location' => $isValidLocation
        ]);
        $attendance->save();

        // Buat notifikasi untuk guru
        if ($user->teacher_id) {
            Notification::create([
                'user_id' => $user->teacher_id,
                'title' => 'Siswa Check In',
                'message' => "{$user->name} telah melakukan check in PKL",
                'type' => 'info',
                'url' => '/guru/monitoring'
            ]);
        }

        return response()->json([
            'message' => 'Check in berhasil',
            'status' => $status,
            'distance' => round($distance),
            'is_valid_location' => $isValidLocation
        ]);
    }

    // ... method lainnya
}