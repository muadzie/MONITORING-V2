<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Placement;
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
        ]);

        $user = $request->user();
        
        // Ambil penempatan aktif siswa
        $placement = Placement::where('student_id', $user->id)
            ->where('status', 'active')
            ->with('company')
            ->first();

        if (!$placement) {
            return response()->json([
                'message' => 'Anda belum memiliki penempatan PKL aktif'
            ], 400);
        }

        $company = $placement->company;

        if (!$company) {
            return response()->json([
                'message' => 'Data perusahaan tidak ditemukan'
            ], 400);
        }

        // Hitung jarak
        $distance = $this->calculateDistance(
            $request->latitude, $request->longitude,
            $company->latitude, $company->longitude
        );

        $isValidLocation = $distance <= $company->radius;

        if (!$isValidLocation) {
            return response()->json([
                'message' => "Anda berada di luar radius absensi! Jarak Anda " . round($distance) . "m, radius perusahaan {$company->radius}m",
                'distance' => round($distance),
                'radius' => $company->radius
            ], 400);
        }

        $today = Carbon::today();
        
        // Cek apakah sudah ada absensi hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->check_in) {
            return response()->json([
                'message' => 'Anda sudah check in hari ini'
            ], 400);
        }

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->company_id = $company->id;
            $attendance->date = $today;
        }

        $checkInTime = Carbon::now();
        $status = $checkInTime->format('H:i') > '08:00' ? 'late' : 'present';

        $attendance->check_in = $checkInTime;
        $attendance->check_in_lat = $request->latitude;
        $attendance->check_in_lng = $request->longitude;
        $attendance->status = $status;
        $attendance->save();

        // Buat notifikasi untuk guru
        if ($placement->teacher_id) {
            Notification::create([
                'user_id' => $placement->teacher_id,
                'title' => 'Siswa Check In',
                'message' => "{$user->name} telah melakukan check in PKL",
                'type' => 'info',
                'is_read' => false
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Check in berhasil',
            'status' => $status,
            'distance' => round($distance),
            'is_valid_location' => $isValidLocation
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'message' => 'Anda belum melakukan check in hari ini'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'message' => 'Anda sudah check out hari ini'
            ], 400);
        }

        // Ambil penempatan untuk validasi radius
        $placement = Placement::where('student_id', $user->id)
            ->where('status', 'active')
            ->with('company')
            ->first();

        if ($placement && $placement->company) {
            $company = $placement->company;
            $distance = $this->calculateDistance(
                $request->latitude, $request->longitude,
                $company->latitude, $company->longitude
            );
            
            $isValidLocation = $distance <= $company->radius;
        } else {
            $isValidLocation = false;
        }

        $attendance->check_out = Carbon::now();
        $attendance->check_out_lat = $request->latitude;
        $attendance->check_out_lng = $request->longitude;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Check out berhasil',
            'is_valid_location' => $isValidLocation
        ]);
    }

    public function today(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        return response()->json([
            'success' => true,
            'has_checked_in' => $attendance && $attendance->check_in ? true : false,
            'has_checked_out' => $attendance && $attendance->check_out ? true : false,
            'data' => $attendance
        ]);
    }

    public function history(Request $request)
    {
        $user = $request->user();
        
        $attendances = Attendance::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(30)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }

    // Fungsi untuk menghitung jarak (Haversine formula)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // dalam meter
        
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}