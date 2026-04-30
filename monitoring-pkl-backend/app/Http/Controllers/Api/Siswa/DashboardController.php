<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Assessment;
use App\Models\Placement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = auth()->user();
        
        // Hitung persentase kehadiran
        $totalAttendance = Attendance::where('user_id', $user->id)->count();
        $presentCount = Attendance::where('user_id', $user->id)
            ->whereIn('status', ['present', 'late'])
            ->count();
        $attendancePercentage = $totalAttendance > 0 ? round(($presentCount / $totalAttendance) * 100) : 0;
        
        // Hitung logbook
        $totalLogbooks = Logbook::where('user_id', $user->id)->count();
        $approvedLogbooks = Logbook::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();
        $pendingLogbooks = Logbook::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();
        
        // Hitung rata-rata nilai
        $assessments = Assessment::where('student_id', $user->id)
            ->where('assessor_type', 'guru')
            ->get();
        $averageGrade = $assessments->avg('final_score');
        
        return response()->json([
            'attendance' => $attendancePercentage,
            'logbooks' => $totalLogbooks,
            'grade' => round($averageGrade),
            'approved_logbooks' => $approvedLogbooks,
            'pending_logbooks' => $pendingLogbooks,
        ]);
    }
    
    public function recentLogbooks()
    {
        $user = auth()->user();
        
        $logbooks = Logbook::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return response()->json($logbooks);
    }
}