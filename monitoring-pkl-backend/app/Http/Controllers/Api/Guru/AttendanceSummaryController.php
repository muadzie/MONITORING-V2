<?php

namespace App\Http\Controllers\Api\Guru;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Assessment;
use App\Models\Placement;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceSummaryController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Ambil parameter filter
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $angkatan = $request->get('angkatan');
        
        // Jika tidak ada range tanggal, gunakan bulan dan tahun
        if (!$startDate || !$endDate) {
            $month = $request->get('month', Carbon::now()->month);
            $year = $request->get('year', Carbon::now()->year);
            $startDate = Carbon::create($year, $month, 1)->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::create($year, $month, 1)->endOfMonth()->format('Y-m-d');
        }
        
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        
        // Ambil semua siswa yang dibimbing guru ini
        $students = User::where('role_id', 2)
            ->where('teacher_id', $user->id)
            ->with(['class', 'company', 'placements' => function($q) {
                $q->where('status', 'active');
            }])
            ->get();
        
        // Jika ada filter angkatan, filter berdasarkan data yang ada
        if ($angkatan && !empty($angkatan)) {
            $students = $students->filter(function($student) use ($angkatan) {
                // Metode 1: Dari NISN (4 digit pertama)
                $nisnYear = substr($student->nisn, 0, 4);
                
                // Metode 2: Dari kelas (XII = 2024, XI = 2025, X = 2026)
                $kelasYear = $this->getTahunDariKelas($student->class->name ?? '');
                
                // Metode 3: Dari tanggal mulai PKL
                $placementYear = null;
                if ($student->placements->first()) {
                    $placementYear = Carbon::parse($student->placements->first()->start_date)->year;
                }
                
                $match = ($nisnYear == $angkatan) || ($kelasYear == $angkatan) || ($placementYear == $angkatan);
                
                // Debug log
                \Log::info("Filter angkatan: {$angkatan} | Student: {$student->name} | NISN: {$nisnYear} | Kelas: {$kelasYear} | Placement: {$placementYear} | Match: " . ($match ? 'YES' : 'NO'));
                
                return $match;
            });
        }
        
        // Hitung hari kerja
        $workingDays = 0;
        $currentDate = clone $start;
        while ($currentDate <= $end) {
            if ($currentDate->dayOfWeek != 0 && $currentDate->dayOfWeek != 6) {
                $workingDays++;
            }
            $currentDate->addDay();
        }
        
        // Ambil attendance
        $attendances = Attendance::whereIn('user_id', $students->pluck('id'))
            ->whereBetween('date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get()
            ->groupBy('user_id');
        
        // Ambil assessments
        $assessments = Assessment::whereIn('student_id', $students->pluck('id'))
            ->where('assessor_type', 'guru')
            ->get()
            ->keyBy('student_id');
        
        $summary = [];
        $no = 1;
        
        foreach ($students as $student) {
            $studentAttendances = $attendances->get($student->id, collect());
            
            $hadir = $studentAttendances->where('status', 'present')->count();
            $terlambat = $studentAttendances->where('status', 'late')->count();
            $alpha = $studentAttendances->where('status', 'absent')->count();
            $sakit = $studentAttendances->where('status', 'sick')->count();
            $izin = $studentAttendances->where('status', 'permit')->count();
            
            // Hitung alpha untuk hari yang tidak ada record
            $attendanceDays = $studentAttendances->pluck('date')->toArray();
            $alphaDays = 0;
            $currentDateLoop = clone $start;
            while ($currentDateLoop <= $end) {
                if ($currentDateLoop->dayOfWeek != 0 && $currentDateLoop->dayOfWeek != 6) {
                    $dateStr = $currentDateLoop->format('Y-m-d');
                    if (!in_array($dateStr, $attendanceDays)) {
                        $alphaDays++;
                    }
                }
                $currentDateLoop->addDay();
            }
            
            $totalAlpha = $alpha + $alphaDays;
            $totalKehadiran = $hadir + $terlambat + $sakit + $izin;
            $persentase = $workingDays > 0 ? round(($totalKehadiran / $workingDays) * 100) : 0;
            
            $assessment = $assessments->get($student->id);
            $rataNilai = $assessment ? $assessment->final_score : '-';
            
            $summary[] = [
                'no' => $no++,
                'nisn' => $student->nisn,
                'nama_siswa' => $student->name,
                'kelas' => $student->class->name ?? '-',
                'perusahaan' => $student->company->name ?? '-',
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'alpha' => $totalAlpha,
                'sakit' => $sakit,
                'izin' => $izin,
                'persentase' => $persentase,
                'rata_nilai' => $rataNilai,
            ];
        }
        
        return response()->json([
            'success' => true,
            'data' => $summary,
            'meta' => [
                'start_date' => $start->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'total_siswa' => $students->count(),
                'total_hari_kerja' => $workingDays,
            ]
        ]);
    }
    
    private function getTahunDariKelas($kelas)
    {
        // Mapping kelas ke tahun masuk
        // Asumsi: kelas XII lulus 2026 (masuk 2024)
        // kelas XI lulus 2027 (masuk 2025)
        // kelas X lulus 2028 (masuk 2026)
        if (strpos($kelas, 'XII') !== false) return 2024;
        if (strpos($kelas, 'XI') !== false) return 2025;
        if (strpos($kelas, 'X') !== false) return 2026;
        return null;
    }
}