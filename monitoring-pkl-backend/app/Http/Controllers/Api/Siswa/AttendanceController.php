<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Placement;
use App\Models\Permission;
use App\Models\CompanyHoliday;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'photo' => 'nullable|string',
            'logbook_activity' => 'nullable|string',
            'logbook_description' => 'nullable|string',
        ]);

        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');
        
        // Isi absensi sebelumnya (alpha)
        $this->fillPreviousAbsences($user->id, $today);
        
        // ============================================================
        // CEK APAKAH SISWA MEMILIKI IZIN/SAKIT PADA TANGGAL HARI INI
        // ============================================================
        $permission = Permission::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'approved')
            ->first();
        
        if ($permission) {
            $typeText = $permission->type === 'sick' ? 'SAKIT' : 'IZIN';
            return response()->json([
                'success' => false,
                'message' => "Anda memiliki pengajuan {$typeText} pada tanggal {$today} yang telah disetujui. Tidak perlu melakukan absensi.",
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
        // CEK APAKAH HARI INI LIBUR UNTUK PERUSAHAAN INI
        // ============================================================
        $isHoliday = CompanyHoliday::where('company_id', $company->id)
            ->where('date', $today)
            ->exists();

        if ($isHoliday) {
            $holiday = CompanyHoliday::where('company_id', $company->id)
                ->where('date', $today)
                ->first();
            return response()->json([
                'success' => false,
                'message' => 'Hari ini adalah hari libur di ' . $company->name . ($holiday->description ? ' (' . $holiday->description . ')' : '') . '. Tidak perlu melakukan absensi.',
                'is_holiday' => true,
            ], 400);
        }

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
        
        // Tentukan batas waktu: shift_start placement -> deadline perusahaan -> global setting -> default 08:00
        $deadline = $placement->shift_start 
            ?? $company->checkin_deadline 
            ?? \App\Models\Setting::getValue('checkin_deadline', '08:00');
        
        $status = $checkInTime->format('H:i') > $deadline ? 'late' : 'present';

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

        if ($request->filled('photo')) {
            $attendance->photo = $this->saveBase64Photo($request->photo, $user->id);
        }

        $attendance->save();

        if ($request->filled('logbook_activity')) {
            try {
                \App\Models\Logbook::create([
                    'user_id' => $user->id,
                    'date' => $today,
                    'activity' => $request->logbook_activity,
                    'description' => $request->logbook_description ?? '',
                    'status' => 'pending',
                ]);
            } catch (\Exception $e) {
                \Log::error('Logbook auto-create error: ' . $e->getMessage());
            }
        }

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
            'photo' => 'nullable|string',
        ]);

        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');
        
        // ============================================================
        // CEK APAKAH SISWA MEMILIKI IZIN/SAKIT PADA TANGGAL HARI INI
        // ============================================================
        $permission = Permission::where('user_id', $user->id)
            ->where('date', $today)
            ->where('status', 'approved')
            ->first();
        
        if ($permission) {
            $typeText = $permission->type === 'sick' ? 'SAKIT' : 'IZIN';
            return response()->json([
                'success' => false,
                'message' => "Anda memiliki pengajuan {$typeText} pada tanggal {$today} yang telah disetujui. Tidak perlu melakukan absensi."
            ], 400);
        }

        // ============================================================
        // CEK APAKAH HARI INI LIBUR UNTUK PERUSAHAAN
        // ============================================================
        $placement = Placement::where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($placement) {
            $isHoliday = CompanyHoliday::where('company_id', $placement->company_id)
                ->where('date', $today)
                ->exists();

            if ($isHoliday) {
                return response()->json([
                    'success' => false,
                    'message' => 'Hari ini adalah hari libur di perusahaan anda. Tidak perlu melakukan absensi.',
                    'is_holiday' => true,
                ], 400);
            }
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

        if ($request->filled('photo')) {
            $attendance->photo_out = $this->saveBase64Photo($request->photo, $user->id);
        }

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
        
        // Cek izin/sakit HANYA UNTUK HARI INI
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
                'permission_date' => $permission->date,
                'message' => "Hari ini Anda sedang " . ($permission->type === 'sick' ? 'SAKIT' : 'IZIN')
            ]);
        }

        // Cek apakah hari ini libur perusahaan
        $placement = Placement::where('student_id', $user->id)
            ->where('status', 'active')
            ->first();

        $isHoliday = false;
        $holidayDescription = null;

        if ($placement) {
            $holiday = CompanyHoliday::where('company_id', $placement->company_id)
                ->where('date', $today)
                ->first();

            if ($holiday) {
                $isHoliday = true;
                $holidayDescription = $holiday->description;
            }
        }

        if ($isHoliday) {
            return response()->json([
                'success' => true,
                'has_checked_in' => false,
                'has_checked_out' => false,
                'is_holiday' => true,
                'holiday_description' => $holidayDescription,
                'message' => 'Hari ini adalah hari libur' . ($holidayDescription ? ' (' . $holidayDescription . ')' : '')
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
            'is_holiday' => false,
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

    public function monthly(Request $request)
    {
        $user = $request->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        $attendances = Attendance::where('user_id', $user->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->orderBy('date', 'asc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }
    
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $user = $request->user();
        $path = $request->file('photo')->store('attendance-photos', 'public');

        // Simpan photo ke attendance hari ini jika sudah check-in
        $today = now()->format('Y-m-d');
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        if ($attendance) {
            $attendance->photo = $path;
            $attendance->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil diupload',
            'photo_url' => asset('storage/' . $path),
        ]);
    }
    
    public function todayWithPhoto(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today()->format('Y-m-d');

        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $today)
            ->first();

        $photoUrl = null;
        if ($attendance && $attendance->photo) {
            $photoUrl = asset('storage/' . $attendance->photo);
        }

        return response()->json([
            'success' => true,
            'has_checked_in' => $attendance && $attendance->check_in ? true : false,
            'has_checked_out' => $attendance && $attendance->check_out ? true : false,
            'data' => $attendance,
            'photo_url' => $photoUrl,
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        return haversineDistance($lat1, $lon1, $lat2, $lon2);
    }

    private function saveBase64Photo($base64, $userId)
    {
        $imageData = base64_decode($base64);
        $filename = 'attendance_' . $userId . '_' . time() . '.jpg';
        $path = 'attendance-photos/' . $filename;
        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $imageData);
        return $path;
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

        // Ambil daftar hari libur perusahaan
        $companyHolidays = CompanyHoliday::where('company_id', $placement->company_id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $currentDate])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->format('Y-m-d'))
            ->toArray();
        
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            
            // Lewati hari libur (Sabtu/Minggu)
            if ($date->isWeekend()) {
                continue;
            }

            // Lewati hari libur perusahaan
            if (in_array($dateStr, $companyHolidays)) {
                continue;
            }
            
            // Cek apakah sudah ada attendance
            $exists = Attendance::where('user_id', $userId)
                ->where('date', $dateStr)
                ->exists();
            
            if (!$exists) {
                // Cek apakah ada izin/sakit untuk tanggal itu
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
                        'notes' => 'Tidak hadir tanpa keterangan',
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