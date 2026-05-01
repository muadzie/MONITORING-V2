<?php
// app/Http/Controllers/Api/Admin/AssessmentController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Logbook;
use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AssessmentController extends Controller
{
    /**
     * Get students list for assessment
     */
   public function getStudentsForAssessment(Request $request)
{
    try {
        $students = User::where('role_id', 2)
            ->with(['company', 'teacher', 'class'])
            ->select('id', 'name', 'nisn', 'email', 'kelas', 'class_id', 'company_id', 'teacher_id')
            ->get();
        
        $result = [];
        foreach ($students as $student) {
            // Ambil assessment dari guru
            $guruAssessment = Assessment::where('student_id', $student->id)
                ->where('assessor_type', 'guru')
                ->first();
            
            // Ambil assessment dari perusahaan
            $perusahaanAssessment = Assessment::where('student_id', $student->id)
                ->where('assessor_type', 'perusahaan')
                ->first();
            
            // LOG untuk debugging
            Log::info('Student: ' . $student->name . ' (ID: ' . $student->id . ')');
            Log::info('Perusahaan Assessment found: ' . ($perusahaanAssessment ? 'YES' : 'NO'));
            if ($perusahaanAssessment) {
                Log::info('Perusahaan Score: ' . $perusahaanAssessment->performance_score);
            }
            
            // Hitung nilai guru
            $guruScore = null;
            if ($guruAssessment) {
                $guruScores = [];
                if ($guruAssessment->attendance_score) $guruScores[] = $guruAssessment->attendance_score;
                if ($guruAssessment->logbook_score) $guruScores[] = $guruAssessment->logbook_score;
                if ($guruAssessment->report_score) $guruScores[] = $guruAssessment->report_score;
                if ($guruAssessment->attitude_score) $guruScores[] = $guruAssessment->attitude_score;
                if (count($guruScores) > 0) {
                    $guruScore = round(array_sum($guruScores) / count($guruScores), 2);
                }
            }
            
            // Hitung nilai perusahaan
            $perusahaanScore = null;
            if ($perusahaanAssessment) {
                // Ambil dari performance_score atau final_score
                $perusahaanScore = $perusahaanAssessment->performance_score ?? $perusahaanAssessment->final_score;
            } else {
                // Fallback: cek dari logbook
                $avgLogbookGrade = Logbook::where('user_id', $student->id)
                    ->whereNotNull('grade')
                    ->avg('grade');
                if ($avgLogbookGrade) {
                    $perusahaanScore = round($avgLogbookGrade, 2);
                }
            }
            
            // Nilai akhir (rata-rata guru dan perusahaan)
            $finalScore = null;
            if ($guruScore && $perusahaanScore) {
                $finalScore = round(($guruScore + $perusahaanScore) / 2, 2);
            } elseif ($guruScore) {
                $finalScore = $guruScore;
            } elseif ($perusahaanScore) {
                $finalScore = $perusahaanScore;
            }
            
            $result[] = [
                'id' => $student->id,
                'nisn' => $student->nisn ?? '-',
                'name' => $student->name ?? '-',
                'email' => $student->email ?? '-',
                'kelas' => $student->kelas ?? '-',
                'company_id' => $student->company_id,
                'company' => $student->company ? [
                    'id' => $student->company->id,
                    'name' => $student->company->name
                ] : null,
                'teacher_id' => $student->teacher_id,
                'teacher' => $student->teacher ? [
                    'id' => $student->teacher->id,
                    'name' => $student->teacher->name
                ] : null,
                
                // Nilai Guru
                'guru_score' => $guruScore,
                'guru_attendance' => $guruAssessment ? $guruAssessment->attendance_score : null,
                'guru_logbook' => $guruAssessment ? $guruAssessment->logbook_score : null,
                'guru_report' => $guruAssessment ? $guruAssessment->report_score : null,
                'guru_attitude' => $guruAssessment ? $guruAssessment->attitude_score : null,
                'guru_notes' => $guruAssessment ? $guruAssessment->notes : null,
                
                // Nilai Perusahaan
                'perusahaan_score' => $perusahaanScore, // INI YANG PENTING
                'perusahaan_performance' => $perusahaanAssessment ? $perusahaanAssessment->performance_score : null,
                'perusahaan_discipline' => $perusahaanAssessment ? $perusahaanAssessment->discipline_score : null,
                'perusahaan_notes' => $perusahaanAssessment ? $perusahaanAssessment->notes : null,
                
                // Nilai Akhir
                'final_score' => $finalScore,
            ];
        }
        
        return response()->json($result);
        
    } catch (\Exception $e) {
        Log::error('Get students error: ' . $e->getMessage());
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
    
    /**
     * Store or update assessment
     */
    public function store(Request $request, $id)
    {
        try {
            Log::info('Saving assessment for student: ' . $id);
            Log::info('Request data: ', $request->all());
            
            // Validasi
            $request->validate([
                'guru.attendance' => 'nullable|numeric|min:0|max:100',
                'guru.logbook' => 'nullable|numeric|min:0|max:100',
                'guru.report' => 'nullable|numeric|min:0|max:100',
                'guru.attitude' => 'nullable|numeric|min:0|max:100',
                'guru.notes' => 'nullable|string',
                'perusahaan.performance' => 'nullable|numeric|min:0|max:100',
                'perusahaan.discipline' => 'nullable|numeric|min:0|max:100',
                'perusahaan.notes' => 'nullable|string',
                'final_score' => 'nullable|numeric|min:0|max:100'
            ]);
            
            // Cek apakah siswa ada
            $student = User::where('role_id', 2)->findOrFail($id);
            
            // SIMPAN PENILAIAN GURU
            $guruScores = [];
            if ($request->has('guru.attendance') && $request->input('guru.attendance') !== '') 
                $guruScores[] = (float)$request->input('guru.attendance');
            if ($request->has('guru.logbook') && $request->input('guru.logbook') !== '') 
                $guruScores[] = (float)$request->input('guru.logbook');
            if ($request->has('guru.report') && $request->input('guru.report') !== '') 
                $guruScores[] = (float)$request->input('guru.report');
            if ($request->has('guru.attitude') && $request->input('guru.attitude') !== '') 
                $guruScores[] = (float)$request->input('guru.attitude');
            
            $guruFinalScore = count($guruScores) > 0 ? round(array_sum($guruScores) / count($guruScores), 2) : null;
            
            // Simpan assessment guru
            if (count($guruScores) > 0 || $request->input('guru.notes')) {
                Assessment::updateOrCreate(
                    [
                        'student_id' => $id,
                        'assessor_type' => 'guru'
                    ],
                    [
                        'assessor_id' => auth()->id(),
                        'attendance_score' => $request->input('guru.attendance'),
                        'logbook_score' => $request->input('guru.logbook'),
                        'report_score' => $request->input('guru.report'),
                        'attitude_score' => $request->input('guru.attitude'),
                        'final_score' => $guruFinalScore,
                        'notes' => $request->input('guru.notes')
                    ]
                );
            }
            
            // SIMPAN PENILAIAN PERUSAHAAN
            $perusahaanScores = [];
            if ($request->has('perusahaan.performance') && $request->input('perusahaan.performance') !== '') 
                $perusahaanScores[] = (float)$request->input('perusahaan.performance');
            if ($request->has('perusahaan.discipline') && $request->input('perusahaan.discipline') !== '') 
                $perusahaanScores[] = (float)$request->input('perusahaan.discipline');
            
            $perusahaanFinalScore = count($perusahaanScores) > 0 ? round(array_sum($perusahaanScores) / count($perusahaanScores), 2) : null;
            
            // Simpan assessment perusahaan
            if (count($perusahaanScores) > 0 || $request->input('perusahaan.notes')) {
                Assessment::updateOrCreate(
                    [
                        'student_id' => $id,
                        'assessor_type' => 'perusahaan'
                    ],
                    [
                        'assessor_id' => auth()->id(),
                        'performance_score' => $request->input('perusahaan.performance'),
                        'discipline_score' => $request->input('perusahaan.discipline'),
                        'final_score' => $perusahaanFinalScore,
                        'notes' => $request->input('perusahaan.notes')
                    ]
                );
            }
            
            // SIMPAN NILAI AKHIR (opsional, bisa di salah satu assessment atau terpisah)
            if ($request->has('final_score')) {
                // Bisa simpan di tabel terpisah atau update di salah satu assessment
                // Untuk sementara, simpan di assessment guru
                Assessment::updateOrCreate(
                    [
                        'student_id' => $id,
                        'assessor_type' => 'guru'
                    ],
                    [
                        'final_score' => $request->input('final_score')
                    ]
                );
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Penilaian berhasil disimpan'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Store assessment error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal menyimpan penilaian: ' . $e->getMessage()
            ], 500);
        }
    }

    
    
    /**
     * Get assessment report
     */
    public function report(Request $request)
    {
        try {
            $assessments = Assessment::with(['student', 'student.company', 'student.teacher'])
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $assessments
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 500);
        }
    }
}