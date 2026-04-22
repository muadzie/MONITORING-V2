<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Placement;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;

class PlacementController extends Controller
{
    public function index()
    {
        $placements = Placement::with(['student', 'company'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($placements);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,canceled',
            'notes' => 'nullable|string',
        ]);

        // Cek apakah siswa sudah memiliki penempatan aktif
        $existingPlacement = Placement::where('student_id', $request->student_id)
            ->where('status', 'active')
            ->first();
            
        if ($existingPlacement) {
            return response()->json([
                'message' => 'Siswa sudah memiliki penempatan aktif'
            ], 400);
        }

        $placement = Placement::create($request->all());
        
        // Update company_id di user
        User::where('id', $request->student_id)->update([
            'company_id' => $request->company_id
        ]);

        return response()->json($placement->load(['student', 'company']), 201);
    }

    public function show($id)
    {
        $placement = Placement::with(['student', 'company'])->findOrFail($id);
        return response()->json($placement);
    }

    public function update(Request $request, $id)
    {
        $placement = Placement::findOrFail($id);
        
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,canceled',
            'notes' => 'nullable|string',
        ]);

        $placement->update($request->all());
        
        // Update company_id di user
        if ($placement->status === 'active') {
            User::where('id', $placement->student_id)->update([
                'company_id' => $placement->company_id
            ]);
        } else {
            User::where('id', $placement->student_id)->update([
                'company_id' => null
            ]);
        }

        return response()->json($placement->load(['student', 'company']));
    }

    public function destroy($id)
    {
        $placement = Placement::findOrFail($id);
        
        // Reset company_id user
        User::where('id', $placement->student_id)->update([
            'company_id' => null
        ]);
        
        $placement->delete();
        
        return response()->json(['message' => 'Placement deleted']);
    }
    
    public function getAvailableStudents()
    {
        $placedStudentIds = Placement::where('status', 'active')->pluck('student_id');
        
        $students = User::where('role_id', 2)
            ->whereNotIn('id', $placedStudentIds)
            ->select('id', 'name', 'nisn', 'class_id')
            ->with('class')
            ->get();
        
        return response()->json($students);
    }
}