<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $user = Auth::user();
        $role = $user->role->name;
        
        $stats = [];
        
        if ($role === 'Admin') {
            // Stats untuk Admin
            $stats = [
                'total_students' => User::where('role_id', 2)->count(),
                'total_companies' => \App\Models\Company::count(),
                'present_today' => Attendance::whereDate('date', Carbon::today())->where('status', 'present')->count(),
                'total_logbooks' => Logbook::count(),
                'present_percentage' => $this->getAttendancePercentage(),
                'total_logbooks' => Logbook::count(),
                'total_permissions' => \App\Models\Permission::count(),
                'average_grade' => round(Logbook::avg('grade') ?? 0, 2),
            ];
        } elseif ($role === 'Guru') {
            // Stats untuk Guru (siswa bimbingan)
            $studentIds = User::where('teacher_id', $user->id)->pluck('id');
            $stats = [
                'total_students' => $studentIds->count(),
                'present_today' => Attendance::whereIn('user_id', $studentIds)->whereDate('date', Carbon::today())->where('status', 'present')->count(),
                'total_logbooks' => Logbook::whereIn('user_id', $studentIds)->count(),
                'present_percentage' => $this->getAttendancePercentage($studentIds),
                'total_logbooks' => Logbook::whereIn('user_id', $studentIds)->count(),
                'total_permissions' => \App\Models\Permission::whereIn('user_id', $studentIds)->count(),
                'average_grade' => round(Logbook::whereIn('user_id', $studentIds)->avg('grade') ?? 0, 2),
            ];
        } elseif ($role === 'Siswa') {
            // Stats untuk Siswa
            $stats = [
                'present_percentage' => $this->getStudentAttendancePercentage($user->id),
                'total_logbooks' => Logbook::where('user_id', $user->id)->count(),
                'total_permissions' => \App\Models\Permission::where('user_id', $user->id)->count(),
                'average_grade' => round(Logbook::where('user_id', $user->id)->avg('grade') ?? 0, 2),
            ];
        } elseif ($role === 'Perusahaan') {
            // Stats untuk Perusahaan
            $studentIds = User::where('company_id', $user->company_id)->pluck('id');
            $stats = [
                'total_students' => $studentIds->count(),
                'total_logbooks' => Logbook::whereIn('user_id', $studentIds)->count(),
                'average_grade' => round(Logbook::whereIn('user_id', $studentIds)->avg('grade') ?? 0, 2),
            ];
        }
        
        return response()->json($stats);
    }
    
    private function getAttendancePercentage($studentIds = null)
    {
        $query = Attendance::whereMonth('date', Carbon::now()->month);
        
        if ($studentIds) {
            $query->whereIn('user_id', $studentIds);
        }
        
        $total = $query->count();
        $present = $query->where('status', 'present')->count();
        
        if ($total == 0) return 0;
        
        return round(($present / $total) * 100, 2);
    }
    
    private function getStudentAttendancePercentage($userId)
    {
        $total = Attendance::where('user_id', $userId)->whereMonth('date', Carbon::now()->month)->count();
        $present = Attendance::where('user_id', $userId)->whereMonth('date', Carbon::now()->month)->where('status', 'present')->count();
        
        if ($total == 0) return 0;
        
        return round(($present / $total) * 100, 2);
    }
}