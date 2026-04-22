<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\User;
use Illuminate\Http\Request;

class ClassController extends Controller
{
    public function index()
    {
        $classes = Classes::with('teacher')->withCount('students')->get();
        return response()->json($classes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'academic_year' => 'nullable|string',
        ]);

        $class = Classes::create($request->all());
        $class->load('teacher');
        
        return response()->json($class, 201);
    }

    public function show($id)
    {
        $class = Classes::with('teacher', 'students')->findOrFail($id);
        return response()->json($class);
    }

    public function update(Request $request, $id)
    {
        $class = Classes::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:users,id',
            'academic_year' => 'nullable|string',
        ]);
        
        $class->update($request->all());
        $class->load('teacher');
        
        return response()->json($class);
    }

    public function destroy($id)
    {
        $class = Classes::findOrFail($id);
        
        // Cek apakah kelas masih memiliki siswa
        if ($class->students()->count() > 0) {
            return response()->json([
                'message' => 'Kelas tidak dapat dihapus karena masih memiliki siswa'
            ], 400);
        }
        
        $class->delete();
        return response()->json(['message' => 'Class deleted']);
    }
}