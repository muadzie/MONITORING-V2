<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index()
    {
        $teachers = User::where('role_id', 3)
            ->select('id', 'name', 'email', 'nip', 'phone', 'mata_pelajaran', 'created_at')
            ->withCount('students')
            ->get();
        
        return response()->json($teachers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'nip' => 'nullable|string|unique:users,nip',
            'phone' => 'nullable|string',
            'mata_pelajaran' => 'nullable|string',
        ]);

        $teacher = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'nip' => $request->nip,
            'phone' => $request->phone,
            'mata_pelajaran' => $request->mata_pelajaran,
            'role_id' => 3,
        ]);

        return response()->json($teacher, 201);
    }

    public function update(Request $request, $id)
    {
        $teacher = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'nip' => 'nullable|string|unique:users,nip,' . $id,
            'phone' => 'nullable|string',
            'mata_pelajaran' => 'nullable|string',
        ]);

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'nip' => $request->nip,
            'phone' => $request->phone,
            'mata_pelajaran' => $request->mata_pelajaran,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $teacher->update($updateData);

        return response()->json($teacher);
    }

    public function destroy($id)
    {
        $teacher = User::findOrFail($id);
        
        // Cek apakah guru memiliki siswa bimbingan
        if ($teacher->students()->count() > 0) {
            return response()->json([
                'message' => 'Guru tidak dapat dihapus karena masih memiliki siswa bimbingan'
            ], 400);
        }
        
        $teacher->delete();
        return response()->json(['message' => 'Teacher deleted']);
    }
}