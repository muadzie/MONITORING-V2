<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'description' => 'nullable|string'
        ]);

        $role = Role::create($request->all());
        return response()->json($role, 201);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $id,
            'description' => 'nullable|string'
        ]);

        $role->update($request->all());
        return response()->json($role);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Cek apakah role masih digunakan
        if ($role->users()->count() > 0) {
            return response()->json([
                'message' => 'Role cannot be deleted because it has users'
            ], 400);
        }
        
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }
}