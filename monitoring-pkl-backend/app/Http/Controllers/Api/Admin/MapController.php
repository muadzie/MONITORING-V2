<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Placement;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Get all company locations with student counts
     */
    public function getCompanyLocations()
    {
        // Ambil semua perusahaan dengan data lengkap
        $companies = Company::select('id', 'name', 'address', 'latitude', 'longitude', 'radius')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();
        
        // Format response dengan company_name
        $result = [];
        foreach ($companies as $company) {
            // Ambil siswa magang aktif
            $placements = Placement::with('student.class')
                ->where('company_id', $company->id)
                ->where('status', 'active')
                ->get();
            
            $studentsList = $placements->map(function ($p) {
                return [
                    'id' => $p->student->id,
                    'name' => $p->student->name,
                    'nisn' => $p->student->nisn,
                    'kelas' => $p->student->kelas ?? ($p->student->class ? $p->student->class->name : null),
                    'jurusan' => $p->student->jurusan,
                ];
            })->values()->toArray();
            
            $result[] = [
                'id' => $company->id,
                'company_name' => $company->name,
                'name' => $company->name,
                'address' => $company->address,
                'latitude' => (float) $company->latitude,
                'longitude' => (float) $company->longitude,
                'radius' => $company->radius,
                'students_count' => count($studentsList),
                'students' => $studentsList,
            ];
        }
        
        return response()->json($result);
    }
    
    /**
     * Get detailed map data with placements
     */
    public function getMapData()
    {
        $placements = Placement::with(['student', 'company'])
            ->where('status', 'active')
            ->get();
        
        $markers = [];
        foreach ($placements as $placement) {
            if ($placement->company && $placement->company->latitude && $placement->company->longitude) {
                $markers[] = [
                    'id' => $placement->id,
                    'student_name' => $placement->student->name,
                    'company_name' => $placement->company->name,
                    'latitude' => (float) $placement->company->latitude,
                    'longitude' => (float) $placement->company->longitude,
                    'radius' => $placement->company->radius,
                    'address' => $placement->company->address,
                    'students_count' => Placement::where('company_id', $placement->company_id)
                        ->where('status', 'active')
                        ->count(),
                ];
            }
        }
        
        return response()->json([
            'markers' => $markers,
            'center' => [
                'lat' => -6.5696,
                'lng' => 107.7583
            ]
        ]);
    }
}