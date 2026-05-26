<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MonitoringController extends Controller
{
    public function index()
    {
        try {
            $today = Carbon::today();

            $students = User::where('role_id', 2)
                ->with(['company', 'class'])
                ->withCount(['logbooks'])
                ->withAvg('logbooks as avg_grade', 'grade')
                ->get();

            $todayAttendances = Attendance::whereIn('user_id', $students->pluck('id'))
                ->whereDate('date', $today)
                ->get()
                ->keyBy('user_id');

            $result = $students->map(function ($student) use ($todayAttendances) {
                $attendance = $todayAttendances->get($student->id);

                return [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class_id' => $student->class_id,
                    'class' => $student->class,
                    'company_id' => $student->company_id,
                    'company' => $student->company,
                    'today_status' => $attendance ? $attendance->status : 'absent',
                    'logbook_count' => (int) $student->logbooks_count,
                    'avg_grade' => $student->avg_grade ? round((float) $student->avg_grade, 2) : 0,
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan internal'
            ], 500);
        }
    }

    public function progress(Request $request)
    {
        try {
            $query = User::where('role_id', 2)
                ->with(['company', 'class', 'teacher'])
                ->withCount(['logbooks'])
                ->withAvg('logbooks as avg_grade', 'grade');

            if ($request->class_id) {
                $query->where('class_id', $request->class_id);
            }

            if ($request->company_id) {
                $query->where('company_id', $request->company_id);
            }

            $students = $query->get();

            $result = $students->map(function ($student) {
                $logbookCount = (int) $student->logbooks_count;
                $avgGrade = $student->avg_grade;

                return [
                    'id' => $student->id,
                    'nisn' => $student->nisn,
                    'name' => $student->name,
                    'class' => $student->class,
                    'company' => $student->company,
                    'teacher' => $student->teacher,
                    'logbook_count' => $logbookCount,
                    'progress' => min(100, round(($logbookCount / 30) * 100)),
                    'avg_grade' => $avgGrade ? round((float) $avgGrade, 2) : 0,
                ];
            });

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan internal'], 500);
        }
    }
    
    public function attendance(Request $request)
    {
        try {
            $query = Attendance::with(['user', 'user.company', 'user.class'])  // ← TAMBAHKAN RELASI
                ->whereBetween('date', [$request->start_date ?? '2026-01-01', $request->end_date ?? Carbon::now()]);
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            $attendances = $query->orderBy('date', 'desc')->get();
            
            // Format response dengan data lengkap
            $formattedData = $attendances->map(function($attendance) {
                return [
                    'id' => $attendance->id,
                    'date' => $attendance->date,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'status' => $attendance->status,
                    'notes' => $attendance->notes,
                    'photo' => $attendance->photo,
                    'photo_out' => $attendance->photo_out,
                    'user' => $attendance->user ? [
                        'id' => $attendance->user->id,
                        'name' => $attendance->user->name,
                        'nisn' => $attendance->user->nisn,
                        'kelas' => $attendance->user->kelas,
                        'email' => $attendance->user->email,
                    ] : null,
                    'company' => $attendance->user && $attendance->user->company ? [
                        'id' => $attendance->user->company->id,
                        'name' => $attendance->user->company->name,
                        'address' => $attendance->user->company->address,
                    ] : null,
                    'class' => $attendance->user && $attendance->user->class ? [
                        'id' => $attendance->user->class->id,
                        'name' => $attendance->user->class->name,
                        'jurusan' => $attendance->user->class->jurusan,
                    ] : null,
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $formattedData
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
   public function logbook(Request $request)
{
    try {
        // Load relasi user beserta company dan class
        $query = Logbook::with(['user', 'user.company', 'user.class'])
            ->whereBetween('date', [$request->start_date ?? '2026-01-01', $request->end_date ?? Carbon::now()]);
        
        if ($request->status) {
            $query->where('status', $request->status);
        }
        
        $logbooks = $query->orderBy('date', 'desc')->get();
        
        // Format response dengan data lengkap termasuk kelas
        $formattedData = $logbooks->map(function($logbook) {
            return [
                'id' => $logbook->id,
                'date' => $logbook->date,
                'activity' => $logbook->activity,
                'description' => $logbook->description,
                'status' => $logbook->status,
                'grade' => $logbook->grade,
                'feedback' => $logbook->feedback,
                'attachment' => $logbook->attachment,
                'created_at' => $logbook->created_at,
                'user' => $logbook->user ? [
                    'id' => $logbook->user->id,
                    'name' => $logbook->user->name,
                    'nisn' => $logbook->user->nisn,
                    // Ambil kelas dari relasi class atau langsung dari field kelas
                    'kelas' => $logbook->user->class ? $logbook->user->class->name : ($logbook->user->kelas ?? '-'),
                    'email' => $logbook->user->email,
                    'phone' => $logbook->user->phone,
                ] : null,
                'company' => $logbook->user && $logbook->user->company ? [
                    'id' => $logbook->user->company->id,
                    'name' => $logbook->user->company->name,
                    'address' => $logbook->user->company->address,
                ] : null,
                'class' => $logbook->user && $logbook->user->class ? [
                    'id' => $logbook->user->class->id,
                    'name' => $logbook->user->class->name,
                    'jurusan' => $logbook->user->class->jurusan,
                ] : null,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedData
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function show($id)
    {
        try {
            $student = User::where('role_id', 2)
                ->with(['company', 'class'])
                ->withCount(['logbooks'])
                ->withAvg('logbooks as avg_grade', 'grade')
                ->findOrFail($id);

            $recentLogbooks = Logbook::where('user_id', $student->id)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get();

            return response()->json([
                'id' => $student->id,
                'nisn' => $student->nisn,
                'name' => $student->name,
                'class' => $student->class,
                'company' => $student->company,
                'logbook_count' => (int) $student->logbooks_count,
                'avg_grade' => $student->avg_grade ? round((float) $student->avg_grade, 2) : 0,
                'recent_logbooks' => $recentLogbooks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan internal'
            ], 500);
        }
    }

    public function downloadAttachment($id)
    {
        try {
            $logbook = Logbook::findOrFail($id);

            if (!$logbook->attachment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak ada lampiran'
                ], 404);
            }

            if (!Storage::disk('public')->exists($logbook->attachment)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File lampiran tidak ditemukan'
                ], 404);
            }

            $filePath = Storage::disk('public')->path($logbook->attachment);
            $fileName = basename($logbook->attachment);

            return response()->download($filePath, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendownload lampiran'
            ], 500);
        }
    }
}