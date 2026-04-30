<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Placement;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');
        $this->fillPreviousAbsences($user->id, $today);
        
        // ============================================================
        // CEK APAKAH SISWA MEMILIKI IZIN/SAKIT PADA TANGGAL INI
        // ============================================================
        $permission = Permission::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'approved')
            ->first();
        
        if ($permission) {
            $typeText = $permission->type === 'sick' ? 'SAKIT' : 'IZIN';
            return response()->json([
                'success' => false,
                'message' => "Anda memiliki pengajuan {$typeText} pada tanggal ini yang telah disetujui. Tidak perlu melakukan absensi.",
                'permission' => [
                    'type' => $permission->type,
                    'reason' => $permission->reason,
                    'date' => $permission->date,
                ]
            ], 400);
        }
        
        // ============================================================
        // CEK APAKAH SISWA MEMILIKI PENEMPATAN AKTIF
        // ============================================================
        $placement = Placement::where('student_id', $user->id)
            ->where('status', 'active')
            ->with('company')
            ->first();

        if (!$placement) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum memiliki penempatan PKL aktif'
            ], 400);
        }

        $company = $placement->company;

        // ============================================================
        // VALIDASI RADIUS
        // ============================================================
        $distance = $this->calculateDistance(
            $request->latitude, $request->longitude,
            $company->latitude, $company->longitude
        );

        if ($distance > $company->radius) {
            return response()->json([
                'success' => false,
                'message' => "Anda berada di luar radius absensi! Jarak Anda " . round($distance) . "m, radius perusahaan {$company->radius}m"
            ], 400);
        }

        // ============================================================
        // CEK APAKAH SUDAH CHECK IN HARI INI
        // ============================================================
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance && $attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check in hari ini'
            ], 400);
        }

        // ============================================================
        // PROSES CHECK IN
        // ============================================================
        $checkInTime = Carbon::now();
        $status = $checkInTime->format('H:i') > '08:00' ? 'late' : 'present';

        if (!$attendance) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->company_id = $company->id;
            $attendance->date = $today;
        }

        $attendance->check_in = $checkInTime;
        $attendance->check_in_lat = $request->latitude;
        $attendance->check_in_lng = $request->longitude;
        $attendance->status = $status;
        $attendance->is_valid_location = true;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Check in berhasil',
            'status' => $status,
            'distance' => round($distance)
        ]);
    }

    public function checkOut(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');
        
        // ============================================================
        // CEK APAKAH SISWA MEMILIKI IZIN/SAKIT PADA TANGGAL INI
        // ============================================================
        $permission = Permission::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'approved')
            ->first();
        
        if ($permission) {
            $typeText = $permission->type === 'sick' ? 'SAKIT' : 'IZIN';
            return response()->json([
                'success' => false,
                'message' => "Anda memiliki pengajuan {$typeText} pada tanggal ini yang telah disetujui. Tidak perlu melakukan absensi."
            ], 400);
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan check in hari ini'
            ], 400);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah check out hari ini'
            ], 400);
        }

        $attendance->check_out = Carbon::now();
        $attendance->check_out_lat = $request->latitude;
        $attendance->check_out_lng = $request->longitude;
        $attendance->save();

        return response()->json([
            'success' => true,
            'message' => 'Check out berhasil'
        ]);
    }

    public function today(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');
        
        // Cek izin/sakit
        $permission = Permission::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'approved')
            ->first();
        
        if ($permission) {
            return response()->json([
                'success' => true,
                'has_checked_in' => false,
                'has_checked_out' => false,
                'is_permission_day' => true,
                'permission_type' => $permission->type,
                'permission_reason' => $permission->reason,
                'message' => "Hari ini Anda sedang {$permission->type}"
            ]);
        }
        
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        return response()->json([
            'success' => true,
            'has_checked_in' => $attendance && $attendance->check_in ? true : false,
            'has_checked_out' => $attendance && $attendance->check_out ? true : false,
            'is_permission_day' => false,
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

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function fillPreviousAbsences($userId, $currentDate)
{
    // Ambil tanggal mulai PKL (dari placement)
    $placement = Placement::where('student_id', $userId)
        ->where('status', 'active')
        ->first();
    
    if (!$placement) {
        return;
    }
    
    $startDate = Carbon::parse($placement->start_date);
    $endDate = Carbon::parse($currentDate)->subDay();
    
    if ($startDate > $endDate) {
        return;
    }
    
    for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
        $dateStr = $date->format('Y-m-d');
        
        // Cek apakah sudah ada attendance
        $exists = Attendance::where('user_id', $userId)
            ->where('date', $dateStr)
            ->exists();
        
        if (!$exists) {
            // Cek apakah ada izin/sakit
            $permission = Permission::where('user_id', $userId)
                ->where('date', $dateStr)
                ->where('status', 'approved')
                ->exists();
            
            if (!$permission) {
                // Buat alpha
                Attendance::create([
                    'user_id' => $userId,
                    'company_id' => $placement->company_id,
                    'date' => $dateStr,
                    'status' => 'absent',
                    'notes' => 'Tidak hadir tanpa keterangan (auto generated)',
                    'is_valid_location' => false,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                
                \Log::info("Auto filled absent for user {$userId} on {$dateStr}");
            }
        }
    }
}
}