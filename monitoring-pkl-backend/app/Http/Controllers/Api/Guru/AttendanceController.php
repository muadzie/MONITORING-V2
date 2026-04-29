<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Placement;
use App\Models\User;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Ambil semua siswa yang dibimbing oleh guru ini
        // Asumsi: relasi teacher_id di tabel users
        $studentIds = User::where('teacher_id', $user->id)->pluck('id');
        
        if ($studentIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }
        
        // Ambil data absensi siswa tersebut
        $attendances = Attendance::whereIn('user_id', $studentIds)
            ->with(['student' => function($q) {
                $q->select('id', 'name', 'nisn', 'class_id');
                $q->with('class:id,name');
            }, 'company'])
            ->orderBy('date', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }
    
    public function summary(Request $request)
    {
        $user = auth()->user();
        $studentIds = User::where('teacher_id', $user->id)->pluck('id');
        
        $summary = [
            'total_present' => Attendance::whereIn('user_id', $studentIds)->where('status', 'present')->count(),
            'total_late' => Attendance::whereIn('user_id', $studentIds)->where('status', 'late')->count(),
            'total_absent' => Attendance::whereIn('user_id', $studentIds)->where('status', 'absent')->count(),
            'total_sick' => Attendance::whereIn('user_id', $studentIds)->where('status', 'sick')->count(),
            'total_permit' => Attendance::whereIn('user_id', $studentIds)->where('status', 'permit')->count(),
        ];
        
        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
    
    // Ambil daftar siswa bimbingan
    public function students(Request $request)
    {
        $user = auth()->user();
        
        $students = User::where('teacher_id', $user->id)
            ->where('role_id', 3) // role siswa
            ->select('id', 'name', 'nisn', 'class_id')
            ->with('class:id,name')
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }
}