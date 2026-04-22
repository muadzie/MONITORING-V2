<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AttendanceReportExport;
use App\Exports\LogbookReportExport;
use App\Exports\StudentSummaryExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function attendance(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        $export = new AttendanceReportExport(
            $request->start_date,
            $request->end_date,
            $request->company_id,
            $request->class_id
        );
        
        $filename = 'Laporan_Absensi_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download($export, $filename);
    }
    
    public function logbook(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);
        
        $export = new LogbookReportExport(
            $request->start_date,
            $request->end_date,
            $request->student_id
        );
        
        $filename = 'Laporan_Logbook_' . date('Y-m-d_His') . '.xlsx';
        return Excel::download($export, $filename);
    }
    
    public function summary(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020',
        ]);
        
        $export = new StudentSummaryExport($request->month, $request->year);
        
        $filename = 'Rekap_Siswa_Bulan_' . $request->month . '_' . $request->year . '.xlsx';
        return Excel::download($export, $filename);
    }
}