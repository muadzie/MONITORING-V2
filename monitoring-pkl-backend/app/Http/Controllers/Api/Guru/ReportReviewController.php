<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class ReportReviewController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Ambil semua siswa yang dibimbing oleh guru ini
        $studentIds = User::where('teacher_id', $user->id)->pluck('id');
        
        // Ambil laporan dari siswa-siswa tersebut
        $reports = Report::whereIn('user_id', $studentIds)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($report) {
                return [
                    'id' => $report->id,
                    'student' => [
                        'id' => $report->user->id,
                        'name' => $report->user->name,
                    ],
                    'file_name' => $report->file_name,
                    'file_url' => asset('storage/' . $report->file_path),
                    'status' => $report->status,
                    'notes' => $report->notes,
                    'created_at' => $report->created_at,
                ];
            });
        
        return response()->json([
            'success' => true,
            'data' => $reports
        ]);
    }
    
    public function approve(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->status = 'approved';
        $report->notes = $request->notes;
        $report->save();
        
        return response()->json(['success' => true]);
    }
    
    public function reject(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $report->status = 'rejected';
        $report->notes = $request->notes;
        $report->save();
        
        return response()->json(['success' => true]);
    }
}