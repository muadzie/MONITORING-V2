<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $students = User::where('teacher_id', $request->user()->id)
            ->where('role_id', 2)
            ->with('assessments')
            ->get();
        
        return response()->json($students);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'attendance_score' => 'nullable|integer|min:0|max:100',
            'logbook_score' => 'nullable|integer|min:0|max:100',
            'report_score' => 'nullable|integer|min:0|max:100',
            'attitude_score' => 'nullable|integer|min:0|max:100',
            'notes' => 'nullable|string'
        ]);

        // Hitung nilai akhir
        $scores = [
            $request->attendance_score,
            $request->logbook_score,
            $request->report_score,
            $request->attitude_score
        ];
        $validScores = array_filter($scores);
        $finalScore = !empty($validScores) ? array_sum($validScores) / count($validScores) : null;

        $assessment = Assessment::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'assessor_id' => $request->user()->id,
                'assessor_type' => 'guru'
            ],
            [
                'attendance_score' => $request->attendance_score,
                'logbook_score' => $request->logbook_score,
                'report_score' => $request->report_score,
                'attitude_score' => $request->attitude_score,
                'final_score' => $finalScore,
                'notes' => $request->notes
            ]
        );

        // Notifikasi ke siswa
        Notification::create([
            'user_id' => $request->student_id,
            'title' => 'Penilaian PKL',
            'message' => 'Nilai PKL Anda telah diinput oleh guru pembimbing',
            'type' => 'success',
            'url' => '/siswa/assessment'
        ]);

        return response()->json($assessment);
    }
}