<?php
// app/Http/Controllers/Api/Admin/ReportController.php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Logbook;
use App\Models\User;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    /**
     * LAPORAN ABSENSI - REKAP PER SISWA
     * Download Excel
     */
    public function attendance(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            Log::info('Attendance report requested', [
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'company_id' => $request->company_id,
                'class_id' => $request->class_id
            ]);

            // Ambil data
            $data = $this->getAttendanceData($request);

            Log::info('Attendance data count', ['count' => count($data)]);

            // Jika tidak ada data, tetap buat Excel dengan pesan
            if (count($data) == 0) {
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setCellValue('A1', 'TIDAK ADA DATA');
                $sheet->setCellValue('A2', 'Periode: ' . $request->start_date . ' - ' . $request->end_date);
                $sheet->setCellValue('A3', 'Tidak ditemukan data absensi untuk periode tersebut');
                
                $writer = new Xlsx($spreadsheet);
                $filename = 'Laporan_Rekap_Absensi_Kosong_' . date('Y-m-d') . '.xlsx';
                $tempFile = tempnam(sys_get_temp_dir(), 'excel');
                $writer->save($tempFile);
                
                return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
            }
            
            // Buat Excel
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Title
            $sheet->setCellValue('A1', 'LAPORAN REKAP ABSENSI PKL');
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Periode: ' . Carbon::parse($request->start_date)->format('d/m/Y') . ' - ' . Carbon::parse($request->end_date)->format('d/m/Y'));
            $sheet->mergeCells('A2:K2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Headers
            $headers = ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Perusahaan', 'Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpha', 'Kehadiran (%)'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $sheet->getStyle($col . '4')->getFont()->setBold(true);
                $sheet->getStyle($col . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF2C3E50');
                $sheet->getStyle($col . '4')->getFont()->getColor()->setARGB('FFFFFFFF');
                $sheet->getStyle($col . '4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $col++;
            }

            // Data
            $row = 5;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nisn']);
                $sheet->setCellValue('C' . $row, $item['name']);
                $sheet->setCellValue('D' . $row, $item['class']);
                $sheet->setCellValue('E' . $row, $item['company']);
                $sheet->setCellValue('F' . $row, $item['hadir']);
                $sheet->setCellValue('G' . $row, $item['terlambat']);
                $sheet->setCellValue('H' . $row, $item['izin']);
                $sheet->setCellValue('I' . $row, $item['sakit']);
                $sheet->setCellValue('J' . $row, $item['alpha']);
                $sheet->setCellValue('K' . $row, $item['persentase'] . '%');

                // Color based on percentage
                if ($item['persentase'] >= 90) {
                    $sheet->getStyle('K' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF28A745');
                } elseif ($item['persentase'] >= 75) {
                    $sheet->getStyle('K' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFC107');
                } else {
                    $sheet->getStyle('K' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFDC3545');
                }
                $row++;
            }

            // Auto size columns
            foreach (range('A', 'K') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Add border
            $styleArray = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['argb' => 'FFDDDDDD'],
                    ],
                ],
            ];
            $sheet->getStyle('A4:' . $col . ($row - 1))->applyFromArray($styleArray);

            $writer = new Xlsx($spreadsheet);
            $filename = 'Laporan_Rekap_Absensi_' . date('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validasi gagal: ' . json_encode($e->errors())], 422);
        } catch (\Exception $e) {
            Log::error('Attendance export error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * PREVIEW JSON - Laporan Absensi
     */
    public function attendanceJson(Request $request)
    {
        try {
            $data = $this->getAttendanceData($request);
            return response()->json([
                'success' => true,
                'data' => $data,
                'count' => count($data)
            ]);
        } catch (\Exception $e) {
            Log::error('Attendance JSON error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get Attendance Data (digunakan untuk Excel dan JSON)
     */
    private function getAttendanceData(Request $request)
    {
        // Set default jika tidak ada
        $start_date = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $end_date = $request->end_date ?? Carbon::now()->toDateString();

        // Query siswa
        $query = User::where('role_id', 2); // role siswa

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }
        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        $students = $query->with(['company', 'class'])->get();

        $data = [];
        foreach ($students as $student) {
            // Hitung absensi
            $attendances = Attendance::where('user_id', $student->id)
                ->whereBetween('date', [$start_date, $end_date])
                ->get();

            $hadir = $attendances->where('status', 'present')->count();
            $terlambat = $attendances->where('status', 'late')->count();
            $izin = $attendances->where('status', 'permit')->count();
            $sakit = $attendances->where('status', 'sick')->count();
            $alpha = $attendances->where('status', 'absent')->count();
            $totalHadir = $hadir + $terlambat;
            $totalHari = $attendances->count();
            $persentase = $totalHari > 0 ? round(($totalHadir / $totalHari) * 100, 1) : 0;

            $data[] = [
                'nisn' => $student->nisn ?? '-',
                'name' => $student->name ?? '-',
                'kelas' => $student->class?->name ?? $student->kelas ?? '-',
                'perusahaan' => $student->company?->name ?? '-',
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'persentase' => $persentase,
            ];
        }

        return $data;
    }

    /**
     * LAPORAN LOGBOOK - REKAP PER SISWA
     */
    public function logbook(Request $request)
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date',
            ]);

            $data = $this->getLogbookData($request);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'LAPORAN REKAP LOGBOOK PKL');
            $sheet->mergeCells('A1:J1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $headers = ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Perusahaan', 'Total Logbook', 'Rata-rata Nilai', 'Menunggu', 'Disetujui', 'Ditolak'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '3', $header);
                $sheet->getStyle($col . '3')->getFont()->setBold(true);
                $sheet->getStyle($col . '3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF17A2B8');
                $sheet->getStyle($col . '3')->getFont()->getColor()->setARGB('FFFFFFFF');
                $col++;
            }

            $row = 4;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nisn']);
                $sheet->setCellValue('C' . $row, $item['name']);
                $sheet->setCellValue('D' . $row, $item['class']);
                $sheet->setCellValue('E' . $row, $item['company']);
                $sheet->setCellValue('F' . $row, $item['total_logbook']);
                $sheet->setCellValue('G' . $row, $item['avg_grade']);
                $sheet->setCellValue('H' . $row, $item['pending']);
                $sheet->setCellValue('I' . $row, $item['approved']);
                $sheet->setCellValue('J' . $row, $item['rejected']);
                $row++;
            }

            foreach (range('A', 'J') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Laporan_Rekap_Logbook_' . date('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Logbook export error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PREVIEW JSON - Laporan Logbook
     */
    public function logbookJson(Request $request)
    {
        try {
            $data = $this->getLogbookData($request);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get Logbook Data
     */
    private function getLogbookData(Request $request)
    {
        $start_date = $request->start_date ?? Carbon::now()->startOfMonth()->toDateString();
        $end_date = $request->end_date ?? Carbon::now()->toDateString();

        $query = User::where('role_id', 2);

        if ($request->filled('student_id')) {
            $query->where('id', $request->student_id);
        }

        $students = $query->with(['company', 'class'])->get();

        $data = [];
        foreach ($students as $student) {
            $logbooks = Logbook::where('user_id', $student->id)
                ->whereBetween('date', [$start_date, $end_date])
                ->get();

            $totalLogbook = $logbooks->count();
            $avgGrade = $logbooks->avg('grade');
            $pending = $logbooks->where('status', 'pending')->count();
            $approved = $logbooks->where('status', 'approved')->count();
            $rejected = $logbooks->where('status', 'rejected')->count();

            $data[] = [
                'nisn' => $student->nisn ?? '-',
                'name' => $student->name ?? '-',
                'kelas' => $student->class?->name ?? $student->kelas ?? '-',
                'perusahaan' => $student->company?->name ?? '-',
                'total_logbook' => $totalLogbook,
                'avg_grade' => $avgGrade ? round($avgGrade, 2) : 0,
                'pending' => $pending,
                'approved' => $approved,
                'rejected' => $rejected
            ];
        }

        return $data;
    }

    /**
     * LAPORAN NILAI AKHIR SISWA
     */
    public function grade(Request $request)
    {
        try {
            $data = $this->getGradeData($request);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'LAPORAN NILAI AKHIR PKL');
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $headers = ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Perusahaan', 'Nilai Akhir', 'Grade', 'Keterangan', 'Status'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '3', $header);
                $sheet->getStyle($col . '3')->getFont()->setBold(true);
                $sheet->getStyle($col . '3')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF6F42C1');
                $sheet->getStyle($col . '3')->getFont()->getColor()->setARGB('FFFFFFFF');
                $col++;
            }

            $row = 4;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nisn']);
                $sheet->setCellValue('C' . $row, $item['name']);
                $sheet->setCellValue('D' . $row, $item['class']);
                $sheet->setCellValue('E' . $row, $item['company']);
                $sheet->setCellValue('F' . $row, $item['final_grade']);
                $sheet->setCellValue('G' . $row, $item['grade_letter']);
                $sheet->setCellValue('H' . $row, $item['grade_description']);
                $sheet->setCellValue('I' . $row, $item['status']);
                $row++;
            }

            foreach (range('A', 'I') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Laporan_Nilai_Akhir_PKL_' . date('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Grade export error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PREVIEW JSON - Laporan Nilai
     */
    public function gradeJson(Request $request)
    {
        try {
            $data = $this->getGradeData($request);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get Grade Data
     */
    private function getGradeData(Request $request)
    {
        $students = User::where('role_id', 2)
            ->with(['company', 'class'])
            ->get();

        $data = [];
        foreach ($students as $student) {
            $avgGrade = Logbook::where('user_id', $student->id)->avg('grade');
            $finalGrade = $avgGrade ? round($avgGrade, 2) : 0;

            $gradeLetter = '';
            $gradeDescription = '';
            if ($finalGrade >= 90) {
                $gradeLetter = 'A';
                $gradeDescription = 'Sangat Baik';
            } elseif ($finalGrade >= 80) {
                $gradeLetter = 'B';
                $gradeDescription = 'Baik';
            } elseif ($finalGrade >= 70) {
                $gradeLetter = 'C';
                $gradeDescription = 'Cukup';
            } else {
                $gradeLetter = 'D';
                $gradeDescription = 'Kurang';
            }

            $data[] = [
                'nisn' => $student->nisn ?? '-',
                'name' => $student->name ?? '-',
                'kelas' => $student->class?->name ?? $student->kelas ?? '-',
                'perusahaan' => $student->company?->name ?? '-',
                'final_grade' => $finalGrade,
                'grade_letter' => $gradeLetter,
                'grade_description' => $gradeDescription,
                'status' => $finalGrade >= 70 ? 'LULUS' : 'TIDAK LULUS'
            ];
        }

        return $data;
    }

    /**
     * REKAP KESELURUHAN SISWA
     */
    public function summary(Request $request)
    {
        try {
            $data = $this->getSummaryData($request);

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'REKAP KESELURUHAN PKL');
            $sheet->mergeCells('A1:L1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Periode: ' . ($request->start_date ?? 'Semua Waktu') . ' - ' . ($request->end_date ?? 'Semua Waktu'));
            $sheet->mergeCells('A2:L2');
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $headers = ['No', 'NISN', 'Nama Siswa', 'Kelas', 'Perusahaan', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Total Logbook', 'Rata-rata Nilai', 'Nilai Akhir'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($col . '4', $header);
                $sheet->getStyle($col . '4')->getFont()->setBold(true);
                $sheet->getStyle($col . '4')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF343A40');
                $sheet->getStyle($col . '4')->getFont()->getColor()->setARGB('FFFFFFFF');
                $col++;
            }

            $row = 5;
            $no = 1;
            foreach ($data as $item) {
                $sheet->setCellValue('A' . $row, $no++);
                $sheet->setCellValue('B' . $row, $item['nisn']);
                $sheet->setCellValue('C' . $row, $item['name']);
                $sheet->setCellValue('D' . $row, $item['class']);
                $sheet->setCellValue('E' . $row, $item['company']);
                $sheet->setCellValue('F' . $row, $item['hadir']);
                $sheet->setCellValue('G' . $row, $item['izin']);
                $sheet->setCellValue('H' . $row, $item['sakit']);
                $sheet->setCellValue('I' . $row, $item['alpha']);
                $sheet->setCellValue('J' . $row, $item['total_logbook']);
                $sheet->setCellValue('K' . $row, $item['avg_grade']);
                $sheet->setCellValue('L' . $row, $item['final_grade']);
                $row++;
            }

            foreach (range('A', 'L') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'Rekap_Keseluruhan_PKL_' . date('Y-m-d') . '.xlsx';
            $tempFile = tempnam(sys_get_temp_dir(), 'excel');
            $writer->save($tempFile);

            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            Log::error('Summary export error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * PREVIEW JSON - Rekap Keseluruhan
     */
    public function summaryJson(Request $request)
    {
        try {
            $data = $this->getSummaryData($request);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Get Summary Data
     */
    private function getSummaryData(Request $request)
    {
        $start_date = $request->start_date ?? null;
        $end_date = $request->end_date ?? null;

        $query = User::where('role_id', 2);

        if ($request->filled('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $students = $query->with(['company', 'class'])->get();

        $data = [];
        foreach ($students as $student) {
            // Attendance query
            $attendanceQuery = Attendance::where('user_id', $student->id);
            if ($start_date && $end_date) {
                $attendanceQuery->whereBetween('date', [$start_date, $end_date]);
            }
            $attendances = $attendanceQuery->get();

            $hadir = $attendances->where('status', 'present')->count();
            $izin = $attendances->where('status', 'permit')->count();
            $sakit = $attendances->where('status', 'sick')->count();
            $alpha = $attendances->where('status', 'absent')->count();

            // Logbook query
            $logbookQuery = Logbook::where('user_id', $student->id);
            if ($start_date && $end_date) {
                $logbookQuery->whereBetween('date', [$start_date, $end_date]);
            }
            $logbooks = $logbookQuery->get();

            $totalLogbook = $logbooks->count();
            $avgGrade = $logbooks->avg('grade');
            $finalGrade = $avgGrade ? round($avgGrade, 2) : 0;

            $data[] = [
                'nisn' => $student->nisn ?? '-',
                'name' => $student->name ?? '-',
                'kelas' => $student->class?->name ?? $student->kelas ?? '-',
                'perusahaan' => $student->company?->name ?? '-',
                'hadir' => $hadir,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpha' => $alpha,
                'total_logbook' => $totalLogbook,
                'avg_grade' => $avgGrade ? round($avgGrade, 2) : 0,
                'final_grade' => $finalGrade
            ];
        }

        return $data;
    }
}