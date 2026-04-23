<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
   public function index()
{
    $students = User::where('role_id', 2)
        ->with(['company', 'class'])  // Tambahkan 'class'
        ->select('id', 'name', 'email', 'nisn', 'class_id', 'phone', 'company_id', 'created_at')
        ->get();
    
    return response()->json($students);
}

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'phone' => 'nullable|string',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $student = User::create([
            'nisn' => $request->nisn,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
            'role_id' => 2,
        ]);

        return response()->json($student, 201);
    }

    public function update(Request $request, $id)
    {
        $student = User::findOrFail($id);
        
        $request->validate([
            'nisn' => 'required|string|unique:users,nisn,' . $id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'kelas' => 'nullable|string',
            'jurusan' => 'nullable|string',
            'phone' => 'nullable|string',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $updateData = [
            'nisn' => $request->nisn,
            'name' => $request->name,
            'email' => $request->email,
            'kelas' => $request->kelas,
            'jurusan' => $request->jurusan,
            'phone' => $request->phone,
            'company_id' => $request->company_id,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $student->update($updateData);

        return response()->json($student);
    }

    public function destroy($id)
    {
        $student = User::findOrFail($id);
        $student->delete();
        return response()->json(['message' => 'Student deleted']);
    }
}