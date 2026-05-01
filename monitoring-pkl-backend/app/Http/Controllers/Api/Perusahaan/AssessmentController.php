<?php

namespace App\Http\Controllers\Api\Perusahaan;

use App\Http\Controllers\Controller;
use App\Models\Logbook;
use App\Models\User;
use App\Models\Assessment;
use App\Models\Placement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();
            $companyId = $user->company_id;
            
            // Ambil semua siswa yang magang di perusahaan ini
            $studentIds = Placement::where('company_id', $companyId)
                ->where('status', 'active')
                ->pluck('student_id');
            
            // Ambil semua logbook dari siswa-siswa tersebut
            $logbooks = Logbook::whereIn('user_id', $studentIds)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($logbook) {
                    return [
                        'id' => $logbook->id,
                        'student_id' => $logbook->user_id,
                        'student' => [
                            'id' => $logbook->user->id,
                            'name' => $logbook->user->name,
                            'nisn' => $logbook->user->nisn,
                        ],
                        'date' => $logbook->date,
                        'activity' => $logbook->activity,
                        'description' => $logbook->description,
                        'attachment' => $logbook->attachment,
                        'attachment_url' => $logbook->attachment ? asset('storage/' . $logbook->attachment) : null,
                        'grade' => $logbook->grade,
                        'feedback' => $logbook->feedback,
                        'created_at' => $logbook->created_at,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $logbooks
            ]);
            
        } catch (\Exception $e) {
            Log::error('Index error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function pending()
    {
        try {
            $user = auth()->user();
            $companyId = $user->company_id;
            
            $studentIds = Placement::where('company_id', $companyId)
                ->where('status', 'active')
                ->pluck('student_id');
            
            $logbooks = Logbook::whereIn('user_id', $studentIds)
                ->whereNull('grade')
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($logbook) {
                    return [
                        'id' => $logbook->id,
                        'student_id' => $logbook->user_id,
                        'student' => [
                            'id' => $logbook->user->id,
                            'name' => $logbook->user->name,
                            'nisn' => $logbook->user->nisn,
                        ],
                        'date' => $logbook->date,
                        'activity' => $logbook->activity,
                        'description' => $logbook->description,
                        'attachment' => $logbook->attachment,
                        'attachment_url' => $logbook->attachment ? asset('storage/' . $logbook->attachment) : null,
                        'created_at' => $logbook->created_at,
                    ];
                });
            
            return response()->json([
                'success' => true,
                'data' => $logbooks
            ]);
            
        } catch (\Exception $e) {
            Log::error('Pending error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function show($id)
    {
        try {
            $logbook = Logbook::with('user')->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $logbook->id,
                    'student_id' => $logbook->user_id,
                    'student' => [
                        'id' => $logbook->user->id,
                        'name' => $logbook->user->name,
                        'nisn' => $logbook->user->nisn,
                    ],
                    'date' => $logbook->date,
                    'activity' => $logbook->activity,
                    'description' => $logbook->description,
                    'attachment' => $logbook->attachment,
                    'attachment_url' => $logbook->attachment ? asset('storage/' . $logbook->attachment) : null,
                    'grade' => $logbook->grade,
                    'feedback' => $logbook->feedback,
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
    }
    
    public function grade(Request $request, $id)
    {
        try {
            Log::info('=== GRADE PROCESS START ===');
            Log::info('Request data:', $request->all());
            
            $request->validate([
                'grade' => 'required|integer|min:0|max:100',
                'feedback' => 'nullable|string'
            ]);
            
            $user = auth()->user();
            Log::info('User ID: ' . $user->id);
            Log::info('Company ID: ' . $user->company_id);
            
            $logbook = Logbook::findOrFail($id);
            Log::info('Logbook found:', [
                'id' => $logbook->id,
                'user_id' => $logbook->user_id,
                'current_grade' => $logbook->grade
            ]);
            
            // Update nilai di tabel logbooks
            $logbook->grade = $request->grade;
            $logbook->feedback = $request->feedback;
            $logbook->status = 'approved';
            $logbook->save();
            Log::info('Logbook updated');
            
            // ========== SINKRONKAN KE TABEL ASSESSMENTS ==========
            // Gunakan DB transaction untuk keamanan
            DB::beginTransaction();
            
            try {
                // Cari assessment yang sudah ada
                $assessment = Assessment::where('student_id', $logbook->user_id)
                    ->where('assessor_type', 'perusahaan')
                    ->first();
                
                if ($assessment) {
                    // Update yang sudah ada
                    $assessment->performance_score = $request->grade;
                    $assessment->discipline_score = $request->grade;
                    $assessment->notes = $request->feedback;
                    $assessment->final_score = $request->grade;
                    $assessment->save();
                    Log::info('Assessment UPDATED', ['id' => $assessment->id]);
                } else {
                    // Buat baru
                    $assessment = Assessment::create([
                        'student_id' => $logbook->user_id,
                        'assessor_id' => $user->id,
                        'assessor_type' => 'perusahaan',
                        'performance_score' => $request->grade,
                        'discipline_score' => $request->grade,
                        'notes' => $request->feedback,
                        'final_score' => $request->grade
                    ]);
                    Log::info('Assessment CREATED', ['id' => $assessment->id]);
                }
                
                DB::commit();
                Log::info('Assessment sync successful');
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Assessment sync failed: ' . $e->getMessage());
                throw $e;
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Penilaian berhasil disimpan dan disinkronkan',
                'data' => [
                    'logbook' => [
                        'id' => $logbook->id,
                        'grade' => $logbook->grade,
                        'feedback' => $logbook->feedback
                    ],
                    'assessment' => $assessment
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Grade error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function feedback(Request $request, $id)
    {
        try {
            $request->validate([
                'feedback' => 'required|string'
            ]);
            
            $logbook = Logbook::findOrFail($id);
            $logbook->feedback = $request->feedback;
            $logbook->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Feedback berhasil disimpan'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Feedback error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan feedback: ' . $e->getMessage()
            ], 500);
        }
    }
}