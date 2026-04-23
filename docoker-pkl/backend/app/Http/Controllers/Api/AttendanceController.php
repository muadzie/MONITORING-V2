<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function checkIn(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = $request->user();
        $company = $user->company;
        
        if (!$company) {
            return response()->json(['message' => 'No company assigned'], 400);
        }

        // Calculate distance
        $distance = $this->calculateDistance(
            $company->latitude, $company->longitude,
            $request->latitude, $request->longitude
        );

        if ($distance > $company->radius) {
            return response()->json([
                'message' => 'You are outside the permitted radius',
                'distance' => $distance,
                'max_radius' => $company->radius
            ], 400);
        }

        $today = Carbon::today();
        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => $today
        ]);

        $checkInTime = Carbon::now();
        $status = $checkInTime->format('H:i') > '08:00' ? 'late' : 'present';

        $attendance->fill([
            'company_id' => $company->id,
            'check_in' => $checkInTime,
            'check_in_lat' => $request->latitude,
            'check_in_lng' => $request->longitude,
            'status' => $status
        ]);

        $attendance->save();

        return response()->json([
            'message' => 'Check-in successful',
            'attendance' => $attendance,
            'status' => $status
        ]);
    }

    public function checkOut(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        if (!$attendance || !$attendance->check_in) {
            return response()->json(['message' => 'No check-in found for today'], 400);
        }

        if ($attendance->check_out) {
            return response()->json(['message' => 'Already checked out'], 400);
        }

        $attendance->update([
            'check_out' => Carbon::now(),
            'check_out_lat' => $request->latitude,
            'check_out_lng' => $request->longitude,
        ]);

        return response()->json([
            'message' => 'Check-out successful',
            'attendance' => $attendance
        ]);
    }

    public function getTodayStatus(Request $request)
    {
        $user = $request->user();
        $today = Carbon::today();
        
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', $today)
            ->first();

        return response()->json([
            'has_checked_in' => $attendance && $attendance->check_in,
            'has_checked_out' => $attendance && $attendance->check_out,
            'attendance' => $attendance,
            'status' => $attendance ? $attendance->status : 'absent'
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta/2) * sin($latDelta/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta/2) * sin($lonDelta/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}