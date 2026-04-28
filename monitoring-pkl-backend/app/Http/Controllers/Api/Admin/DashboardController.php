<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\Permission;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Statistik utama dashboard
    public function stats()
    {
        return response()->json([
            'total_students' => User::where('role_id', 2)->count(),
            'total_teachers' => User::where('role_id', 3)->count(),
            'total_companies' => Company::count(),
            'total_logbooks' => Logbook::count(),
            'pending_logbooks' => Logbook::where('status', 'pending')->count(),
        ]);
    }

    // Statistik kehadiran hari ini
    public function attendanceStats()
    {
        $today = Carbon::today();
        
        $stats = [
            'present' => Attendance::whereDate('date', $today)->where('status', 'present')->count(),
            'late' => Attendance::whereDate('date', $today)->where('status', 'late')->count(),
            'absent' => Attendance::whereDate('date', $today)->where('status', 'absent')->count(),
            'sick' => Attendance::whereDate('date', $today)->where('status', 'sick')->count(),
            'permit' => Attendance::whereDate('date', $today)->where('status', 'permit')->count(),
        ];
        
        return response()->json($stats);
    }

    // Aktivitas terbaru
    public function recentActivities()
    {
        // Gabungkan dari berbagai sumber
        $attendances = Attendance::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'attendance',
                    'description' => $this->getAttendanceStatusText($item->status),
                    'user' => $item->user->name,
                    'user_name' => $item->user->name,
                    'time' => $item->created_at->diffForHumans(),
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'statusClass' => $this->getStatusClass($item->status)
                ];
            });
        
        $logbooks = Logbook::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'logbook',
                    'description' => "Membuat logbook: {$item->activity}",
                    'user' => $item->user->name,
                    'user_name' => $item->user->name,
                    'time' => $item->created_at->diffForHumans(),
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'statusClass' => $this->getLogbookStatusClass($item->status)
                ];
            });
        
        $permissions = Permission::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'type' => 'permission',
                    'description' => "Mengajukan {$item->type}",
                    'user' => $item->user->name,
                    'user_name' => $item->user->name,
                    'time' => $item->created_at->diffForHumans(),
                    'created_at' => $item->created_at,
                    'status' => $item->status,
                    'statusClass' => $this->getPermissionStatusClass($item->status)
                ];
            });
        
        // Gabungkan dan urutkan
        $activities = $attendances->concat($logbooks)->concat($permissions);
        $activities = $activities->sortByDesc('created_at')->take(10)->values();
        
        return response()->json($activities);
    }

    // Top siswa berdasarkan logbook
    public function topStudents()
    {
        $topStudents = User::where('role_id', 2)
            ->with('company')
            ->withCount('logbooks')
            ->orderBy('logbooks_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function($student) {
                // Hitung progress (target 30 logbook)
                $progress = min(100, round(($student->logbooks_count / 30) * 100));
                
                // Hitung rata-rata nilai
                $avgGrade = $student->logbooks()->avg('grade');
                
                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'nisn' => $student->nisn,
                    'company' => $student->company ? $student->company->name : '-',
                    'logbook_count' => $student->logbooks_count,
                    'avg_grade' => $avgGrade ? round($avgGrade, 2) : 0,
                    'progress' => $progress
                ];
            });
        
        return response()->json($topStudents);
    }

    // Helper functions
    private function getAttendanceStatusText($status)
    {
        $map = [
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Alpha',
            'sick' => 'Sakit',
            'permit' => 'Izin'
        ];
        return $map[$status] ?? $status;
    }

    private function getStatusClass($status)
    {
        $map = [
            'present' => 'bg-green-100 text-green-800',
            'late' => 'bg-yellow-100 text-yellow-800',
            'absent' => 'bg-red-100 text-red-800',
            'sick' => 'bg-blue-100 text-blue-800',
            'permit' => 'bg-purple-100 text-purple-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800'
        ];
        return $map[$status] ?? 'bg-gray-100 text-gray-800';
    }

    private function getLogbookStatusClass($status)
    {
        $map = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];
        return $map[$status] ?? 'bg-gray-100 text-gray-800';
    }

    private function getPermissionStatusClass($status)
    {
        $map = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'approved' => 'bg-green-100 text-green-800',
            'rejected' => 'bg-red-100 text-red-800'
        ];
        return $map[$status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getAllData()
{
    $today = Carbon::today();
    
    return response()->json([
        'stats' => [
            'total_students' => User::where('role_id', 2)->count(),
            'total_teachers' => User::where('role_id', 3)->count(),
            'total_companies' => Company::count(),
            'total_logbooks' => Logbook::count(),
            'pending_logbooks' => Logbook::where('status', 'pending')->count(),
        ],
        'attendance_stats' => [
            'present' => Attendance::whereDate('date', $today)->where('status', 'present')->count(),
            'late' => Attendance::whereDate('date', $today)->where('status', 'late')->count(),
            'absent' => Attendance::whereDate('date', $today)->where('status', 'absent')->count(),
            'sick' => Attendance::whereDate('date', $today)->where('status', 'sick')->count(),
            'permit' => Attendance::whereDate('date', $today)->where('status', 'permit')->count(),
        ],
        'recent_activities' => $this->getRecentActivities(),
        'top_students' => $this->getTopStudents()
    ]);
}

private function getRecentActivities()
{
    // Gabungkan dari attendance, logbook, permission
    $activities = collect();
    
    $attendances = Attendance::with('user')->latest()->limit(5)->get()->map(function($item) {
        return [
            'id' => $item->id,
            'type' => 'attendance',
            'description' => $this->getStatusText($item->status),
            'user' => $item->user->name,
            'time' => $item->created_at->diffForHumans(),
            'created_at' => $item->created_at,
        ];
    });
    
    $logbooks = Logbook::with('user')->latest()->limit(5)->get()->map(function($item) {
        return [
            'id' => $item->id,
            'type' => 'logbook',
            'description' => "Membuat logbook: {$item->activity}",
            'user' => $item->user->name,
            'time' => $item->created_at->diffForHumans(),
            'created_at' => $item->created_at,
        ];
    });
    
    return $attendances->concat($logbooks)->sortByDesc('created_at')->take(10)->values();
}

private function getTopStudents()
{
    return User::where('role_id', 2)
        ->with('company')
        ->withCount('logbooks')
        ->orderBy('logbooks_count', 'desc')
        ->limit(10)
        ->get()
        ->map(function($student) {
            return [
                'id' => $student->id,
                'name' => $student->name,
                'company' => $student->company->name ?? '-',
                'logbook_count' => $student->logbooks_count,
                'avg_grade' => round($student->logbooks()->avg('grade') ?? 0, 2),
                'progress' => min(100, round(($student->logbooks_count / 30) * 100))
            ];
        });
}

private function getStatusText($status)
{
    $map = ['present' => 'Hadir', 'late' => 'Terlambat', 'absent' => 'Alpha', 'sick' => 'Sakit', 'permit' => 'Izin'];
    return $map[$status] ?? $status;
}
}