<?php

namespace App\Exports;

use App\Models\Logbook;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class LogbookReportExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    protected $startDate;
    protected $endDate;
    protected $studentId;

    public function __construct($startDate, $endDate, $studentId = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->studentId = $studentId;
    }

    public function query()
    {
        $query = Logbook::with('user')
            ->whereBetween('date', [$this->startDate, $this->endDate]);
        
        if ($this->studentId) {
            $query->where('user_id', $this->studentId);
        }
        
        return $query->orderBy('date', 'desc');
    }

    public function map($logbook): array
    {
        static $rowNumber = 0;
        $rowNumber++;
        
        return [
            $rowNumber,
            $logbook->user->nisn ?? '-',
            $logbook->user->name,
            $logbook->date,
            $logbook->activity,
            $logbook->description,
            $this->getStatusText($logbook->status),
            $logbook->grade ?? '-',
            $logbook->feedback ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'NO',
            'NISN',
            'NAMA SISWA',
            'TANGGAL',
            'AKTIVITAS',
            'DESKRIPSI',
            'STATUS',
            'NILAI',
            'FEEDBACK'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:I1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        
        $sheet->getStyle('A1:I' . ($sheet->getHighestRow()))->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]],
        ]);
    }

    public function title(): string
    {
        return 'Laporan Logbook';
    }

    private function getStatusText($status)
    {
        $map = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'];
        return $map[$status] ?? $status;
    }
}