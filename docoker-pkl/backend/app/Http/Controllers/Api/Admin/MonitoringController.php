<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
            // Ambil semua siswa dengan relasi
            $students = User::where('role_id', 2)
                ->with(['company', 'class'])
                ->get();
            
            $result = [];
            foreach ($students as $student) {
                // Status kehadiran hari ini
                $today = Carbon::today();
                $attendance = Attendance::where('user_id', $student->id)
                    ->whereDate('date', $today)
                    ->first();
                
                // Hitung total logbook
                $logbookCount = Logbook::where('user_id', $student->id)->count();
                
                // Hitung rata-rata nilai
                $avgGrade = Logbook::where('user_id', $student->id)->avg('grade');
                
                $result[] = [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class_id' => $student->class_id,
                    'class' => $student->class,
                    'company_id' => $student->company_id,
                    'company' => $student->company,
                    'today_status' => $attendance ? $attendance->status : 'absent',
                    'logbook_count' => $logbookCount,
                    'avg_grade' => $avgGrade ? round($avgGrade, 2) : 0,
                ];
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

       public function progress(Request $request)
    {
        try {
            $query = User::where('role_id', 2)
                ->with(['company', 'class']);
            
            if ($request->class_id) {
                $query->where('class_id', $request->class_id);
            }
            
            if ($request->company_id) {
                $query->where('company_id', $request->company_id);
            }
            
            $students = $query->get();
            
            $result = [];
            foreach ($students as $student) {
                $logbookCount = Logbook::where('user_id', $student->id)->count();
                $progress = min(100, round(($logbookCount / 30) * 100));
                $avgGrade = Logbook::where('user_id', $student->id)->avg('grade');
                
                $result[] = [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class' => $student->class,
                    'company' => $student->company,
                    'logbook_count' => $logbookCount,
                    'progress' => $progress,
                    'avg_grade' => $avgGrade ? round($avgGrade, 2) : 0,
                ];
            }
            
            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function attendance(Request $request)
    {
        try {
            $query = Attendance::with(['user', 'company'])
                ->whereBetween('date', [$request->start_date ?? '2026-01-01', $request->end_date ?? Carbon::now()]);
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            $attendances = $query->orderBy('date', 'desc')->get();
            
            return response()->json($attendances);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function logbook(Request $request)
    {
        try {
            $query = Logbook::with('user')
                ->whereBetween('date', [$request->start_date ?? '2026-01-01', $request->end_date ?? Carbon::now()]);
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            $logbooks = $query->orderBy('date', 'desc')->get();
            
            return response()->json($logbooks);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    
    
    public function show($id)
    {
        try {
            $student = User::where('role_id', 2)
                ->with(['company', 'class'])
                ->findOrFail($id);
            
            // Recent logbooks
            $recentLogbooks = Logbook::where('user_id', $student->id)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();
            
            // Logbook count
            $logbookCount = Logbook::where('user_id', $student->id)->count();
            
            // Rata-rata nilai
            $avgGrade = Logbook::where('user_id', $student->id)->avg('grade');
            
            return response()->json([
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'class' => $student->class,
                'company' => $student->company,
                'logbook_count' => $logbookCount,
                'avg_grade' => $avgGrade ? round($avgGrade, 2) : 0,
                'recent_logbooks' => $recentLogbooks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}