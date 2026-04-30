<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Permission;
use App\Models\Placement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        $user = auth()->user();
        
        // Ambil semua siswa yang dibimbing oleh guru ini
        $studentIds = User::where('role_id', 2)
            ->where('teacher_id', $user->id)
            ->pluck('id');
        
        // Hitung total siswa bimbingan
        $totalStudents = $studentIds->count();
        
        // Hitung kehadiran hari ini
        $today = Carbon::today()->format('Y-m-d');
        $presentToday = Attendance::whereIn('user_id', $studentIds)
            ->where('date', $today)
            ->whereIn('status', ['present', 'late'])
            ->count();
        
        // Hitung logbook pending
        $pendingLogbooks = Logbook::whereIn('user_id', $studentIds)
            ->where('status', 'pending')
            ->count();
        
        // Hitung permission pending
        $pendingPermissions = Permission::whereIn('user_id', $studentIds)
            ->where('status', 'pending')
            ->count();
        
        // Hitung rata-rata kehadiran bulan ini
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');
        $totalAttendance = Attendance::whereIn('user_id', $studentIds)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->count();
        $totalStudentsDays = $totalStudents * Carbon::now()->daysInMonth;
        $averageAttendance = $totalStudentsDays > 0 ? round(($totalAttendance / $totalStudentsDays) * 100) : 0;
        
        return response()->json([
            'students' => $totalStudents,
            'present_today' => $presentToday,
            'pending_logbooks' => $pendingLogbooks,
            'pending_permissions' => $pendingPermissions,
            'average_attendance' => $averageAttendance,
        ]);
    }
    
    public function topStudents()
    {
        $user = auth()->user();
        
        // Ambil siswa yang dibimbing
        $students = User::where('role_id', 2)
            ->where('teacher_id', $user->id)
            ->with('company')
            ->get();
        
        // Hitung jumlah logbook per siswa
        $topStudents = $students->map(function($student) {
            $logbookCount = Logbook::where('user_id', $student->id)->count();
            return [
                'id' => $student->id,
                'name' => $student->name,
                'company_name' => $student->company->name ?? 'Belum ditempatkan',
                'logbook_count' => $logbookCount,
            ];
        })->sortByDesc('logbook_count')->take(5)->values();
        
        return response()->json($topStudents);
    }
    
    public function attendanceChart(Request $request)
    {
        $user = auth()->user();
        $period = $request->get('period', 'week');
        
        $studentIds = User::where('role_id', 2)
            ->where('teacher_id', $user->id)
            ->pluck('id');
        
        if ($period === 'week') {
            $startDate = Carbon::now()->startOfWeek();
            $endDate = Carbon::now()->endOfWeek();
            $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
            $days = [];
            for ($i = 1; $i <= $endDate->day; $i++) {
                $days[] = $i;
            }
        }
        
        $chartData = [];
        $currentDate = clone $startDate;
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayName = $period === 'week' ? $days[$currentDate->dayOfWeekIso - 1] : $currentDate->day;
            
            $hadir = Attendance::whereIn('user_id', $studentIds)
                ->where('date', $dateStr)
                ->whereIn('status', ['present', 'late'])
                ->count();
            
            $terlambat = Attendance::whereIn('user_id', $studentIds)
                ->where('date', $dateStr)
                ->where('status', 'late')
                ->count();
            
            $chartData[] = [
                'day' => $dayName,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
            ];
            
            $currentDate->addDay();
        }
        
        return response()->json($chartData);
    }
}