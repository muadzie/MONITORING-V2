<?php

namespace App\Http\Controllers\Api\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $report = Report::where('user_id', $request->user()->id)->latest()->first();
        if ($report) {
            $report->file_url = Storage::url($report->file_path);
        }
        return response()->json($report);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'report' => 'required|file|mimes:pdf,doc,docx|max:10240'
        ]);

        $user = $request->user();
        
        // Hapus laporan lama jika ada
        $oldReport = Report::where('user_id', $user->id)->first();
        if ($oldReport) {
            Storage::delete($oldReport->file_path);
            $oldReport->delete();
        }

        $file = $request->file('report');
        $path = $file->store('reports', 'public');
        
        $report = Report::create([
            'user_id' => $user->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'status' => 'pending'
        ]);

        $report->file_url = Storage::url($path);
        
        return response()->json($report);
    }

    public function delete(Request $request)
    {
        $report = Report::where('user_id', $request->user()->id)->first();
        if ($report) {
            Storage::delete($report->file_path);
            $report->delete();
        }
        return response()->json(['message' => 'Report deleted']);
    }
}