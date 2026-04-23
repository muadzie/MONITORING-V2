<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Attendance;
use App\Models\Logbook;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentSummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return User::where('role_id', 2)
            ->with(['company', 'class'])
            ->get();
    }

    public function map($student): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        $attendances = Attendance::where('user_id', $student->id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get();
        
        $logbooks = Logbook::where('user_id', $student->id)
            ->whereMonth('date', $this->month)
            ->whereYear('date', $this->year)
            ->get();
        
        $present = $attendances->where('status', 'present')->count();
        $late = $attendances->where('status', 'late')->count();
        $absent = $attendances->where('status', 'absent')->count();
        $sick = $attendances->where('status', 'sick')->count();
        $permit = $attendances->where('status', 'permit')->count();
        $avgGrade = $logbooks->avg('grade') ?? 0;
        
        $totalDays = $attendances->count();
        $attendancePercentage = $totalDays > 0 ? round(($present + $late) / $totalDays * 100, 2) : 0;
        
        return [
            $rowNumber,
            $student->nisn ?? '-',
            $student->name,
            $student->class->name ?? '-',
            $student->company->name ?? '-',
            $present,
            $late,
            $absent,
            $sick,
            $permit,
            $attendancePercentage . '%',
            round($avgGrade, 2),
            $this->getGradeCategory($avgGrade),
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'NISN',
            'NAMA SISWA',
            'KELAS',
            'PERUSAHAAN',
            'HADIR',
            'TERLAMBAT',
            'ALPHA',
            'SAKIT',
            'IZIN',
            'PERSENTASE KEHADIRAN',
            'RATA-RATA NILAI',
            'KATEGORI'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:M1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        
        $sheet->getStyle('A1:M' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
    }

    private function getGradeCategory($grade)
    {
        if ($grade >= 85) return 'Sangat Baik';
        if ($grade >= 75) return 'Baik';
        if ($grade >= 60) return 'Cukup';
        return 'Perlu Bimbingan';
    }
}