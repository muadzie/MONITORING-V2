<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class AttendanceReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $companyId;
    protected $classId;

    public function __construct($startDate, $endDate, $companyId = null, $classId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->companyId = $companyId;
        $this->classId = $classId;
    }

    public function query()
    {
        $query = Attendance::with(['user', 'user.class', 'company'])
            ->whereBetween('date', [$this->startDate, $this->endDate]);
        
        if ($this->companyId) {
            $query->where('company_id', $this->companyId);
        }
        
        if ($this->classId) {
            $query->whereHas('user', function($q) {
                $q->where('class_id', $this->classId);
            });
        }
        
        return $query->orderBy('date', 'desc');
    }

    public function map($attendance): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $attendance->user->nisn ?? '-',
            $attendance->user->name,
            $attendance->user->class->name ?? '-',
            $attendance->company->name,
            $attendance->date,
            $attendance->check_in ?? '-',
            $attendance->check_out ?? '-',
            $this->getStatusText($attendance->status),
            $this->calculateGrade($attendance),
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
            'TANGGAL',
            'CHECK IN',
            'CHECK OUT',
            'STATUS',
            'NILAI'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:J1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        $sheet->getStyle('A1:J1')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        $sheet->getStyle('A2:J' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        // Set lebar kolom
        $sheet->getColumnDimension('A')->setWidth(5);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(25);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(12);
        $sheet->getColumnDimension('I')->setWidth(15);
        $sheet->getColumnDimension('J')->setWidth(10);
    }

    public function title(): string
    {
        return 'Laporan Absensi';
    }

    private function getStatusText($status)
    {
        $map = ['present' => 'Hadir', 'late' => 'Terlambat', 'absent' => 'Alpha', 'sick' => 'Sakit', 'permit' => 'Izin'];
        return $map[$status] ?? $status;
    }

    private function calculateGrade($attendance)
    {
        if ($attendance->status == 'present') return 100;
        if ($attendance->status == 'late') return 80;
        if ($attendance->status == 'sick') return 70;
        if ($attendance->status == 'permit') return 60;
        return 0;
    }
}