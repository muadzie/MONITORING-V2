<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Logbook;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function downloadAttendanceReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'student_id' => 'nullable|exists:users,id',
            'company_id' => 'nullable|exists:companies,id'
        ]);

        $query = Attendance::with(['user', 'company'])
            ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($request->student_id) {
            $query->where('user_id', $request->student_id);
        }

        if ($request->company_id) {
            $query->where('company_id', $request->company_id);
        }

        $attendances = $query->orderBy('date')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = ['NO', 'NISN', 'NAMA', 'TEMPAT PERUSAHAAN', 'TANGGAL', 'CHECK IN', 'CHECK OUT', 'KETERANGAN', 'GRADE'];
        
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        $row = 2;
        foreach ($attendances as $index => $attendance) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $attendance->user->nisn);
            $sheet->setCellValue('C' . $row, $attendance->user->name);
            $sheet->setCellValue('D' . $row, $attendance->company->name);
            $sheet->setCellValue('E' . $row, $attendance->date);
            $sheet->setCellValue('F' . $row, $attendance->check_in);
            $sheet->setCellValue('G' . $row, $attendance->check_out);
            
            $status = '';
            switch ($attendance->status) {
                case 'present': $status = 'Hadir'; break;
                case 'late': $status = 'Terlambat'; break;
                case 'absent': $status = 'Alpha'; break;
                case 'sick': $status = 'Sakit'; break;
                case 'permit': $status = 'Izin'; break;
            }
            $sheet->setCellValue('H' . $row, $status);
            
            // Grade calculation based on attendance
            $grade = $this->calculateGrade($attendance);
            $sheet->setCellValue('I' . $row, $grade);
            
            $row++;
        }

        // Style the header
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'font' => ['color' => ['rgb' => 'FFFFFF']]
        ];
        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);
        
        // Auto-size columns
        foreach(range('A','I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'attendance_report_' . date('Y-m-d_His') . '.xlsx';
        $filepath = storage_path('app/public/' . $filename);
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    public function downloadLogbookReport(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'student_id' => 'nullable|exists:users,id'
        ]);

        $query = Logbook::with('user')
            ->whereBetween('date', [$request->start_date, $request->end_date]);

        if ($request->student_id) {
            $query->where('user_id', $request->student_id);
        }

        $logbooks = $query->orderBy('date')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['NO', 'NISN', 'NAMA SISWA', 'TANGGAL', 'AKTIVITAS', 'DESKRIPSI', 'STATUS', 'NILAI', 'FEEDBACK'];
        
        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . '1', $header);
        }

        $row = 2;
        foreach ($logbooks as $index => $logbook) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $logbook->user->nisn);
            $sheet->setCellValue('C' . $row, $logbook->user->name);
            $sheet->setCellValue('D' . $row, $logbook->date);
            $sheet->setCellValue('E' . $row, $logbook->activity);
            $sheet->setCellValue('F' . $row, $logbook->description);
            $sheet->setCellValue('G' . $row, $logbook->status);
            $sheet->setCellValue('H' . $row, $logbook->grade ?? '-');
            $sheet->setCellValue('I' . $row, $logbook->feedback ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'logbook_report_' . date('Y-m-d_His') . '.xlsx';
        $filepath = storage_path('app/public/' . $filename);
        $writer->save($filepath);

        return response()->download($filepath)->deleteFileAfterSend(true);
    }

    private function calculateGrade($attendance)
    {
        // Simple grade calculation logic
        if ($attendance->status == 'present') return 100;
        if ($attendance->status == 'late') return 80;
        if ($attendance->status == 'sick') return 70;
        if ($attendance->status == 'permit') return 60;
        return 0;
    }
}