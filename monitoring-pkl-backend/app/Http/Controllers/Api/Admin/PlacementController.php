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
        $placements = Placement::with(['student', 'company', 'teacher'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json($placements);
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'company_id' => 'required|exists:companies,id',
            'teacher_id' => 'required|exists:users,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,completed,canceled',
            'notes' => 'nullable|string',
            'shift_start' => 'nullable|string|max:5',
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

        $placement = Placement::create([
            'student_id' => $request->student_id,
            'company_id' => $request->company_id,
            'teacher_id' => $request->teacher_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->status,
            'notes' => $request->notes,
            'shift_start' => $request->shift_start,
        ]);
        
        // Update company_id dan teacher_id di user
        User::where('id', $request->student_id)->update([
            'company_id' => $request->company_id,
            'teacher_id' => $request->teacher_id
        ]);

        return response()->json($placement->load(['student', 'company', 'teacher']), 201);
    }

    public function show($id)
    {
        $placement = Placement::with(['student', 'company', 'teacher'])->findOrFail($id);
        return response()->json($placement);
    }

    public function update(Request $request, $id)
    {
        $placement = Placement::findOrFail($id);
        
        $request->validate([
            'company_id' => 'exists:companies,id',
            'teacher_id' => 'exists:users,id',
            'start_date' => 'date',
            'end_date' => 'date|after:start_date',
            'status' => 'in:active,completed,canceled',
            'notes' => 'nullable|string',
        ]);

        $updateData = [];
        
        if ($request->has('company_id')) {
            $updateData['company_id'] = $request->company_id;
        }
        if ($request->has('teacher_id')) {
            $updateData['teacher_id'] = $request->teacher_id;
        }
        if ($request->has('start_date')) {
            $updateData['start_date'] = $request->start_date;
        }
        if ($request->has('end_date')) {
            $updateData['end_date'] = $request->end_date;
        }
        if ($request->has('status')) {
            $updateData['status'] = $request->status;
        }
        if ($request->has('notes')) {
            $updateData['notes'] = $request->notes;
        }
        if ($request->has('shift_start')) {
            $updateData['shift_start'] = $request->shift_start;
        }
        
        $placement->update($updateData);
        
        // Update company_id dan teacher_id di user
        $userUpdate = [];
        if ($request->has('company_id')) {
            $userUpdate['company_id'] = $request->company_id;
        }
        if ($request->has('teacher_id')) {
            $userUpdate['teacher_id'] = $request->teacher_id;
        }
        
        if (!empty($userUpdate)) {
            User::where('id', $placement->student_id)->update($userUpdate);
        }

        return response()->json($placement->load(['student', 'company', 'teacher']));
    }

    public function destroy($id)
    {
        $placement = Placement::findOrFail($id);
        
        // Reset company_id dan teacher_id user
        User::where('id', $placement->student_id)->update([
            'company_id' => null,
            'teacher_id' => null
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